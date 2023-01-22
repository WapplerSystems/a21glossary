<?php

namespace WapplerSystems\A21glossary;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Ronny Vorpahl (vorpahl@artplan21.de)
 *  (c) 2003 Andreas Schwarzkopf (schwarzkopf@artplan21 de)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Html\HtmlParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * a21glossary: all found words in content wich correspond with the glossary entries
 * will be enriched with special markup and/or with links to the glossary details page
 *
 * @author    Sven Wappler <typo3YYYY@wappler.systems>
 * @author    Ronny Vorpahl <vorpahl@artplan21.de>
 */
class Processor
{

    protected $search = [];
    protected $searchGlobal = [];
    protected $count = [];
    protected $used = [];
    protected $searchMarkers = [];
    protected $replaceMarkers = [];
    protected $searchMarkers2 = [];
    protected $replaceMarkers2 = [];
    protected $piVars = [];
    protected $depths = [];
    protected $replaceGlobal = [];
    protected $replace = [];

    /** @var int */
    protected $pageId = 0;

    /** @var TypoScriptFrontendController */
    protected $tsFeController;

    protected $config = [];

    public function __construct(array $overrideConf = [])
    {

        $this->tsFeController = $GLOBALS['TSFE'];

        $this->pageId = $GLOBALS['TSFE']->id;

        $this->config['glossaryWHAT'] = 'ALL';
    }


    /**
     * AddSlash array
     * This function traverses a multidimensional array and adds slashes to the values.
     * NOTE that the input array is and argument by reference.!!
     * Twin-function to stripSlashesOnArray
     *
     * @param array $theArray Multidimensional input array, (REFERENCE!)
     */
    public static function addSlashesOnArray(array &$theArray)
    {
        foreach ($theArray as &$value) {
            if (\is_array($value)) {
                self::addSlashesOnArray($value);
            } else {
                $value = addslashes($value);
            }
        }
        unset($value);
        reset($theArray);
    }


    /**
     * this is the actual main function that replaces the glossary
     * words with the explanations
     *
     * @param string $content the content that should be parsed
     * @param array $config
     * @return string the modified content
     * @throws \InvalidArgumentException
     */
    public function main($content, $config = [])
    {

        $this->config = array_merge($this->config, $config);

        // return right now if the wrong page type was chosen
        if (!isset($this->config['typeList'])) {
            /** to preserve old behavior we need to differ between emptystring (default TypoScript) which is used in the
             * lines below and null (no TypoScript at all included). Due to ('' !== $typeList) null was never regarded
             * (which is correct), thus we need to check early, instead of relax the check
             */
            return $content;
        }
        $typeList = $this->config['typeList'];
        $typeList = ('' !== $typeList) ? $typeList : '0';
        $typeList = @explode(',', $typeList);

        if (!\in_array((int)GeneralUtility::_GP('type'), $typeList)) {
            return $content;
        }

        // load the whole configuration
        $language = $this->tsFeController->getLanguage();
        $renderCharset = 'utf-8';

        // extract and escape get-vars
        if (GeneralUtility::_GP('tx_a21glossary')) {
            $this->piVars = GeneralUtility::_GP('tx_a21glossary');
            if (\count($this->piVars)) {
                self::addSlashesOnArray($this->piVars);
            }
        }

        // for the stats
        for ($i = -1; $i <= 15; $i++) {
            $this->depths[$i] = 0;
        }

        if (isset($this->config['noglossary']) || ($this->piVars['disable'] ?? null)) {
            return $content;
        }

        if (GeneralUtility::inList($this->config['excludePages'], $this->pageId)) {
            if ($this->config['excludePages'][$this->pageId]) {
                // disable glossary only for certain glossary types
                $this->config['excludeTypes'] .= ',' . $this->config['excludePages'][$this->pageId];
            } else {
                // disable glossary completly on current page: stop glossary rendering immediately
                return $content;
            }
        }

        $items = $this->fetchGlossaryItems($this->config['pidList']);

        if (!$this->count['used']) {
            return $content;
        }

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        // sort entries from z-a to replace more special words with the same beginnng first eg. aminoacid before amino
        krsort($items);

        // move entries with differing shortcut to end of array to prevent double replacement
        foreach ($items as $key => $item) {
            if ('' !== $item['shortcut'] && ($item['shortcut'] != $item['short'])) {
                unset($items[$key]);
                $items[$key] = $item;
            }
        }


        // prepare items
        foreach ($items as $item) {

            if (!GeneralUtility::inList($this->config['excludeTypes'] ?? '', $item['shorttype'])) {

                $cObj->data = $item;

                // set item language
                if ($item['language'] && $language->getTwoLetterIsoCode() != $item['language']) {
                    $lang = (((int)($this->config['noLang'] ?? 0)) ? '' : (' lang="' . $item['language'] . '"'))
                        . (((int)($this->config['xmlLang'] ?? 0)) ? (' xml:lang="' . $item['language'] . '"') : '');
                } else {
                    $lang = '';
                }

                // set item type
                $element = trim(htmlspecialchars(strip_tags($item['shorttype']), ENT_QUOTES, $renderCharset));
                $titleText = trim(htmlspecialchars(strip_tags($item['longversion']), ENT_QUOTES, $renderCharset));
                $title = $item['longversion'] ? (' title="' . $titleText . '"') : '';

                // those can be retrieved later with stdwrap
                $this->tsFeController->register['lang'] = $lang;
                $this->tsFeController->register['title'] = $title;

                // decide replacement linking
                if ($item['force_linking']) {
                    $generateLink = ((int)$item['force_linking'] === 1) ? 1 : 0;

                } elseif (\count($this->config['typolink.']) && GeneralUtility::inList($this->config['linkToGlossary'],
                        $item['shorttype'])) {
                    $generateLink = 1;

                    if ('' !== $this->config['linkOnlyIfNotEmpty']) {
                        $linkOnlyIfNotEmpty = GeneralUtility::trimExplode(',', $this->config['linkOnlyIfNotEmpty']);
                        if (\count($linkOnlyIfNotEmpty)) {
                            foreach ($linkOnlyIfNotEmpty as $checkField) {
                                if ($item[$checkField] == '') {
                                    $generateLink = 0;
                                    break;
                                }
                            }
                        }
                    }

                } else {
                    $generateLink = 0;
                }

                // create and wrap replacement
                // decide to preserve case of the displayed word in the content
                if ($item['force_preservecase']) {
                    $replacement = ((int)$item['force_preservecase'] === 1 ? '$1' : $item['short']);
                } else {
                    $replacement = ((int)$this->config['preserveCase'] ? '$1' : $item['short']);
                }

                $replacement = trim($cObj->stdWrap($replacement, $this->config[$element] ?? []));
                $replacement = ' <' . $element . $lang . $title . '> ' . $replacement . ' </' . $element . '> ';

                if ($generateLink) {
                    $replacement = ' ' . $cObj->typoLink($replacement, $this->config['typolink.'] ?? []) . ' ';
                }

                // set needle
                $needle = preg_quote($item['shortcut'] ? $item['shortcut'] : $item['short'], '/');

                // set needle modifiers
                $PCREmodifiers = $this->config['patternModifiers'];
                if ($item['force_case']) {
                    $caseSensitive = ((int)$item['force_case'] === 1) ? 'i' : '';
                } else {
                    $caseSensitive = $this->config['caseSensitive'] ? '' : 'i';
                }

                // wrap needle regexp
                if ($item['force_regexp'] == 1) {
                    $regExp = '(' . $needle . ')(?![^<>]*?[>])';
                } else {
                    if ($generateLink) {
                        // TODO get nested link recognition working,
                        // feel free to contact us (info@artplan21.de)
                        // if you know how to do better than this
                        $regExp = '(?!<.*?)(?<=\s|[[:punct:]])(' . $needle . ')(?=\s|[[:punct:]])(?![^<>]*?' . '>)';

                    } else {
                        $regExp = '(?!<.*?)(?<=\s|[[:punct:]])(' . $needle . ')(?=\s|[[:punct:]])(?![^<>]*?>)';
                    }
                }
                $regExp = '/' . $regExp . '/' . $caseSensitive . $PCREmodifiers;

                // glossary area override for global replacement
                if ($item['force_global']) {
                    $this->searchGlobal[] = $regExp;
                    $this->replaceGlobal[] = $replacement;
                } else {
                    $this->search[] = $regExp;
                    $this->replace[] = $replacement;
                }
            }
        }

        // split content in head and body
        $dividePos = strpos($content, '<body');
        $head = substr($content, 0, $dividePos);
        $body = substr($content, strpos($content, '<body'));

        // text boundaries fix
        $body = str_replace(['<', '>'], [' <', '> '], $body);

        // prepare include-exclude parts
        $this->searchMarkers = [
            '<!--A21GLOSSARY_begin-->',
            '<!--A21GLOSSARY_end-->',
            '<!--A21GLOSSEX_begin-->',
            '<!--A21GLOSSEX_end-->'
        ];
        $this->replaceMarkers = [
            '<a21glossary>',
            '</a21glossary>',
            '<a21glossex>',
            '</a21glossex>'
        ];

        // add predefined includes
        /* TODO: Remove or improve */
        if ($this->config['glossaryWHAT'] === 'ALL') {
            $this->searchMarkers[] = '<body>';
            $this->replaceMarkers[] = '<body><a21glossary>';

            $this->searchMarkers[] = '</body>';
            $this->replaceMarkers[] = '</a21glossary></body>';
        }

        if ($this->config['glossaryWHAT'] === 'SEARCHTAGS' || (int)($this->config['includeSearchTags'] ?? 0)) {
            $this->searchMarkers[] = '<!--TYPO3SEARCH_begin-->';
            $this->replaceMarkers[] = '<!--TYPO3SEARCH_begin--><a21glossary>';

            $this->searchMarkers[] = '<!--TYPO3SEARCH_end-->';
            $this->replaceMarkers[] = '</a21glossary><!--TYPO3SEARCH_end-->';
        }

        if ('' !== $this->config['excludeTags']) {

            $excludeTags = explode(',', strtolower($this->config['excludeTags']));

            if (count($excludeTags)) {

                // also add uppercase version of each html element
                $excludeTags = array_merge($excludeTags, explode(',', strtoupper($this->config['excludeTags'])));
                $excludeTags = array_unique($excludeTags);

                foreach ($excludeTags as $value) {
                    $patternValue = preg_quote($value, '#');

                    // b is for word boundary
                    $this->searchMarkers2[] = '#<' . $patternValue . '\b#';
                    $this->replaceMarkers2[] = '<a21glossex><' . $patternValue;

                    $this->searchMarkers2[] = '#</' . $patternValue . '>#';
                    $this->replaceMarkers2[] = '</' . $patternValue . '></a21glossex>';
                }
            }
        }


        // count entries
        $this->count['global'] = \count($this->searchGlobal);
        $this->count['local'] = \count($this->search);

        // replace global entries
        if ($this->count['global']) {
            $body = $this->replace($this->searchGlobal, $this->replaceGlobal, $body);
        }

        // replace local entries
        if ($this->count['local']) {

            // set splitmarkers
            $body = str_replace($this->searchMarkers, $this->replaceMarkers, $body);

            // replace local entries by recursive content splitting
            $body = $this->splitAndReplace($body);

            // final marker handling
            if ($this->config['keepGlossaryMarkers']) {

                $body = str_replace(
                    ['<a21glossary>', '</a21glossary>', '<a21glossex>', '</a21glossex>'],
                    [
                        '<!--A21GLOSSARY_begin-->',
                        '<!--A21GLOSSARY_end-->',
                        '<!--A21GLOSSEX_begin-->',
                        '<!--A21GLOSSEX_end-->'
                    ],
                    $body
                );

            } else {

                $body = str_replace(
                    ['<a21glossary>', '</a21glossary>', '<a21glossex>', '</a21glossex>'],
                    '',
                    $body
                );
            }
        }

        // undo text boundaries fix
        $body = str_replace([' <', '> '], ['<', '>'], $body);

        return $head . $body;
    }


    /**
     * function that does the DB calls to fetch all glossary entries
     *
     * @param string $pidList idlists set by configuraion
     * @return array glossary items
     */
    protected function fetchGlossaryItems($pidList): array
    {
        // -1 means: ignore pids
        if ('' === trim($pidList)) {
            $pidList = '-1';
        }

        // fetch glossary items
        $aPidList = GeneralUtility::intExplode(',', $pidList);
        $language = $this->tsFeController->getLanguage();
        $languageUid = $language->getLanguageId();

        $items = [];

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_a21glossary_main');

        $query = $connection->createQueryBuilder()->select('*')->from('tx_a21glossary_main');
        $query->where(
            $query->expr()->in('pid', $aPidList),
            $query->expr()->in('sys_language_uid', [-1, $languageUid])
        )->orderBy('short', 'asc');

        if ($statement = $query->execute()) {
            foreach ($statement as $row) {
                $row['shortcut'] = trim($row['shortcut']);
                $row['short'] = trim($row['short']);
                $items[$row['shortcut'] ? $row['shortcut'] : $row['short']] = $row;
                if (!isset($this->count['found'])) {
                    $this->count['found'] = 0;
                }
                $this->count['found']++;
            }
        }
        $this->count['used'] = \count($items);
        return $items;
    }


    /**
     * wrapper function for preg_replace
     *
     * @param array $search the search expressions
     * @param array $replace the replacement strings
     * @param string $content
     * @return string the result count
     */
    protected function replace($search = [], $replace = [], $content = '')
    {

        $content = preg_replace($search, $replace, $content, -1, $counter);
        if (!isset($this->count['replaced'])) {
            $this->count['replaced'] = 0;
        }
        $this->count['replaced'] += $counter;
        return $content;
    }


    /**
     * splits the content recursivly into replaceable and unreplaceable parts
     *
     * @param $content
     * @param int $glossaryOn
     * @param int $tagsExcluded
     * @param int $depth
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function splitAndReplace($content, $glossaryOn = 0, $tagsExcluded = 0, $depth = 0): string
    {

        // infinite failsafe
        if ($depth >= 15) {
            $this->depths[-1]++;
            return $content;
        }

        // split
        $contentSplit = GeneralUtility::makeInstance(HtmlParser::class)->splitIntoBlock('a21glossary,a21glossex', $content);

        // content is splittable
        if (\count($contentSplit) > 1) {

            $result = '';
            foreach ($contentSplit as $contentSplitValue) {

                // replaceable part
                if (str_starts_with($contentSplitValue, '<a21glossary>') &&
                    str_ends_with($contentSplitValue, '</a21glossary>')) {

                    $result .= '<a21glossary>' . $this->splitAndReplace(substr($contentSplitValue, 13, -14), 1,
                            $tagsExcluded, $depth + 1) . '</a21glossary>';

                    // excluded part
                } elseif (str_starts_with($contentSplitValue, '<a21glossex>') &&
                    str_ends_with($contentSplitValue, '</a21glossex>')) {

                    // change of rules: once excluded, nothing inside may be included again.
                    // $result.= '<a21glossex>'.$this->splitAndReplace(substr($contentSplitValue,12,-13),0,$depth+1).'</a21glossex>';
                    $result .= $contentSplitValue;

                    // unknown part
                } else {

                    $result .= $this->splitAndReplace($contentSplitValue, $glossaryOn, $tagsExcluded, $depth + 1);
                }
            }
            return $result;

            // content seems not splittable
        }
        if ($glossaryOn) {

            // have you already passed this branch once?
            if ($tagsExcluded) {

                $this->depths[$depth]++;
                // content is not splittable - replace it, nonsplittable part and glossary enabled
                return $this->replace($this->search, $this->replace, $content);

            }

            // content maybe splittable if excludetags will be marked, so mark them and go on with recursion
            return $this->splitAndReplace(
                preg_replace(
                    $this->searchMarkers2,
                    $this->replaceMarkers2,
                    $content
                ),
                $glossaryOn,
                1,
                $depth + 1
            );

            // dead end, nonsplittable part and glossary disabled
        }

        return $content;

    }
}
