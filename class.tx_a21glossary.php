<?php
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * a21glossary: all found words in content wich correspond with the glossary entries
 * will be enriched with special markup and/or with links to the glossary details page
 *
 * @author	Ronny Vorpahl <vorpahl@artplan21.de>
 */

class tx_a21glossary {

	protected $search = array();
	protected $searchGlobal = array();
	protected $count = array();
	protected $used = array();
	protected $searchMarkers = array();
	protected $replaceMarkers = array();
	protected $searchMarkers2 = array();
	protected $replaceMarkers2 = array();

	/**
	 * function call to apply to the totally rendered page (with non-caching
	 * objects) hook the alternative is the userfunc below
	 *
	 * @param string $content the full HTML content to output as object
	 * @param tslib_fe $pObj the parent object, in this case the TSFE global object
	 * @return void
	 */
	public function processHook(&$content, $pObj) {

		$conf = $GLOBALS['TSFE']->config['config']['tx_a21glossary.'];
		$content['pObj']->content = $this->main($content['pObj']->content, $conf);
	}

	/**
	 * function call to do the processing via a user function
	 * the alternative is to apply it to the whole HTML content (see above)
	 *
	 * @param string $content the HTML content to output
	 * @param array $conf the configuration array of the user function
	 * @return string the modified HTML content to output
	 */
	public function processUserFunc($content, $conf) {

		if (empty($conf)) {
			$conf = $GLOBALS['TSFE']->config['config']['tx_a21glossary.'];
		}
		return $this->main($content, $conf);
	}


	/**
	 * this is the actual main function that replaces the glossary
	 * words with the explanations
	 *
	 * @param string $content the content that should be parsed
	 * @param array $conf the configuration for the parsing
	 * @return string the modified content
	 */
	protected function main($content, array $conf = NULL) {

		GLOBAL $TSFE;

		$this->time_start = microtime(true);

		// merge with extconf, $conf overrules
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['a21glossary']);
		if(count($extConf)) {
			$conf = array_merge($extConf, (array) $conf);
		}

		// return right now if the wrong page type was chosen
		$typeList = $TSFE->config['typeList'];
		$typeList = strlen($typeList) ? $typeList : '0';
		$typeList = @explode(',', $typeList);

		if (!in_array(intval(t3lib_div::_GP('type')), $typeList)) {
			return $content;
		}

		// load the whole configuration
		$id = $TSFE->id;
		$pageLang = $TSFE->config['config']['language'] ? $TSFE->config['config']['language'] : $TSFE->config['config']['htmlTag_langKey'];
		$renderCharset = $TSFE->renderCharset;

		// extract and escape get-vars
		$this->piVars = t3lib_div::_GP('tx_a21glossary');
		if(count($this->piVars)) {
			t3lib_div::addSlashesOnArray($this->piVars);
		}

		// for the stats
		for ($i = -1; $i<=15; $i++) {
			$this->depths[$i] = 0;
		}

		if (isset($conf['noglossary']) || $this->piVars['disable']) {
			return $content;
		}

		if (t3lib_div::inList($conf['excludePages'], $id)) {
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
		} else {

			$cObj = t3lib_div::makeInstance('tslib_cObj');

			// sort entries from z-a to replace more special words with the same beginnng first eg. aminoacid before amino
			krsort($items);

			// move entries with differing shortcut to end of array to prevent double replacement
			foreach ($items as $key => $item) {
				if (strlen($item['shortcut']) && ($item['shortcut'] != $item['short'])) {
					unset($items[$key]);
					$items[$key] = $item;
				}
			}

			// prepare items
			foreach ($items as $item) {

				if (!t3lib_div::inList($conf['excludeTypes'], $item['shorttype'])) {

					$cObj->data = $item;

					// set item language
					if ($item['language'] && $pageLanguage != $item['language']) {
						$lang  = (intval($conf['noLang']) ? '' : (' lang="'.$item['language'].'"'))
							. (intval($conf['xmlLang']) ? (' xml:lang="'.$item['language'].'"') : '');
					} else {
						$lang = '';
					}

					// set item type
					$element	= trim(htmlspecialchars(strip_tags($item['shorttype']), ENT_QUOTES, $renderCharset));
					$titleText	= trim(htmlspecialchars(strip_tags($item['longversion']), ENT_QUOTES, $renderCharset));
					$title		= $item['longversion'] ? (' title="' . $titleText . '"') : '';

					// those can be retrieved later with stdwrap
					$TSFE->register['lang'] = $lang;
					$TSFE->register['title'] = $title;

					// decide replacement linking
					if ($item['force_linking']) {
						$generateLink = ($item['force_linking'] == 1) ? 1 : 0;

					} elseif (t3lib_div::inList($conf['linkToGlossary'], $item['shorttype']) && count($conf['typolink.'])) {
						$generateLink = 1;

						if (strlen($conf['linkOnlyIfNotEmpty'])) {
							$linkOnlyIfNotEmpty = t3lib_div::trimExplode(',',$conf['linkOnlyIfNotEmpty']);
							if (count($linkOnlyIfNotEmpty)) {
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
						$replacement = ($item['force_preservecase'] == 1 ? "$1" : $item['short']);
					} else {
						$replacement = (intval($conf['preserveCase']) ? "$1" : $item['short']);
					}

					$replacement = trim($cObj->stdWrap($replacement, $conf[$element . '.']));
					$replacement = ' <' . $element . $lang . $title . '> ' . $replacement . ' </' . $element . '> ';

					if ($generateLink) {
						$replacement = ' ' . $cObj->typoLink($replacement, $conf['typolink.']) . ' ';
					}

					// set needle
					$needle = preg_quote($item['shortcut'] ? $item['shortcut'] : $item['short'],'/');

					// set needle modifiers
					$PCREmodifiers = $conf['patternModifiers'];
					if ($item['force_case']) {
						$caseSensitive = ($item['force_case'] == 1) ? 'i' : '';
					} else {
						$caseSensitive = $conf['caseSensitive'] ? '' : 'i';
					}

					// wrap needle regexp
					switch ($item['force_regexp']) {

						// word part
						case 1:
							$regExp = '(' . $needle . ')(?![^<>]*?[>])';
							break;

						// single word
						default:
							if ($generateLink) {
								// TODO get nested link recognition working,
								// feel free to contact us (info@artplan21.de)
								// if you know how to do better than this
								$regExp = '(?!<.*?)(?<=\s|[[:punct:]])(' . $needle . ')(?=\s|[[:punct:]])(?![^<>]*?'.'>)';

							} else {
								$regExp = '(?!<.*?)(?<=\s|[[:punct:]])(' . $needle . ')(?=\s|[[:punct:]])(?![^<>]*?>)';
							}
							break;
					}
					$regExp  = '/' . $regExp . '/' . $caseSensitive . $PCREmodifiers;

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
			$body = str_replace('<', ' <', $body);
			$body = str_replace('>', '> ', $body);

			// prepare include-exclude parts
			$this->searchMarkers  = array(
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
			if ($conf['glossaryWHAT']=='ALL') {
				$this->searchMarkers[] = '<body>';
				$this->replaceMarkers[] = '<body><a21glossary>';

				$this->searchMarkers[] = '</body>';
				$this->replaceMarkers[] = '</a21glossary></body>';
			}

			if ($conf['glossaryWHAT']=='SEARCHTAGS' || intval($conf['includeSearchTags'])) {
				$this->searchMarkers[] = '<!--TYPO3SEARCH_begin-->';
				$this->replaceMarkers[] = '<!--TYPO3SEARCH_begin--><a21glossary>';

				$this->searchMarkers[] = '<!--TYPO3SEARCH_end-->';
				$this->replaceMarkers[] = '</a21glossary><!--TYPO3SEARCH_end-->';
			}

			if (strlen($conf['excludeTags'])) {

				$excludeTags = explode(',', strtolower($conf['excludeTags']));

				if (count($excludeTags)) {

					// also add uppercase version of each html element
					$excludeTags = array_merge($excludeTags,explode(',', strtoupper($conf['excludeTags'])));
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
			$this->count['global'] = count($this->searchGlobal);
			$this->count['local'] = count($this->search);

			// replace global entries
			if ($this->count['global']) {
				$body = $this->replace($this->searchGlobal, $this->replaceGlobal, $body);
			}

			// replace local entries
			if ($this->count['local']) {

				// set splitmarkers
				$body = str_replace($this->searchMarkers, $this->replaceMarkers, $body);

				// replace local entries by recursive content splitting
				$this->parseObj = t3lib_div::makeInstance('t3lib_parsehtml');
				$body = $this->splitAndReplace($body);

				// final marker handling
				if ($conf['keepGlossaryMarkers']) {

					$body = str_replace(
						array('<a21glossary>', '</a21glossary>', '<a21glossex>', '</a21glossex>'),
						array('<!--A21GLOSSARY_begin-->', '<!--A21GLOSSARY_end-->', '<!--A21GLOSSEX_begin-->', '<!--A21GLOSSEX_end-->'),
						$body
					);

				} else {

					$body = str_replace(
						array('<a21glossary>', '</a21glossary>', '<a21glossex>', '</a21glossex>'),
						array('', '', '', ''),
						$body
					);
				}
			}

			// undo text boundaries fix
			$body = str_replace(' <', '<', $body);
			$body = str_replace('> ', '>', $body);

			return $head.$body;
		}
	}


	/**
	 * function that does the DB calls to fetch all glossary entries
	 *
	 * @param string $pidList idlists set by configuraion
	 * @return array glossary items
	 */
	protected function fetchGlossaryItems($pidList) {

		$contentObjectRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');

		// -1 means: ignore pids
		if(!strlen(trim($pidList))) {
			$pidList = -1;
		}

		// fetch glossary items
		$pidList = t3lib_div::intExplode(',', $pidList);
		$languageUid = intval($GLOBALS['TSFE']->sys_language_uid);

		// manual ordering/grouping by pidlist
		foreach ($pidList as $pid) {

			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',						// SELECT
				'tx_a21glossary_main',		// FROM
				'1=1'.						// WHERE
					($pid!=-1 ? ' AND pid=' . $pid : '').
					' AND tx_a21glossary_main.sys_language_uid IN (-1, ' . $languageUid . ')' .
					$contentObjectRenderer->enableFields('tx_a21glossary_main'),
				'',							// GROUP BY
				'short,uid'					// ORDER BY
			);

			if (count($rows)) {
				foreach ($rows as $row) {
					$row['shortcut'] = trim($row['shortcut']);
					$row['short'] = trim($row['short']);
					$items[($row['shortcut'] ? $row['shortcut'] : $row['short'])] = $row;
				}
				$this->count['found'] += count($rows);
			}
		}

		$this->count['used'] = count($items);
		return $items;
	}


	/**
	 * wrapper function for preg_replace
	 *
	 * @param array $search the search expressions
	 * @param array $replace the replacement strings
	 * @param string $source
	 * @return the result count
	 */
	protected function replace($search = array(), $replace = array(), $content = '') {

		$content = preg_replace($search, $replace, $content, -1, $counter);
		$this->count['replaced'] += $counter;
		return $content;
	}


	/**
	 * splits the content recursivly into replaceable and unreplaceable parts
	 *
	 * @param string $body
	 * @param boolean $glossaryOn
	 * @param boolean $tagsExcluded
	 * @param integer $depth
	 * @return string
	 */
	protected function splitAndReplace($content, $glossaryOn = 0, $tagsExcluded = 0, $depth = 0) {

		// infinite failsafe
		if ($depth >= 15) {
			$this->depths[-1]++;
			return $content;
		}

		// split
		$contentSplit = $this->parseObj->splitIntoBlock('a21glossary,a21glossex', $content);

		// content is splittable
		if (count($contentSplit) > 1) {

			$result = '';
			foreach ($contentSplit as $contentSplitValue) {

				// replaceable part
				if ((substr($contentSplitValue, 0, 13) == '<a21glossary>')&&
					(substr($contentSplitValue, -14) == '</a21glossary>')) {

					$result.= '<a21glossary>' . $this->splitAndReplace(substr($contentSplitValue, 13, -14), 1, $tagsExcluded, $depth+1) . '</a21glossary>';

				// excluded part
				} elseif ((substr($contentSplitValue, 0, 12) == '<a21glossex>') &&
					(substr($contentSplitValue, -13) == '</a21glossex>')) {

					// change of rules: once excluded, nothing inside may be included again.
					// $result.= '<a21glossex>'.$this->splitAndReplace(substr($contentSplitValue,12,-13),0,$depth+1).'</a21glossex>';
					$result .= $contentSplitValue;

				// unknown part
				} else {

					$result.= $this->splitAndReplace($contentSplitValue, $glossaryOn, $tagsExcluded, $depth+1);
				}
			}
			return $result;

			// content seems not splittable
		} elseif ($glossaryOn) {

			// have you already passed this branch once?
			if ($tagsExcluded) {

				$this->depths[$depth]++;
				// content is not splittable - replace it, nonsplittable part and glossary enabled
				return $this->replace($this->search, $this->replace, $content);

			} else {

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
			}

			// dead end, nonsplittable part and glossary disabled
		} else {
			return $content;
		}
	}
}

?>