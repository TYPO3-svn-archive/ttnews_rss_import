#
# Table structure for table 'tx_ttnewsrssimport_feeds'
#
CREATE TABLE tx_ttnewsrssimport_feeds (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	url tinytext,
	updateinterval int(11) DEFAULT '0' NOT NULL,
	lastimport int(11) DEFAULT '0' NOT NULL,
	lastimportrss blob,
	newsrecordpid tinytext,
	newscategory varchar(255) DEFAULT '' NOT NULL,
	newcategoryparent varchar(255) DEFAULT '' NOT NULL,
	mapping tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

