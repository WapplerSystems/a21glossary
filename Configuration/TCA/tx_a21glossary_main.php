<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.entry',
        'label' => 'short',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'default_sortby' => 'short asc',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'iconfile' => 'EXT:a21glossary/Resources/Public/Icons/Extension.svg',
        'security' => [
            'ignorePageTypeRestriction' => true
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0]
                ],
                'foreign_table' => 'tx_a21glossary_main',
                'foreign_table_where' => 'AND tx_a21glossary_main.pid=###CURRENT_PID### AND tx_a21glossary_main.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'searchable' => false,
                'default' => 0
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'searchable' => false,
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ]
            ]
        ],
        'fe_group' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login', 'value' => -1],
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login', 'value' => -2],
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups', 'value' => '--div--']
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
            ]
        ],
        'short' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.short',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
                'required' => true,
            ]
        ],
        'shortcut' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shortcut',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'longversion' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.longversion',
            'config' => [
                'type' => 'input',
                'size' => '48',
                'eval' => 'trim',
            ]
        ],
        'shorttype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shorttype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shorttype.I.-1', 'value' => ''],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shorttype.I.0', 'value' => 'span'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shorttype.I.1', 'value' => 'dfn'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shorttype.I.2', 'value' => 'acronym'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.shorttype.I.3', 'value' => 'abbr'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.0', 'value' => ''],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.1', 'value' => 'en'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.2', 'value' => 'fr'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.3', 'value' => 'de'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.4', 'value' => 'it'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.5', 'value' => 'es'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.6', 'value' => 'pt'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.7', 'value' => 'ru'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.8', 'value' => 'zh'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.9', 'value' => 'ja'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.10', 'value' => 'el'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.11', 'value' => 'grc'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.12', 'value' => 'la'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.language.I.13', 'value' => 'he'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.description',
            'config' => [
                'type' => 'text',
                'searchable' => false,
                'cols' => '48',
                'rows' => '5',
            ]
        ],
        'link' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.link',
            'config' => [
                'type' => 'link',
                'searchable' => false,
            ]
        ],
        'exclude' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.exclude',
            'config' => [
                'type' => 'check',
            ]
        ],
        'force_linking' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_linking',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_linking.I.0', 'value' => '0'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_linking.I.1', 'value' => '1'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_linking.I.2', 'value' => '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_case' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_case',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_case.I.0', 'value' => '0'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_case.I.1', 'value' => '1'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_case.I.2', 'value' => '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_preservecase' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_preservecase',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_preservecase.I.0', 'value' => '0'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_preservecase.I.1', 'value' => '1'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_preservecase.I.2', 'value' => '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_regexp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_regexp',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_regexp.I.0', 'value' => '0'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_regexp.I.1', 'value' => '1'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'force_global' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_global',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_global.I.0', 'value' => '0'],
                    ['label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.force_global.I.1', 'value' => '1'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    short, shorttype, description,
                --div--;LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.tabs.advanced,
                    shortcut, longversion, language, link, exclude,
                --div--;LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.tabs.settings,
                    force_linking,force_case,force_preservecase,force_regexp,force_global,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;;access,',
            'columnsOverrides' => [
                'description' => [
                    'config' => [
                        'enableRichtext' => true,
                        'richtextConfiguration' => 'default'
                    ]
                ]
            ]
        ]
    ],
    'palettes' => [
        'language' => [
            'showitem' => '
                sys_language_uid,l18n_parent
            ',
        ],
        'access' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
            'showitem' => '
                starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                --linebreak--,
                fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel
            ',
        ],
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            ',
        ],
    ]
];

