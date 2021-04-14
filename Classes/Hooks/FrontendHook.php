<?php

namespace WapplerSystems\A21glossary\Hooks;

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
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * a21glossary: all found words in content wich correspond with the glossary entries
 * will be enriched with special markup and/or with links to the glossary details page
 *
 * @author    Sven Wappler <typo3YYYY@wappler.systems>
 * @author    Ronny Vorpahl <vorpahl@artplan21.de>
 */
class FrontendHook
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

    /** @var TypoScriptFrontendController */
    protected $pObj = null;

    /**
     * function call to apply to the totally rendered page (with non-caching
     * objects) hook the alternative is the userfunc below
     *
     * @param string $content the full HTML content to output as object
     * @param TypoScriptFrontendController $pObj the parent object, in this case the TSFE global object
     * @return void
     * @throws \InvalidArgumentException
     */
    public function processHook(&$content, TypoScriptFrontendController $pObj)
    {
        $this->pObj = $pObj;

        $conf = $GLOBALS['TSFE']->config['config']['tx_a21glossary.'];
        $pObj->content = $this->main($pObj->content, $conf);
    }

    /**
     * function call to do the processing via a user function
     * the alternative is to apply it to the whole HTML content (see above)
     *
     * @param string $content the HTML content to output
     * @param array $conf the configuration array of the user function
     * @return string the modified HTML content to output
     */
    public function processUserFunc($content, $conf)
    {
        if (empty($conf)) {
            $conf = $GLOBALS['TSFE']->config['config']['tx_a21glossary.'];
        }
        return $this->main($content, $conf);
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
     * @param array $conf the configuration for the parsing
     * @return string the modified content
     * @throws \InvalidArgumentException
     */
    protected function main($content, array $conf = null)
    {

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        GLOBAL $TSFE;

        // merge with extconf, $conf overrules
	    $extConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
		    \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
	    )->get('a21glossary');

        if (\count($extConf)) {
            $conf = array_merge($extConf, (array)$conf);
        }

        // return right now if the wrong page type was chosen
        $typeList = $TSFE->config['typeList'];
        $typeList = ('' !== $typeList) ? $typeList : '0';
        $typeList = @explode(',', $typeList);

        if (!\in_array((int)GeneralUtility::_GP('type'), $typeList)) {
            return $content;
        }

        // load the whole configuration
        $id = $TSFE->id;
        $pageLang = $TSFE->config['config']['language'] ?: $TSFE->config['config']['htmlTag_langKey'];
        $renderCharset = $TSFE->renderCharset;

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

        if (isset($conf['noglossary']) || $this->piVars['disable']) {
            return $content;
        }

        if (GeneralUtility::inList($conf['excludePages'], $id)) {
            if ($conf['excludePages.'][$id]) {
                // disable glossary only for certain glossary types
                $conf['excludeTypes'] .= ',' . $conf['excludePages.'][$id];
            } else {
                // disable glossary completly on current page: stop glossary rendering immediately
                return $content;
            }
        }

        $items = $this->fetchGlossaryItems($conf['pidList']);

        if (!$this->count['used']) {
            return $content;
        }

        $cObj = $objectManager->get(ContentObjectRenderer::class);

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

            if (!GeneralUtility::inList($conf['excludeTypes'], $item['shorttype'])) {

                $cObj->data = $item;

                // set item language
                if ($item['language'] && $pageLang != $item['language']) {
                    $lang = ((int)$conf['noLang'] ? '' : (' lang="' . $item['language'] . '"'))
                        . ((int)$conf['xmlLang'] ? (' xml:lang="' . $item['language'] . '"') : '');
                } else {
                    $lang = '';
                }

                // set item type
                $element = trim(htmlspecialchars(strip_tags($item['shorttype']), ENT_QUOTES, $renderCharset));
                $titleText = trim(htmlspecialchars(strip_tags($item['longversion']), ENT_QUOTES, $renderCharset));
                $title = $item['longversion'] ? (' title="' . $titleText . '"') : '';

                // those can be retrieved later with stdwrap
                $TSFE->register['lang'] = $lang;
                $TSFE->register['title'] = $title;

                // decide replacement linking
                if ($item['force_linking']) {
                    $generateLink = ((int)$item['force_linking'] === 1) ? 1 : 0;

                } elseif (\count($conf['typolink.']) && GeneralUtility::inList($conf['linkToGlossary'],
                        $item['shorttype'])) {
                    $generateLink = 1;

                    if ('' !== $conf['linkOnlyIfNotEmpty']) {
                        $linkOnlyIfNotEmpty = GeneralUtility::trimExplode(',', $conf['linkOnlyIfNotEmpty']);
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
                    $replacement = ((int)$conf['preserveCase'] ? '$1' : $item['short']);
                }

                $replacement = trim($cObj->stdWrap($replacement, $conf[$element . '.']));
                $replacement = ' <' . $element . $lang . $title . '> ' . $replacement . ' </' . $element . '> ';

                if ($generateLink) {
                    $replacement = ' ' . $cObj->typoLink($replacement, $conf['typolink.']) . ' ';
                }

                // set needle
                $needle = preg_quote($item['shortcut'] ? $item['shortcut'] : $item['short'], '/');

                // set needle modifiers
                $PCREmodifiers = $conf['patternModifiers'];
                if ($item['force_case']) {
                    $caseSensitive = ((int)$item['force_case'] === 1) ? 'i' : '';
                } else {
                    $caseSensitive = $conf['caseSensitive'] ? '' : 'i';
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
        $body = str_replace(array('<', '>'), array(' <', '> '), $body);

        // prepare include-exclude parts
        $this->searchMarkers = array(
            '<!--A21GLOSSARY_begin-->',
            '<!--A21GLOSSARY_end-->',
            '<!--A21GLOSSEX_begin-->',
            '<!--A21GLOSSEX_end-->'
        );
        $this->replaceMarkers = array(
            '<a21glossary>',
            '</a21glossary>',
            '<a21glossex>',
            '</a21glossex>'
        );


        // add predefined includes
        /* TODO: Remove or improve */
        if ($conf['glossaryWHAT'] === 'ALL') {
            $this->searchMarkers[] = '<body>';
            $this->replaceMarkers[] = '<body><a21glossary>';

            $this->searchMarkers[] = '</body>';
            $this->replaceMarkers[] = '</a21glossary></body>';
        }

        if ($conf['glossaryWHAT'] === 'SEARCHTAGS' || (int)$conf['includeSearchTags']) {
            $this->searchMarkers[] = '<!--TYPO3SEARCH_begin-->';
            $this->replaceMarkers[] = '<!--TYPO3SEARCH_begin--><a21glossary>';

            $this->searchMarkers[] = '<!--TYPO3SEARCH_end-->';
            $this->replaceMarkers[] = '</a21glossary><!--TYPO3SEARCH_end-->';
        }

        if ('' !== $conf['excludeTags']) {

            $excludeTags = explode(',', strtolower($conf['excludeTags']));

            if (count($excludeTags)) {

                // also add uppercase version of each html element
                $excludeTags = array_merge($excludeTags, explode(',', strtoupper($conf['excludeTags'])));
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
            if ($conf['keepGlossaryMarkers']) {

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
        $body = str_replace(array(' <', '> '), array('<', '>'), $body);

        return $head . $body;
    }


    /**
     * function that does the DB calls to fetch all glossary entries
     *
     * @param string $pidList idlists set by configuraion
     * @return array glossary items
     */
    protected function fetchGlossaryItems($pidList)
    {
        $items = array();
        // -1 means: ignore pids
        if ('' === trim($pidList)) {
            $pidList = '-1';
        }

        // fetch glossary items
        $aPidList = GeneralUtility::intExplode(',', $pidList);
        $languageUid = (int)$GLOBALS['TSFE']->sys_language_uid;

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_a21glossary_domain_model_entry');

        // manual ordering/grouping by pidlist
        foreach ($aPidList as $pid) {
            $query = $connection->createQueryBuilder()->select('*')->from('tx_a21glossary_domain_model_entry');
            $query->where(
                $query->expr()->eq('pid', $query->createNamedParameter($pid, \PDO::PARAM_INT)),
                $query->expr()->in('sys_language_uid', [-1, $languageUid])
            )->orderBy('short', 'asc');

            if ($statement = $query->execute()) {
                foreach ($statement as $row) {
                    $row['shortcut'] = trim($row['shortcut']);
                    $row['short'] = trim($row['short']);
                    $items[$row['shortcut'] ? $row['shortcut'] : $row['short']] = $row;
                }
                $this->count['found'] += $statement->rowCount();
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
                if ((0 === strpos($contentSplitValue, '<a21glossary>')) &&
                    (substr($contentSplitValue, -14) === '</a21glossary>')) {

                    $result .= '<a21glossary>' . $this->splitAndReplace(substr($contentSplitValue, 13, -14), 1,
                            $tagsExcluded, $depth + 1) . '</a21glossary>';

                    // excluded part
                } elseif ((0 === strpos($contentSplitValue, '<a21glossex>')) &&
                    (substr($contentSplitValue, -13) === '</a21glossex>')) {

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
