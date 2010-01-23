<?php
if (! defined('TYPO3_MODE')) {
	die('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_ttnewsrssimport_feeds');

$TCA['tx_ttnewsrssimport_feeds'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden'
		),
		'dividers2tabs' => 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_ttnewsrssimport_feeds.gif'
	)
);

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::insertModuleFunction(
		'web_func',
		'tx_ttnewsrssimport_modfunc1',
		t3lib_extMgm::extPath($_EXTKEY) . 'modfunc1/class.tx_ttnewsrssimport_modfunc1.php',
		'LLL:EXT:ttnews_rss_import/locallang.xml:title',
		'wiz'
	);
}

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['ttnewsrssimport::getFeedInWizard'] = 'EXT:ttnews_rss_import/classes/class.tx_ttnewsrssimport_tceforms_hooks.php:tx_ttnewsrssimport_tceforms_hooks->ajaxDispatch';

// show bodytext in tt_news type 2
t3lib_div::loadTCA('tt_news');
$TCA['tt_news']['types']['2']['showitem'] = str_replace('short,', 'short,bodytext,', $TCA['tt_news']['types']['2']['showitem']);



?>