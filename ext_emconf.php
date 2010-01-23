<?php

########################################################################
# Extension Manager/Repository config file for ext "ttnews_rss_import".
#
# Auto generated 23-01-2010 12:18
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
	'dependencies' => 'cms,tt_news',
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
	'version' => '0.8.1',
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
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"489f";s:10:"README.txt";s:4:"ee2d";s:16:"ext_autoload.php";s:4:"8b02";s:21:"ext_conf_template.txt";s:4:"e5fe";s:12:"ext_icon.gif";s:4:"f11e";s:17:"ext_localconf.php";s:4:"073c";s:14:"ext_tables.php";s:4:"ac54";s:14:"ext_tables.sql";s:4:"00a1";s:33:"icon_tx_ttnewsrssimport_feeds.gif";s:4:"f11e";s:13:"locallang.xml";s:4:"98b2";s:7:"tca.php";s:4:"88bc";s:31:"classes/class.rssimport_api.php";s:4:"915e";s:40:"classes/class.tx_ttnewsrssimport_api.php";s:4:"f560";s:51:"classes/class.tx_ttnewsrssimport_tceforms_hooks.php";s:4:"3485";s:47:"classes/class.tx_ttnewsrssimport_updatetask.php";s:4:"07a4";s:71:"classes/class.tx_ttnewsrssimport_updatetask_additionalfieldprovider.php";s:4:"acf5";s:45:"cli/class.tx_ttnewsrssimport_cli_dispatch.php";s:4:"918d";s:14:"doc/manual.sxw";s:4:"c979";s:46:"modfunc1/class.tx_ttnewsrssimport_modfunc1.php";s:4:"f177";}',
	'suggests' => array(
	),
);

?>