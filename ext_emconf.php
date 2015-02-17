<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ttnews_rss_import".
 *
 * Auto generated 17-02-2015 10:02
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'News RSS Importer',
	'description' => 'Import RSS to news records',
	'category' => 'module',
	'author' => 'Steffen Kamper, Xavier Perseguers',
	'author_email' => 'xavier@causal.ch',
	'shy' => '',
	'dependencies' => 'tt_news',
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
	'author_company' => 'Causal Sàrl',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.3-5.6.99',
			'typo3' => '4.5.0-6.2.99',
			'tt_news' => '3.0.1-3.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"8d5c";s:16:"ext_autoload.php";s:4:"be91";s:21:"ext_conf_template.txt";s:4:"8074";s:12:"ext_icon.gif";s:4:"f11e";s:17:"ext_localconf.php";s:4:"3fc5";s:14:"ext_tables.php";s:4:"ac54";s:14:"ext_tables.sql";s:4:"2fa7";s:33:"icon_tx_ttnewsrssimport_feeds.gif";s:4:"f11e";s:13:"locallang.xml";s:4:"b570";s:7:"tca.php";s:4:"f0b3";s:40:"classes/class.tx_ttnewsrssimport_api.php";s:4:"d0c3";s:51:"classes/class.tx_ttnewsrssimport_tceforms_hooks.php";s:4:"949a";s:47:"classes/class.tx_ttnewsrssimport_updatetask.php";s:4:"55cb";s:71:"classes/class.tx_ttnewsrssimport_updatetask_additionalfieldprovider.php";s:4:"5396";s:45:"cli/class.tx_ttnewsrssimport_cli_dispatch.php";s:4:"918d";s:14:"doc/manual.sxw";s:4:"f873";s:46:"modfunc1/class.tx_ttnewsrssimport_modfunc1.php";s:4:"de86";}',
	'suggests' => array(
	),
);

?>