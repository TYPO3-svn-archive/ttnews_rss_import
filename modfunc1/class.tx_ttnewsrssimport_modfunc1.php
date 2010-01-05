<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009  <>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


include_once(PATH_t3lib . 'class.t3lib_extobjbase.php');
include_once(t3lib_extMgm::extPath('ttnews_rss_import') . 'classes/class.rssimport_api.php');


/**
 * Module extension (addition to function menu) 'News RSS Importer' for the 'ttnews_rss_import' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_ttnewsrssimport
 */
class tx_ttnewsrssimport_modfunc1 extends t3lib_extobjbase {

	/**
	 * Returns the module menu
	 *
	 * @return	Array with menuitems
	 */
	public function modMenu()	{
		global $LANG;

		return array ();
	}

	/**
	 * Main method of the module
	 *
	 * @return	HTML
	 */
	public function main()	{
			// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
		global $SOBE,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$theOutput.=$this->pObj->doc->spacer(5);
		$theOutput.=$this->pObj->doc->section($LANG->getLL("title"), $this->renderWizard, 0, 1);



		return $theOutput;
	}

	protected function renderWizard() {

	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_rss_import/modfunc1/class.tx_ttnewsrssimport_modfunc1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_rss_import/modfunc1/class.tx_ttnewsrssimport_modfunc1.php']);
}

?>