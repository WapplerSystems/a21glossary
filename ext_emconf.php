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

$EM_CONF['a21glossary'] = [
    'title' => 'A21 Glossary',
    'description' => 'A21 Glossary - automatic conversion of all abbreviations and acronyms in the special tags for accessibility issues',
    'version' => '12.0.0',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
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
