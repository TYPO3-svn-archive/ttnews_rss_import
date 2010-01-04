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

class rssimport_api {

	/**
	 *
	 * @var int $counter Internal counter for renaming items
	 */
	protected $counter;
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

	/* Constants for format in function getMapping */
	const JAVASCRIPT_STRING = 1;
	const JAVASCRIPT_JSON = 2;
	const PHP_ARRAY = 3;

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
		$feed = t3lib_div::getURL($feedUrl);

		$this->counter = 0;
		$res = preg_replace_callback(
			'|<([/]?)item|',
			array('rssimport_api', countItems),
			$feed
		);
		$feed = array(
			'xml' => $feed,
			'proc' => t3lib_div::xml2array($res),
			'count' => $this->counter / 2
		);
			// charset conversion
		$GLOBALS['LANG']->csConvObj->convArray($feed, 'utf-8', $GLOBALS['LANG']->charSet, TRUE);
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
		$pid = $conf['config']['storagePid'] ? $conf['config']['storagePid'] : $conf['config']['pid'];
		$catPid = $conf['config']['categoryStoragePid'] ? $conf['config']['categoryStoragePid'] : $pid;
		$item = $guid = array();
		$newcat = 1;
		$defaultCats = $conf['config']['cats'] ? ',' . $conf['config']['cats'] : '';
		$confMapping = is_array($conf['config']['mapping.']) ? is_array($conf['config']['mapping.']) : array();
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
					$category = 'NEWCAT' . $newcat++ . $defaultCats;
					$conf['newscats'][$item['category']] = $category;
				}
				$data['tt_news']['NEW' . $i] = array(
					'pid' => $pid,
					'hidden' => 0,
					'category' => $category,
				);
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
						$data['tt_news'][$key]['category'] = $ret[$tmp[0]] . $defaultCats;
					}
				}
			}
				//write news
			$tce->start($data, array());
			$tce->process_datamap();

			unset($data['tx_ttnewsrssimport_feeds']);
			return array($dataCat, $data);
		} else {
			return array($dataCat, $data);
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
	 * Internal counter, used by getCompleteFeed
	 *
	 * @param array $matches
	 * @return int
	 */
	protected function countItems($matches) {
		return $matches[0] . floor($this->counter++ / 2);
	}
}

?>