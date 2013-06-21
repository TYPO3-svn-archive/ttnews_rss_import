<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: $
 */

$extensionPath = t3lib_extMgm::extPath('ttnews_rss_import') . 'classes/';
$cliPath = t3lib_extMgm::extPath('ttnews_rss_import') . 'cli/';
return array(
	'tx_ttnewsrssimport_api' => $extensionPath . 'class.tx_ttnewsrssimport_api.php',
	'tx_ttnewsrssimport_updatetask' => $extensionPath . 'class.tx_ttnewsrssimport_updatetask.php',
	'tx_ttnewsrssimport_updatetask_additionalfieldprovider' => $extensionPath . 'class.tx_ttnewsrssimport_updatetask_additionalfieldprovider.php',
	'tx_ttnewsrssimport_cli' => $cliPath . 'class.tx_ttnewsrssimport_cli_dispatch.php',
);

?>