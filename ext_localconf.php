<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$extConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
	\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
)->get('a21glossary');

// select method of using glossary
switch (strtoupper($extConf['glossaryWHEN'])) {
    // fast: this hook is called with the cached page containing int-script elements for uncached subparts
    case 'BEFORECACHING':
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_a21glossary'] = WapplerSystems\A21glossary\Hooks\FrontendHook::class . '->processHook';
        break;

    // slow: this one is for the final page containing also the uncached areas
    case 'AFTERCACHING':
	    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['tx_a21glossary'] = WapplerSystems\A21glossary\Hooks\FrontendHook::class . '->processHook';
        break;

    // off: do not invoke the glossary engine automatically
    default:
    case 'OFF';
        break;
}

unset($extConf);

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'WapplerSystems.A21glossary',
    'Pi1',
    [
        'Glossary' => 'index,search'
    ],
    [
        'Glossary' => 'search'
    ]
);
