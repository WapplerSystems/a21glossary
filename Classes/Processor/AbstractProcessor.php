<?php

namespace WapplerSystems\A21glossary\Processor;


use InvalidArgumentException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Html\HtmlParser;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 *
 * @author    Sven Wappler <typo3YYYY@wappler.systems>
 */
abstract class AbstractProcessor implements SingletonInterface
{


    /** @var int */
    protected $pageId = 0;

    /** @var TypoScriptFrontendController */
    protected $tsFeController;

    protected $config = [];

    protected $startMarker = '<glossary>';
    protected $endMarker = '</glossary>';

    // prepare include-exclude parts
    protected $searchMarkers = [
        '<!--A21GLOSSARY_begin-->',
        '<!--A21GLOSSARY_end-->',
        '<!--A21GLOSSEX_begin-->',
        '<!--A21GLOSSEX_end-->'
    ];

    protected $replaceMarkers = [
        '<glossary>',
        '</glossary>',
        '<glossary>',
        '</glossary>'
    ];


    public function __construct(array $overrideConf = [])
    {

        $this->tsFeController = $GLOBALS['TSFE'];

        $this->pageId = $this->tsFeController->id;


        $tsConfig = GeneralUtility::makeInstance(ConfigurationManagerInterface::class)
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );

        $config = $tsConfig['plugin.']['tx_a21glossary.']['settings.'] ?? [];
        $config = GeneralUtility::removeDotsFromTS($config);
        ArrayUtility::mergeRecursiveWithOverrule($config, $overrideConf);

        $this->config = $config;
    }


    /**
     * @param $content
     * @return string
     */
    public function process($content)
    {

        if ($this->checkExlusion()) return $content;

        if (count($this->config) === 0) {
            return $content . '<div class="alert alert-danger">A21Glossary Typoscript settings not loaded. Please include template.</div>';
        }

        $content = $this->prepareContent($content);
        $content = $this->prepareMarkers($content);
        if ($content === '') return '';
        $items = $this->prepareGlossaryItems();

        if (count($items) > 0) {
            $content = $this->replaceItemsInContent($items, $content);
        }

        return $content;

        return $this->cleanUp($content);

    }


    protected function prepareMarkers($content)
    {
        /* do not replace if markers are already set */
        if (strpos($content, $this->startMarker) !== false) return $content;

        /* TODO: replace markers recursively */
        $markers = explode(',', $this->config['markers']);
        foreach ($markers as $markerCouple) {
            $aMarkerCouple = explode('|', $markerCouple);
            if (count($aMarkerCouple) !== 2) {
                throw new Exception('marker couple is no a couple (start/end tag)');
            }
            $content = str_replace([$aMarkerCouple[0], $aMarkerCouple[1]], [$aMarkerCouple[0] . $this->startMarker, $this->endMarker . $aMarkerCouple[1]], $content);
        }

        return $content;
    }


    /** set start/end markers  */
    protected function prepareContent($content)
    {
        if ($content === '') return '';
        return $this->startMarker . $content . $this->endMarker;
    }


    /**
     * @return boolean
     */
    protected function checkExlusion()
    {

        // return right now if the wrong page type was chosen
        $typeList = $this->config['typeList'];
        $typeList = ('' !== $typeList) ? $typeList : '0';
        $typeList = @explode(',', $typeList);

        if (!in_array((int)GeneralUtility::_GP('type'), $typeList)) {
            return true;
        }

        // extract and escape get-vars
        if (GeneralUtility::_GP('tx_a21glossary')) {
            $this->piVars = GeneralUtility::_GP('tx_a21glossary');
            if (\count($this->piVars)) {
                self::addSlashesOnArray($this->piVars);
            }
        }

        if (isset($this->config['noglossary'])) {
            return true;
        }

        if (GeneralUtility::inList($this->config['excludePages'], $this->pageId)) {
            if ($this->config['excludePages.'][$this->pageId]) {
                // disable glossary only for certain glossary types
                $this->config['excludeTypes'] .= ',' . $this->config['excludePages.'][$this->pageId];
            } else {
                // disable glossary completely on current page: stop glossary rendering immediately
                return true;
            }
        }

        return false;
    }

    protected function prepareGlossaryItems()
    {

        $items = $this->fetchGlossaryItems($this->config['pidList']);
        $newItems = [];

        $language = $this->tsFeController->getLanguage();

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

            if (!GeneralUtility::inList($this->config['excludeTypes'], $item['shorttype'])) {

                $cObj->data = $item;

                // set item language
                if ($item['language'] && $language->getTwoLetterIsoCode() !== $item['language']) {
                    $lang = ((int)$this->config['noLang'] ? '' : (' lang="' . $item['language'] . '"'))
                        . ((int)$this->config['xmlLang'] ? (' xml:lang="' . $item['language'] . '"') : '');
                } else {
                    $lang = '';
                }

                // set item type
                $element = trim(htmlspecialchars(strip_tags($item['shorttype']), ENT_QUOTES));
                $titleText = trim(htmlspecialchars(strip_tags($item['longversion']), ENT_QUOTES));
                $title = $item['longversion'] ? (' title="' . $titleText . '"') : '';

                // those can be retrieved later with stdwrap
                $GLOBALS['TSFE']->register['lang'] = $lang;
                $GLOBALS['TSFE']->register['title'] = $title;

                // decide replacement linking
                if ($item['force_linking']) {
                    $generateLink = (boolean)$item['force_linking'];

                } elseif (\count($this->config['typolink.']) && GeneralUtility::inList($this->config['linkToGlossary'],
                        $item['shorttype'])) {
                    $generateLink = true;

                    if ('' !== $this->config['linkOnlyIfNotEmpty']) {
                        $linkOnlyIfNotEmpty = GeneralUtility::trimExplode(',', $this->config['linkOnlyIfNotEmpty']);
                        if (\count($linkOnlyIfNotEmpty)) {
                            foreach ($linkOnlyIfNotEmpty as $checkField) {
                                if ($item[$checkField] === '') {
                                    $generateLink = false;
                                    break;
                                }
                            }
                        }
                    }

                } else {
                    $generateLink = false;
                }

                // create and wrap replacement
                // decide to preserve case of the displayed word in the content
                if ($item['force_preservecase']) {
                    $replacement = ((int)$item['force_preservecase'] === 1 ? '$1' : $item['short']);
                } else {
                    $replacement = ((int)$this->config['preserveCase'] ? '$1' : $item['short']);
                }

                $replacement = trim($cObj->stdWrap($replacement, $this->config[$element . '.']));
                $replacement = ' <' . $element . $lang . $title . '> ' . $replacement . ' </' . $element . '> ';

                if ($generateLink) {
                    $replacement = ' ' . $cObj->typoLink($replacement, $this->config['typolink.']) . ' ';
                }
                $item['replacement'] = $replacement;


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
                        $regExp = '(?!<.*?)(?<=\s|[[:punct:]])(' . $needle . ')(?=\s|[[:punct:]])(?![^<>]*?' . '>)';

                    } else {
                        $regExp = '(?!<.*?)(?<=\s|[[:punct:]])(' . $needle . ')(?=\s|[[:punct:]])(?![^<>]*?>)';
                    }
                }
                $item['regExp'] = '/' . $regExp . '/' . $caseSensitive . $PCREmodifiers;

                $newItems[] = $item;
            }
        }

        return $newItems;
    }

    protected function replaceItemsInContent($items, $content)
    {

        /* filter exclude tags */
        if ('' !== $this->config['excludeTags']) {

            $excludeTags = explode(',', strtolower($this->config['excludeTags']));

            if (count($excludeTags)) {

                // also add uppercase version of each html element
                $excludeTags = array_merge($excludeTags, explode(',', strtoupper($this->config['excludeTags'])));
                $excludeTags = array_unique($excludeTags);


            }

            foreach ($excludeTags as $excludeTag) {

                $content = str_replace(
                    ['<' . $excludeTag . '>', '<' . $excludeTag . ' ', '</' . $excludeTag . '>'],
                    [$this->endMarker . '<' . $excludeTag . '>', $this->endMarker . '<' . $excludeTag . ' ', '</' . $excludeTag . '>' . $this->startMarker],
                    $content);

            }

        }


        foreach ($items as $item) {
            //$content = preg_replace($item['regExp'],$item['replacement'], $content);

        }


        $countStartMarkers = substr_count($content,$this->startMarker);

        $length = count($content);
        for ($i = 0; $i < $length; $i++) {

        }


        return $content;
    }


    /**
     * @param $content
     * @return string
     */
    protected function cleanUp($content)
    {

        $content = str_replace(
            ['<a21glossary>', '</a21glossary>', '<a21glossex>', '</a21glossex>'],
            '',
            $content
        );

        if (!$this->config['keepGlossaryMarkers']) {
            $content = str_replace(
                [
                    '<!--A21GLOSSARY_begin-->',
                    '<!--A21GLOSSARY_end-->',
                    '<!--A21GLOSSEX_begin-->',
                    '<!--A21GLOSSEX_end-->'
                ],
                '',
                $content
            );
        }

        return $content;
    }


    /**
     * function that does the DB calls to fetch all glossary entries
     *
     * @param string $pidList id list set by configuration
     * @return array glossary items
     */
    protected function fetchGlossaryItems($pidList)
    {
        $items = [];
        // -1 means: ignore pids
        if ('' === trim($pidList)) {
            $pidList = '-1';
        }

        // fetch glossary items
        $aPidList = GeneralUtility::intExplode(',', $pidList);

        $language = $this->tsFeController->getLanguage();
        $languageUid = $language->getLanguageId();

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
            }
        }
        return $items;
    }


    /**
     * splits the content recursivly into replaceable and unreplaceable parts
     *
     * @param $content
     * @param int $glossaryOn
     * @param int $tagsExcluded
     * @param int $depth
     * @return string
     * @throws InvalidArgumentException
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
            if (is_array($value)) {
                self::addSlashesOnArray($value);
            } else {
                $value = addslashes($value);
            }
        }
        unset($value);
        reset($theArray);
    }


}
