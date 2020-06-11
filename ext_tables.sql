CREATE TABLE tx_a21glossary_domain_model_entry
(
    short              tinytext                        NOT NULL,
    shortcut           tinytext                        NOT NULL,
    longversion        tinytext                        NOT NULL,
    shorttype          tinytext                        NOT NULL,
    language           char(2)             DEFAULT ''  NOT NULL,
    description        text                            NOT NULL,
    link               tinytext                        NOT NULL,
    exclude            tinyint(3)          DEFAULT '0' NOT NULL,
    force_linking      int(11)             DEFAULT '0' NOT NULL,
    force_case         int(11)             DEFAULT '0' NOT NULL,
    force_preservecase int(11)             DEFAULT '0' NOT NULL,
    force_regexp       int(11)             DEFAULT '0' NOT NULL,
    force_global       int(11)             DEFAULT '0' NOT NULL,

    KEY parent (pid),
    KEY language (l18n_parent,sys_language_uid)
);