<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return array(
    'ctrl' => array(
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
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ),
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, short, shortcut, longversion, shorttype, language, description, link, exclude, force_linking, force_case, force_preservecase, force_regexp, force_global',
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,short,shortcut,longversion,shorttype,language,description,link,exclude,force_linking,force_case,force_preservecase,force_regexp,force_global'
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
                )
            )
        ),
        'l18n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_a21glossary_main',
                'foreign_table_where' => 'AND tx_a21glossary_main.pid=###CURRENT_PID### AND tx_a21glossary_main.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'starttime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => array(
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
                )
            )
        ),
        'fe_group' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.usergroups', '--div--')
                ),
                'foreign_table' => 'fe_groups'
            )
        ),
        'short' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xlf:tx_a21glossary_main.short',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            )
        ),
        'shortcut' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shortcut',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            )
        ),
        'longversion' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.longversion',
            'config' => array(
                'type' => 'input',
                'size' => '48',
                'eval' => 'trim',
            )
        ),
        'shorttype' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.-1', ''),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.0', 'span'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.1', 'dfn'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.2', 'acronym'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.shorttype.I.3', 'abbr'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'language' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.0', ''),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.1', 'en'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.2', 'fr'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.3', 'de'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.4', 'it'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.5', 'es'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.6', 'pt'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.7', 'ru'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.8', 'zh'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.9', 'ja'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.10', 'el'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.11', 'grc'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.12', 'la'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.language.I.13', 'he'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.description',
            'config' => array(
                'type' => 'text',
                'cols' => '48',
                'rows' => '5',
            )
        ),
        'link' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.link',
            'config' => array(
                'type' => 'input',
                'renderType' => 'inputLink',
                'max' => '255',
                'eval' => 'trim',
            )
        ),
        'exclude' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.exclude',
            'config' => array(
                'type' => 'check',
            )
        ),

        'force_linking' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking.I.0', '0'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking.I.1', '1'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_linking.I.2', '2'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'force_case' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case.I.0', '0'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case.I.1', '1'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_case.I.2', '2'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'force_preservecase' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase.I.0', '0'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase.I.1', '1'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_preservecase.I.2', '2'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'force_regexp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_regexp',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_regexp.I.0', '0'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_regexp.I.1', '1'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'force_global' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_global',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_global.I.0', '0'),
                    array('LLL:EXT:a21glossary/Resources/Private/Language/locallang_db.xml:tx_a21glossary_main.force_global.I.1', '1'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, short, shortcut, longversion, shorttype, language, description;;;richtext:rte_transform[flag=rte_enabled|mode=ts_css|imgpath=uploads/tx_a21glossary/rte/], link, exclude,force_linking,force_case,force_preservecase,force_regexp,force_global')
    ),
    'palettes' => array(
        '1' => array('showitem' => 'starttime, endtime, fe_group'),
        '2' => array('showitem' => 'shortcut'),
    )
);

?>