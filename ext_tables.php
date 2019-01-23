<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_a21glossary_main');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_a21glossary_main');
