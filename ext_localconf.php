<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_ttnewsrssimport_UpdateTask'] = array(
		'extension'        => $_EXTKEY,
		'title'            => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:updateTask.name',
		'description'      => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:updateTask.description',
		'additionalFields' => 'tx_ttnewsrssimport_UpdateTask_AdditionalFieldProvider',
	);
}

	// Register the Scheduler as a possible key for CLI calls
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array(
	'EXT:' . $_EXTKEY . '/cli/rssimport_cli_dispatch.php', '_CLI_rssimport'
);

?>