<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Steffen Kamper <info@sk-typo3.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_ttnewsrssimport_UpdateTask extends tx_scheduler_Task {

	protected $api;
	public $task_uidList;

	public function __construct() {
			// initialize API
		$this->api = t3lib_div::makeInstance('tx_ttnewsrssimport_Api');
		parent::__construct();
	}

	public function execute() {
		if (!is_object($this->api)) {
			$this->api = t3lib_div::makeInstance('tx_ttnewsrssimport_Api');
		}
		$data = $this->api->doImportForRecords($this->task_uidList);
		return TRUE;
	}

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ttnews_rss_import/classes/class.tx_ttnewsrssimport_updatetask_additionalfieldprovider.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ttnews_rss_import/classes/class.tx_ttnewsrssimport_updatetask_additionalfieldprovider.php']);
}
?>