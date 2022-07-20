<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "a21glossary".
 *
 * Auto generated 08-04-2014 13:08
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'A21 Glossary',
    'description' => 'A21 Glossary - automatic conversion of all abbreviations and acronyms in the special tags for accessibility issues',
    'version' => '11.0.0',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'autoload' => [
        'psr-4' => [
            'WapplerSystems\\A21glossary\\' => 'Classes'
        ]
    ],
];
