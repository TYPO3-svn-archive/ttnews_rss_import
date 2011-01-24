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

class tx_ttnewsrssimport_Api {

	/**
	 *
	 * @var int $counter Internal counter for renaming items
	 */
	protected $counter;
	protected $log = array();

	public $writeToLog = TRUE;
	public $forceImport = FALSE;
	public $extConf;

	/* Constants for format in function getMapping */
	const JAVASCRIPT_STRING = 1;
	const JAVASCRIPT_JSON = 2;
	const PHP_ARRAY = 3;


	/**
	 * @var array $defaultMapping Default mapping tt_news fields => RSS fields
	 */
	protected $defaultMapping = array(
		'title' => 'title',
		'links' => 'link',
		'datetime' => 'pubDate',
		'author' => 'dc:creator|author',
		'bodytext' => 'content:encoded',
		'ext_url' => 'guid|link',
	);



	/**
	 * Read given Feed into an array
	 *
	 * @param string $feedUrl
	 * @return array processed feed
	 */
	public function getFeed($feedUrl) {
		return $this->readFeedIntoArray($feedUrl);
	}

	/**
	 * Read tsConfig
	 *
	 * @param int $pid
	 * @return array tsConfig:
	 */
	public function getTSconfig($pid=0) {
		if ($pid === 0) {
			$pid = intval(t3lib_div::_GP('id'));
		}
		if ($pid === 0) {
			return array();
		} else {
			$tmp = t3lib_BEfunc::getModTSconfig($pid, 'mod.ttnew_rss_import');
			return $tmp['properties'];
		}
	}

	/**
	 * Reads complete Feed into an array
	 *
	 * @param string $feedUrl
	 * @return array raw and proceeded feed
	 */
	protected function readFeedIntoArray($feedUrl) {
		
		
		$xml = t3lib_div::getURL($feedUrl,0);
						
		$this->counter = 0;
		$res = preg_replace_callback(
			'|<([/]?)item|',
			array('tx_ttnewsrssimport_Api', countItems),
			$xml
		);
		$feed = array(
			'xml' => $xml,
			'proc' => t3lib_div::xml2array($res),
			'count' => $this->counter / 2
		);
		
		//fix for bug #6635 : Import Issues with non-UTF8 encodings. Charset of the RSS file was not detected.
		//Author : Alban Cousinie
		//extracting charset from RSS file
		$rss = new DOMDocument();
		$rss->loadXML($feed['xml']);
		$rssCharset = $rss->encoding;
		
		// charset conversion
		$GLOBALS['LANG']->csConvObj->convArray($feed, $rssCharset, $GLOBALS['LANG']->charSet, TRUE);
		//en of fix for bug #6635
		
		return $feed;
	}

	/**
	 * Does the DB import for given feed. Setting simulate to TRUE will only show the data but will not update the DB
	 *
	 * @param array $conf
	 * @param boolean $simulate
	 */
	public function importFeed($conf, $simulate = FALSE) {
		if (!isset($conf['url'])) {
			return -1;
		}

		$data = $dataCat = array();
		$dataCat['tt_news_cat'] = array();
		$pid = $conf['config']['storagePid'] ? $conf['config']['storagePid'] : $conf['config']['pid'];
		$catPid = $conf['config']['categoryStoragePid'] ? $conf['config']['categoryStoragePid'] : $pid;
		$item = $guid = array();
		$newcat = 1;
		$defaultCats = $conf['config']['cats'] ? ',' . $conf['config']['cats'] : '';
		
		//fix for bug #6589 : field mapping was ignored when typed into the RSS importer DB record mapping field
		//Author : Alban Cousinie
		$confMapping = is_array($conf['config']['mapping.']) ? $conf['config']['mapping.'] : array();
		if(is_array($conf['config']['mapping.'])){
			//mapping is exploitable directly as it is already a PHP array
			$confMapping = $conf['config']['mapping.'];
		}
		else{
			
			//here we have the mapping as a text configuration. Convert it to an array
			$confMapping = array();
			$arrayLines = preg_split("/(\r\n)/", $conf['config']['mapping.']);
			
			foreach($arrayLines as $lineIndex => $lineContent){
				$lineValues =  explode('=',$lineContent);
				$confMapping[trim($lineValues[0])] = trim($lineValues[1]);
			}
		}
		//END of fix for bug #6589
		//former buggy code left below (TODO : REMOVE THIS OLD CODE) :
		/*
		$confMapping = is_array($conf['config']['mapping.']) ? is_array($conf['config']['mapping.']) : array();
		*/
		
		$mapping = $this->getMapping($confMapping);


		if (isset($conf['proc']) && intval($conf['count']) > 0) {
				// generate new record array
			for ($i = 0; $i < intval($conf['count']); $i++) {
				$item = $conf['proc']['channel']['item' . $i];
				if (isset($conf['newscats'][$item['category']])) {
					$category = $conf['newscats'][$item['category']] . $defaultCats;
				} else {
					$dataCat['tt_news_cat']['NEWCAT' . $newcat] = array(
						'pid' => $catPid,
						'title' => $item['category'],
						'parent_category' => $conf['config']['newCategoryParentId']
					);
					if (isset($conf['config']['default.']['tt_news_cat.'])) {
						foreach ($conf['config']['default.']['tt_news_cat.'] as $key => $def) {
							$dataCat['tt_news_cat']['NEWCAT' . $newcat][$key] = $def;
						}
					}
					$category = 'NEWCAT' . $newcat++ . $defaultCats;
					$conf['newscats'][$item['category']] = $category;
				}
				$data['tt_news']['NEW' . $i] = array(
					'pid' => $pid,
					'hidden' => 0,
					'category' => $category,
				);

				if (isset($conf['config']['default.']['tt_news.'])) {
					foreach ($conf['config']['default.']['tt_news.'] as $key => $def) {
						$data['tt_news']['NEW' . $i][$key] = $def;
					}
				}
					// map data
				foreach ($mapping as $key => $map) {
						if ($map) {
						$parts = t3lib_div::trimExplode('|', $map);
						foreach ($parts as $part) {
							if (isset($item[$part])) {
								$data['tt_news']['NEW' . $i][$key] = $key == 'datetime' ? strtotime($item[$part]) : $item[$part];
								if ($key == 'ext_url') {
									$guid[$item[$part]] = 'NEW' . $i;
								}
								break;
							}
						}
					}
				}

			}

				// unset records which already exists
			$guids = t3lib_div::csvValues(array_keys($guid));
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('ext_url', 'tt_news', 'ext_url IN(' . $guids . ')' . t3lib_BEfunc::deleteClause('tt_news'));
			if ($res) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					unset ($data['tt_news'][$guid[$row['ext_url']]]);
				}
			}
		}


		if ($simulate === FALSE) {
				// update current record
			$data['tx_ttnewsrssimport_feeds'][$conf['config']['uid']] = array(
				'lastimport' => time(),
				'lastimportrss' => $conf['xml']
			);

			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->stripslashes_values = 0;

				//write cats
			if (count($dataCat)) {
				$tce->start($dataCat, array());
				$tce->process_datamap();
				$ret = $tce->substNEWwithIDs;
					//subst new cats
				foreach ($data['tt_news'] as $key => $value) {
					if (substr($value['category'], 0, 6) === 'NEWCAT') {
						$tmp = explode(',', $value['category']);
						$data['tt_news'][$key]['category'] = ltrim($ret[$tmp[0]] . $defaultCats, ',');
					}
				}
			}
				//write news
			$tce->start($data, array());
			$tce->process_datamap();

			unset($data['tx_ttnewsrssimport_feeds']);
			return array($dataCat['tt_news_cat'], $data['tt_news']);
		} else {
			return array($dataCat['tt_news_cat'], $data['tt_news']);
		}


	}

	/**
	 * Get the mapping, merge default with given configurtion
	 *
	 * @param array $mapping configured mapping
	 * @param int $format one of the constants
	 * @return array the mapping array
	 */
	public function getMapping($mapping, $format = self::PHP_ARRAY) {
		$mapping = array_merge($this->defaultMapping, $mapping);
		switch ($format) {
			case self::PHP_ARRAY:
				return $mapping;
			break;
			case self::JAVASCRIPT_JSON:
				return json_encode($mapping);
			break;
			case self::JAVASCRIPT_STRING:
				$str = '';
				foreach ($mapping as $key => $value) {
					$str .= "'" . $key . " = " . $value . '\'+"\n"+';
				}
				$str = substr($str, 0, -1);
				return trim($str);
			break;
		}
	}

	/**
	 * Get the existing news categories from one given parent category, flat list
	 *
	 * @param int $parentId
	 * @return array array with existing new categories
	 */
	public function getNewsCategories($parentId = 0) {
		$arr = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_news_cat', 'parent_category=' . intval($parentId) . t3lib_BEfunc::deleteClause('tt_news_cat'));
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$arr[$row['title']] = $row['uid'];
		}
		return $arr;
	}

	/**
	 * Convert given id's into integer, support commalists
	 * Example: input pages_33|News  output 33
	 * Example: input ttnews_7|Cat1,ttnews_15|Cat2,  output 7,15
	 *
	 * @param string $str
	 * @return string converted id(-list)
	 */
	public function getPidsFromPA($str) {
		$ret = array();
		if (strpos($str, ',')  !== FALSE) {
			$elements = t3lib_div::trimExplode(',', $str, TRUE);
			foreach ($elements as $element) {
				if (strpos($element, '|') !== FALSE) {
					$tmp = explode('|', $element);
					$ret[] = intval(preg_replace('/[^0-9]+/', '', $tmp[0]));
				} else {
					$ret[] = intval(preg_replace('/[^0-9]+/', '', $element));
				}
			}
		} else {
			if (strpos($str, '|') !== FALSE) {
				$tmp = explode('|', $str);
				$ret[] = intval(preg_replace('/[^0-9]+/', '', $tmp[0]));
			} else {
				$ret[] = intval(preg_replace('/[^0-9]+/', '', $str));
			}
		}

		return implode(',', $ret);
	}

	/**
	 * Get records from tx_ttnewsrssimport_feeds
	 *
	 * @param string $pidList
	 * @param string $uidList
	 * @param boolean $respectInterval
	 * @return array $rows
	 */
	public function getImportRecords($pidList = '', $uidList = '', $respectInterval = TRUE) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_ttnewsrssimport_feeds',
			'1' . ($pidList ? ' AND pid IN(' . $pidList . ')' : '') .
			($uidList ? ' AND uid IN(' . $uidList . ')' : '') .
			($respectInterval ? ' AND (UNIX_TIMESTAMP() - lastimport - updateinterval)>0' : '') .
			' AND deleted=0 AND hidden=0'
		);
		return $rows;
	}

	/**
	 * Imports a list or all rss feeds from existing records
	 *
	 * @param string $uidList
	 * @param boolean $simulate
	 * @return array:
	 */
	public function doImportForRecords($uidList='*', $simulate=FALSE) {
		$records = $this->getImportRecords('', $uidList == '*' ? '' : $uidList, $this->forceImport ? FALSE : TRUE);
		$data = array();
		$this->log = array();
		foreach ($records as $row) {
			$xml = $this->getFeed($row['url']);

			$tmp['url'] = $row['url'];
			$tmp['config'] = $this->getTSconfig($row['pid']);

			$pid = $row['newsrecordpid'] ? $row['newsrecordpid'] : ($tmp['config']['storagePid'] ? $tmp['config']['storagePid'] : $row['pid']);
			$catPid = $tmp['config']['categoryStoragePid'] ? $tmp['config']['categoryStoragePid'] : $pid;

			$tmp['config']['pid'] = $this->getPidsFromPA($pid);
			$tmp['config']['categoryStoragePid'] = $this->getPidsFromPA($catPid);
			$tmp['config']['uid'] = $row['uid'];
			$tmp['config']['newCategoryParentId'] = $row['newcategoryparent'] ? $this->getPidsFromPA($row['newcategoryparent']) : $tmp['config']['newCategoryParentId'];
				//cats
			$tmp['config']['cats'] = $row['newscategory'] ? $this->getPidsFromPA($row['newscategory']) : $tmp['config']['defaultCategories'];
			$tmp['config']['mapping.'] = $row['mapping'] ? $row['mapping'] : $tmp['config']['mapping.'];

			$tmp['newscats'] = $this->getNewsCategories($tmp['config']['newCategoryParentId']);
			$xml = array_merge($tmp, $xml);
			$data[$row['uid']] = $this->importFeed($xml, $simulate);

			$this->log[] = date('r') . chr(9) .
				($simulate ? $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.simulate') : $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.import')) . ' "' . $row['title'] . '" (' . $row['uid'] . ')' . chr(9) .
				$row['url'] . chr(9) .
				count($data[$row['uid']][0]) . ' ' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.categories') . ', ' .
				count($data[$row['uid']][1]) . ' ' . $GLOBALS['LANG']->sL('LLL:EXT:ttnews_rss_import/locallang.xml:wizard.news');
		}
		$this->writeLog($data);
		return $data;
	}

	/**
	 * Internal counter, used by getCompleteFeed
	 *
	 * @param array $matches
	 * @return int
	 */
	protected function countItems($matches) {
		return $matches[0] . floor($this->counter++ / 2);
	}
	
	/**
	 * Writes an Array to the logfile. Useful for debugging the scheduler task.
	 *
	 * @param array $theArray
	 */
	protected function arrayToLog($theArray){
		foreach($theArray as $index => $value){
			if(is_array($value)){
				$this->log[] = "[".$index."] => ARRAY";
				$this->log[] = "\t".$this->arrayToLog($value);
			}
			else $this->log[] = "[".$index."] => ".$value;
		}
	}

	protected function writeLog($data) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ttnews_rss_import']);

		if ($this->writeToLog && count($this->log) && is_file(PATH_site . $extConf['logFile'])) {
			$logEntry = chr(10) . implode(chr(10), $this->log);
			if( $fh = @fopen(PATH_site . $extConf['logFile'], 'a+')) {
				fputs($fh, $logEntry, strlen($logEntry));
				fclose($fh);
			}
		}
	}
}

?>