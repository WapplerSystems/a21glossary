<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin('SveWap.A21glossary', 'Pi1', 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:plugins.pi1.title');
ExtensionManagementUtility::addToInsertRecords('tx_a21glossary_main');
