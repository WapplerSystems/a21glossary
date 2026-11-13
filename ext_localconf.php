<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WapplerSystems\A21glossary\Controller\GlossaryController;


ExtensionUtility::configurePlugin(
    'a21glossary',
    'Pi1',
    [GlossaryController::class => 'index,search,show'],
    [GlossaryController::class => 'search']
);
