<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'a21glossary',
    'Pi1',
    'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:plugins.pi1.title',
    'tx-a21glossary',
    'plugins',
    'Show glossary entries'
);


ExtensionManagementUtility::addToInsertRecords('tx_a21glossary_main');
