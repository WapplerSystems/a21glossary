<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/', 'A21 Glossary');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main', 'a21glossary'));
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_a21glossary_main');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_a21glossary_main');



