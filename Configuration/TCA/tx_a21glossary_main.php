<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main',
        'label' => 'short',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioning' => '1',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'default_sortby' => 'ORDER BY short,uid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, short, shortcut, longversion, shorttype, language, description, link, exclude, force_linking, force_case, force_preservecase, force_regexp, force_global',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,short,shortcut,longversion,shorttype,language,description,link,exclude,force_linking,force_case,force_preservecase,force_regexp,force_global'
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ]
            ]
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_a21glossary_main',
                'foreign_table_where' => 'AND tx_a21glossary_main.pid=###CURRENT_PID### AND tx_a21glossary_main.sys_language_uid IN (-1,0)',
            ]
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            ]
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
                ]
            ]
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.any_login', -2],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.usergroups', '--div--']
                ],
                'foreign_table' => 'fe_groups'
            ]
        ],
        'short' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.short',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ]
        ],
        'shortcut' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shortcut',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'longversion' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.longversion',
            'config' => [
                'type' => 'input',
                'size' => '48',
                'eval' => 'trim',
            ]
        ],
        'shorttype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.-1', ''],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.0', 'span'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.1', 'dfn'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.2', 'acronym'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.3', 'abbr'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.0', ''],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.1', 'en'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.2', 'fr'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.3', 'de'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.4', 'it'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.5', 'es'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.6', 'pt'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.7', 'ru'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.8', 'zh'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.9', 'ja'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.10', 'el'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.11', 'grc'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.12', 'la'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.13', 'he'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.description',
            'config' => [
                'type' => 'text',
                'cols' => '48',
                'rows' => '5',
            ]
        ],
        'link' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.link',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'max' => '255',
                'eval' => 'trim',
            ]
        ],
        'exclude' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.exclude',
            'config' => [
                'type' => 'check',
            ]
        ],

        'force_linking' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking.I.0', '0'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking.I.1', '1'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking.I.2', '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_case' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case.I.0', '0'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case.I.1', '1'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case.I.2', '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_preservecase' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase.I.0', '0'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase.I.1', '1'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase.I.2', '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_regexp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_regexp',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_regexp.I.0', '0'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_regexp.I.1', '1'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_global' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_global',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_global.I.0', '0'],
                    ['LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_global.I.1', '1'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, short, shortcut, longversion, shorttype, language, description;;;richtext:rte_transform[flag=rte_enabled|mode=ts_css|imgpath=uploads/tx_a21glossary/rte/], link, exclude,force_linking,force_case,force_preservecase,force_regexp,force_global']
    ],
    'palettes' => [
        '1' => ['showitem' => 'starttime, endtime, fe_group'],
        '2' => ['showitem' => 'shortcut'],
    ]
];

