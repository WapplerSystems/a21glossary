<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_a21glossary'] = WapplerSystems\A21glossary\Hooks\FrontendHook::class . '->processHook';


TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SveWap.A21glossary',
    'Pi1',
    ['Glossary' => 'index,search'],
    ['Glossary' => 'search']
);

