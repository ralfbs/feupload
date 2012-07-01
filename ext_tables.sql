#
# Table structure for table 'tx_feupload_domain_model_file'
#
CREATE TABLE tx_feupload_domain_model_file (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	
	title varchar(255) DEFAULT '' NOT NULL,
	file varchar(255) DEFAULT '' NOT NULL,
	
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	fe_groups int(11) DEFAULT '0' NOT NULL,
	visibility int(1) DEFAULT '1' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_feupload_domain_model_file_fegroup_mm'
#
CREATE TABLE tx_feupload_file_fegroup_mm (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(50) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'pages'
#
CREATE TABLE fe_groups (
	feupload_storage_pid int(11) unsigned DEFAULT '' NOT NULL,
);
