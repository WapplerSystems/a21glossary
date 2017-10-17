#
# Table structure for table 'tx_a21glossary_main'
#
CREATE TABLE tx_a21glossary_main (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	t3ver_oid int(11) unsigned DEFAULT '0' NOT NULL,
	t3ver_id int(11) unsigned DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	short tinytext NOT NULL,
	shortcut tinytext NOT NULL,
	longversion tinytext NOT NULL,
	shorttype tinytext NOT NULL,
	language char(2) DEFAULT '' NOT NULL,
	description text NOT NULL,
	link tinytext NOT NULL,
	exclude tinyint(3) DEFAULT '0' NOT NULL,
	force_linking int(11) DEFAULT '0' NOT NULL,
	force_case int(11) DEFAULT '0' NOT NULL,
	force_preservecase int(11) DEFAULT '0' NOT NULL,
	force_regexp int(11) DEFAULT '0' NOT NULL,
	force_global int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);