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


class tx_ttnewsrssimport_tceforms_hooks {

	protected $container = '<div class="rsswimportiz">|</div>';
	protected $headerInserted = FALSE;
	protected $lastXML;

	protected $api;

	public function __construct() {
			// initialize API
		$this->api = t3lib_div::makeInstance('tx_ttnewsrssimport_Api');
	}

	/**
	 * userFunc field in TCEFORMS
	 *
	 * @param array $PA
	 * @param t3lib_tceForms $pObj
	 * @return string result
	 */
	public function wizard($PA, $pObj) {
		$row = $PA['row'];
		$row['storagePid'] = $row['newsrecordpid'] ? $row['newsrecordpid'] : $row['pid'];

		if (!$row['url']) {
			return str_replace('|', $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.enterValidUrl'), $this->container);
		} else {
				// get the feed
			$content = $this->getFeedInWizard($row);
			if ($this->lastXML) {
				$PA['row']['lastimportrss'] = $this->XML;
				$PA['row']['lastimport'] = time();
			}
			return $content;
		}
	}

	/**
	 * Help function for wizard
	 *
	 * @param array $row
	 * @return string Content
	 */
	public function getFeedInWizard($row) {
		$in = t3lib_div::_GP('feedwizard');
		$this->lastXML = NULL;

		if ($in['testfeed'] || $in['simulate'] || $in['import']) {
			$xml = $this->api->getFeed($row['url']);
			$xml['url'] = $row['url'];

			if ($in['simulate'] || $in['import']) {
				$xml['config'] = $this->api->getTSconfig($row['storagePid']);

				$pid = $row['newsrecordpid'] ? $row['newsrecordpid'] : ($xml['config']['storagePid'] ? $xml['config']['storagePid'] : $row['pid']);
				$catPid = $xml['config']['categoryStoragePid'] ? $xml['config']['categoryStoragePid'] : $pid;

				$xml['config']['pid'] = $this->api->getPidsFromPA($pid);
				$xml['config']['categoryStoragePid'] = $this->api->getPidsFromPA($catPid);
				$xml['config']['uid'] = $row['uid'];
				$xml['config']['newCategoryParentId'] = $row['newcategoryparent'] ? $this->api->getPidsFromPA($row['newcategoryparent']) : $xml['config']['newCategoryParentId'];
					//cats
				$xml['config']['cats'] = $row['newscategory'] ? $this->api->getPidsFromPA($row['newscategory']) : $xml['config']['defaultCategories'];
				$xml['config']['mapping.'] = $row['mapping'] ? $row['mapping'] : $xml['config']['mapping.'];
			}
		}

		if ($in['testfeed']) {
			$hlObj = t3lib_div::makeInstance('t3lib_syntaxhl');
			$feed = t3lib_div::getURL($row['url']);
			$title = 'Feed: <a href="' . $row['url'] . '" target="_blank" />' . $row['url'] . '</a>';
			$content = '<p>' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.contentLength') . ': ' . t3lib_div::formatSize(strlen($feed)) . '</p>
			<h4>' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.preview') . ':</h4>
			<iframe width="800px" height="300" frameborder="1" src="' . $row['url'] . '"></iframe>
			<h4>' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.source') . ':</h4>
			<pre style="background:#fff;border: 1px solid #333; padding: 10px; overflow: scroll; width: 800px;height: 400px;">' . $hlObj->highLight_DS($feed) . '</pre>';
		} elseif ($in['simulate']) {
			$xml['newscats'] = $this->api->getNewsCategories($xml['config']['newCategoryParentId']);
			$data = $this->api->importFeed($xml, 1);
			$categories = count($data[0]);
			$news = count($data[1]);
			$title = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.simulate');
			$content = '<p style="font-weight:bold;margin:5px 15px;">' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.newCategories') . ': ' .
				$categories . '<br />' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.newArticles') . ': ' .
				$news . '<br /></p>';
			if ($categories + $news > 0) {
				$content .= t3lib_div::view_array($data);
			} else {
				$content .= '<p>' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.noItemsToImport') . '</p>';
			}
		} elseif ($in['import']) {
			$this->lastXML = $xml['xml'];
			$xml['newscats'] = $this->api->getNewsCategories($xml['config']['newCategoryParentId']);
			$data = $this->api->importFeed($xml);
			$categories = count($data[0]);
			$news = count($data[1]);
			$title = '' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.import') . '';
			$content = '<p style="font-weight:bold;margin:5px 15px;">' .
				sprintf($GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.categoriesImportedInPid'), $xml['config']['categoryStoragePid']) . ': ' .
				$categories . '<br />' .
				sprintf($GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.articlesImportedInPid'), $xml['config']['pid']) . ': ' .
				$news . '<br /></p>';
			if ($categories + $news > 0) {
				$content .= t3lib_div::view_array($data);
			} else {
				$content .= '<p>' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.noItemsToImport') . '</p>';
			}
		} else {
			$title = $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.selectAction');
			$content = '';
		}




		return strtr($this->getTemplate(), array(
			'###TITLE###' => $title,
			'###CONTENT###' => $content,
			'###CONTROLS###' => $this->getControls(),
		));
	}

	/**
	 * Get control buttons for wizard field
	 *
	 * @return string buttons
	 */
	protected function getControls() {
		return '
			<input type="submit" name="feedwizard[testfeed]" value="' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.button.textXML') . '" />
			&nbsp;
			<input type="submit" name="feedwizard[simulate]" value="' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.button.simulateImport') . '" />
			&nbsp;
			<input type="submit" name="feedwizard[import]" value="' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.button.doImport') . '" />
		';
	}

	/**
	 * Give the HTML template
	 *
	 * @return string template
	 */
	protected function getTemplate() {
		$out = '
		<div id="feedwizard-controls" style="background: #eee;margin-bottom:15px;">###CONTROLS###</div>
		<h2 id="feedxmlarray" class="section-header expanded">###TITLE###</h2>
		###CONTENT###'
		;
		return str_replace('|', $out, $this->container);
	}

	/**
	 * Wizard for the mapping field
	 *
	 * @param array $params
	 * @param t3lib_tceForms $pObj
	 * @return string Content
	 */
	public function renderMappingWizard($params, $pObj) {
		$conf = $this->api->getTSconfig($params['pid']);
		$confMapping = is_array($conf['mapping.']) ? is_array($conf['mapping.']) : array();
		$mapping = $this->api->getMapping($confMapping, tx_ttnewsrssimport_Api::JAVASCRIPT_STRING);
		$onClick = 'document.getElementsByName(\'' . $params['itemName'] . '\')[0].value = ' . $mapping . ';';
		return '<input type="button" name="feedwizard[getMapping]" value="' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang_db.xml:tceforms.button.loadMapping') . '" onclick="' . htmlspecialchars($onClick) . '" />';
	}

	/**
	 * Dispatcher for AJAX calls
	 *
	 * @param array $params
	 * @param TYPO3AJAX $ajaxObj
	 */
	public function ajaxDispatch(array $params, TYPO3AJAX $ajaxObj) {

	}
}

?>