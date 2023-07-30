<?php

use WapplerSystems\A21glossary\Controller\GlossaryController;

defined('TYPO3') || die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_a21glossary'] = WapplerSystems\A21glossary\Hooks\FrontendHook::class . '->processHook';

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'a21glossary',
    'Pi1',
    [GlossaryController::class => 'index,search,show'],
    [GlossaryController::class => 'search']
);
