<?php

########################################################################
# Extension Manager/Repository config file for ext "ttnews_rss_import".
#
# Auto generated 30-12-2009 23:15
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'News RSS Importer',
	'description' => 'Import RSS to news records',
	'category' => 'module',
	'author' => 'Steffen Kamper',
	'author_email' => 'info@sk-typo3.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => 'modfunc1',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_news',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'sk-typo3',
	'version' => '0.8.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.0.0-5.3.99',
			'typo3' => '4.3.0-4.3.99',
			'tt_news' => '3.0.1-3.9.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:15:{s:9:"ChangeLog";s:4:"489f";s:10:"README.txt";s:4:"9fa9";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"51e6";s:14:"ext_tables.sql";s:4:"d6d7";s:33:"icon_tx_ttnewsrssimport_feeds.gif";s:4:"475a";s:16:"locallang.xml";s:4:"ac76";s:22:"t3blogger_complete.xml";s:4:"3fb3";s:7:"tca.php";s:4:"384a";s:31:"classes/class.rssimport_api.php";s:4:"65d7";s:51:"classes/class.tx_ttnewsrssimport_tceforms_hooks.php";s:4:"643e";s:19:"doc/wizard_form.dat";s:4:"4d78";s:20:"doc/wizard_form.html";s:4:"7d0e";s:46:"modfunc1/class.tx_ttnewsrssimport_modfunc1.php";s:4:"dd03";s:22:"modfunc1/locallang.xml";s:4:"15ca";}',
);

?>