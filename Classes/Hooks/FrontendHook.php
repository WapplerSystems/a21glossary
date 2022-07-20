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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use WapplerSystems\A21glossary\Processor;

/**
 * a21glossary: all found words in content wich correspond with the glossary entries
 * will be enriched with special markup and/or with links to the glossary details page
 *
 * @author    Sven Wappler <typo3YYYY@wappler.systems>
 * @author    Ronny Vorpahl <vorpahl@artplan21.de>
 */
class FrontendHook
{


    /**
     * function call to apply to the totally rendered page (with non-caching
     * objects) hook the alternative is the userfunc below
     *
     * @param array $params
     * @param TypoScriptFrontendController $pObj the parent object, in this case the TSFE global object
     * @return void
     * @throws \InvalidArgumentException
     */
    public function processHook($params, TypoScriptFrontendController $pObj)
    {
        $conf = $GLOBALS['TSFE']->config['config']['tx_a21glossary.'] ?? null;
        if ($conf === null) {
            return;
        }
        $conf = GeneralUtility::removeDotsFromTS($conf);

        /** @var Processor $processor */
        $processor = GeneralUtility::makeInstance(Processor::class);
        $pObj->content = $processor->main($pObj->content,$conf);
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


}
