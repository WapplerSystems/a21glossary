#
# Table structure for table 'tx_a21glossary_main'
#
CREATE TABLE tx_a21glossary_main
(
	short              varchar(255) DEFAULT '' NOT NULL,
	shortcut           varchar(255) DEFAULT '' NOT NULL,
	longversion        varchar(255) DEFAULT '' NOT NULL,
	shorttype          varchar(255) DEFAULT '' NOT NULL,
	language           char(2)    DEFAULT ''  NOT NULL,
	description        text,
	link               varchar(255) DEFAULT '' NOT NULL,
	exclude            tinyint(3) DEFAULT '0' NOT NULL,
	force_linking      int(11)    DEFAULT '0' NOT NULL,
	force_case         int(11)    DEFAULT '0' NOT NULL,
	force_preservecase int(11)    DEFAULT '0' NOT NULL,
	force_regexp       int(11)    DEFAULT '0' NOT NULL,
	force_global       int(11)    DEFAULT '0' NOT NULL
);
