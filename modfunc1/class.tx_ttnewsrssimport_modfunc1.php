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

/**
 * Module extension (addition to function menu) 'News RSS Importer' for the 'ttnews_rss_import' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_ttnewsrssimport
 */
class tx_ttnewsrssimport_modfunc1 extends t3lib_extobjbase {

	protected $api;

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

		$this->api = t3lib_div::makeInstance('tx_ttnewsrssimport_Api');

		$input = t3lib_div::_GP('data');
		$selectedRecords = $input['tx_ttnewsrssimport_feeds'];
		$theOutput = '';

		$this->pObj->doc->inDocStylesArray[] = '
		.rssimport-formactions {margin-top:20px;border-top:1px dashed #333;width:500px;padding-top:5px;}
		.rssimport-protocol {margin:20px 20px 0 0;border:1px solid #aaa;padding:5px;background:#eee;line-height:1;}
		.rssimport-protocol table {margin:10px 0;}
		';

		$extIcon = '<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('ttnews_rss_import'), 'ext_icon.gif') . ' alt="" style="margin-right:5px;" />';
		$theOutput .= $this->pObj->doc->spacer(5);
		$theOutput .= $this->pObj->doc->section($extIcon . $GLOBALS['LANG']->sL("LLL:EXT:ttnews_rss_import/locallang.xml:wizard.title"), '', FALSE, TRUE, FALSE, TRUE);

		$tableLayout = array (
			'table' => array (
				'<table border="0" cellspacing="1" cellpadding="2" style="width:auto;" id="typo3-filelist">', '</table>'),
				'0' => array (
					'tr' => array (
						'<tr class="c-headLine" valign="top">', '</tr>'
					),
					'defCol' => array ('<td class="cell">', '</td>')
				),
				'defRowOdd' => array (
					'tr' => array ('<tr class="bgColor6">', '</tr>'),
					'defCol' => array ('<td class="cell">', '</td>')
				),
				'defRowEven' => array (
					'tr' => array ('<tr class="bgColor4">', '</tr>'),
					'defCol' => array ('<td class="cell">', '</td>')
				),

		);
		$table = $titles = array ();
		$tr = 0;

		$icon_no = '<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('scheduler'), 'res/gfx/status_scheduled.png') . ' alt="" style="margin-right:5px;" />';
		$icon_yes = '<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('scheduler'), 'res/gfx/status_running.png') . ' alt="" style="margin-right:5px;" />';

		$records = $this->api->getImportRecords('', '', FALSE);

		// Header row
		$table[$tr][] = '<input type="checkbox" id="checkAll" name="data[checkAll]" value="1" onclick="$$(\'.checkImports\').each(function(e){e.checked=$(\'checkAll\').checked;});" value="1" ' . ($input['checkAll'] ? 'checked="checked"' : '') . ' />'; //all check
		$table[$tr][] = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.pid');
		$table[$tr][] = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.uidtitle');
		$table[$tr][] = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.url');
		$table[$tr][] = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.updateintervalshort');
		$table[$tr][] = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.lastimport');
		$table[$tr][] = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.edit');
		$tr ++;

		foreach ($records as $row) {
			$titles[$row['uid']] = $row['title'];
			$params = '&edit[tx_ttnewsrssimport_feeds][' . $row['uid'] . ']=edit';
			$edit = '<a href="#" onclick="' . htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])) . '">
				<img' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif') . ' title="' . $GLOBALS['LANG']->getLL('editRecord', 1) . '" alt="" />
			</a>';
			$last = time() - $row['lastimport'];
			$icon = $last > $row['updateinterval'] ? $icon_yes : $icon_no;

			$table[$tr][] = '<input type="checkbox" class="checkImports" name="data[tx_ttnewsrssimport_feeds][' .$row['uid'] . ']" value="1" ' . ($selectedRecords[$row['uid']] ? 'checked="checked"' : '') . ' />';
			$table[$tr][] = htmlspecialchars($row['pid']);
			$table[$tr][] = '[' . $row['uid'] . '] ' .htmlspecialchars($row['title']) ;
			$table[$tr][] = htmlspecialchars($row['url']);
			$table[$tr][] = htmlspecialchars($row['updateinterval']) . ' ' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.seconds');;
			$table[$tr][] = $icon . ' ' . t3lib_beFunc::calcAge($last) . ' (' . $last . ')';
			$table[$tr][] = $edit;
			$tr ++;
		}

		$theOutput .= $this->pObj->doc->table($table, $tableLayout);

		$theOutput .= '
		<div class="rssimport-formactions">
			<input type="checkbox" class="checkbox" name="data[protocol]" value="1" id="check_protocol" ' . ($input['protocol'] ? 'checked="checked"' : '') . ' />
			<label for="check_protocol">' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.outputProtocol') . '</label>
			<input type="checkbox" class="checkbox" name="data[log]" value="1" id="check_log" ' . ($input['log'] ? 'checked="checked"' : '') . ' />
			<label for="check_log">' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.writeLog') . '</label><br /><br />

			<input type="submit" name="data[submit_simulate]" value="' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.import.1') . '" />
			<input type="submit" name="data[submit_do]" value="' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.import.0') . '" />
		</div>';


		if ($input['submit_simulate'] || $input['submit_do']) {
			$simulate = isset($input['submit_simulate']);
			$protocol = isset($input['protocol']);
			$log = isset($input['log']);
			$this->api->writeToLog = $log;
			$this->api->forceImport = TRUE;

			if (count($selectedRecords) === 0) {
				$theOutput .= '<div class="rssimport-protocol">' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.noselected') . '</div>';
			} else {
				$out = array();
				$uids = implode(',', array_keys($selectedRecords));
				$data = $this->api->doImportForRecords($uids, $simulate);
				foreach ($data as $key => $value) {
					$cats = count($value[0]);
					$news = count($value[1]);
					$icon = $cats + $news > 0 ? $icon_yes : $icon_no;

					$out[] = $icon . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.import.' . intval($simulate)) .
					': ' . htmlspecialchars($titles[$key] . ' (' . $key . ')') . ': ' .
					$GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.categories') . ': ' . $cats . ' ' .
					$GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.news') . ': ' . $news . '<br/>' .
					($protocol ? t3lib_utility_Debug::viewArray($value) : '');
				}
				$theOutput .= '<div class="rssimport-protocol">' . implode('<br />', $out) . '</div>';
			}
		}
		return $theOutput;
	}


}



if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ttnews_rss_import/modfunc1/class.tx_ttnewsrssimport_modfunc1.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ttnews_rss_import/modfunc1/class.tx_ttnewsrssimport_modfunc1.php']);
}

?>