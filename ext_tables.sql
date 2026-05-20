# Extend table structure for table 'sys_file_storage'
CREATE TABLE sys_file_storage (
    protected tinyint(3) DEFAULT 0 NOT NULL,
    protected_by_default int(11) unsigned DEFAULT 0 NOT NULL
);

#
# Table structure for table 'tx_fpfileprotector_domain_model_protection'
#
CREATE TABLE tx_fpfileprotector_domain_model_protection (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    storage int(11) unsigned DEFAULT 0 NOT NULL,
    folder varchar(255) DEFAULT '' NOT NULL,
    fe_login tinyint(3) DEFAULT 0 NOT NULL,
    be_login tinyint(3) DEFAULT 0 NOT NULL,
    user_groups int(11) unsigned DEFAULT 0 NOT NULL,
    users int(11) unsigned DEFAULT 0 NOT NULL,

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

    t3ver_oid int(11) DEFAULT '0' NOT NULL,
    t3ver_id int(11) DEFAULT '0' NOT NULL,
    t3ver_wsid int(11) DEFAULT '0' NOT NULL,
    t3ver_label varchar(255) DEFAULT '' NOT NULL,
    t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_stage int(11) DEFAULT '0' NOT NULL,
    t3ver_count int(11) DEFAULT '0' NOT NULL,
    t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
    t3ver_move_id int(11) DEFAULT '0' NOT NULL,

    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l10n_source int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid,t3ver_wsid),
);

# MM Table between tx_fpfileproctor_domain_model_protection and fe_groups
CREATE TABLE tx_fpfileprotector_protection_fegroups_mm (
    uid_local int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
# MM Table between tx_fpfileproctor_domain_model_protection and fe_users
CREATE TABLE tx_fpfileprotector_protection_feusers_mm (
    uid_local int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,
    KEY uid_local (uid_local),
KEY uid_foreign (uid_foreign)
);