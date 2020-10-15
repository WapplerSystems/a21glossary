<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}


TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SveWap.A21glossary',
    'Pi1',
    ['Glossary' => 'index,search'],
    ['Glossary' => 'search']
);
