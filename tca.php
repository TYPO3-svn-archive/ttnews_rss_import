<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Create wizard configuration:
$wizConfig = array(
	'type' => 'userFunc',
	'userFunc' => 'EXT:ttnews_rss_import/classes/class.tx_ttnewsrssimport_tceforms_hooks.php:tx_ttnewsrssimport_tceforms_hooks->wizard',
	'params' => array()
);

$TCA['tx_ttnewsrssimport_feeds'] = array(
	'ctrl' => $TCA['tx_ttnewsrssimport_feeds']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,url,updateinterval,synchronizerss,lastimport,errors,lasterrorstring,lastimportrss,newsrecordpid,newscategory'
	),
	'feInterface' => $TCA['tx_ttnewsrssimport_feeds']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			)
		),
		'url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.url',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			)
		),
		'updateinterval' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.updateinterval',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'int',
				'default'  => '7200'
			)
		),
		'synchronizerss' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.synchronizerss',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			)
		),
		'lastimport' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.lastimport',
			'config' => array(
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'lastimportrss' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.lastimportrss',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'newsrecordpid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.newsrecordpid',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => '1',
				'maxitems' => '1',
				'minitems' => '0',
				'show_thumbs' => '1',
				'wizards' => array(
					'suggest' => array(
						'type' => 'suggest',
					),
				),
			)
		),
		'newscategory' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.newscategory',
			'config' => array(
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'tx_ttnews_TCAform_selectTree->renderCategoryFields',
				'treeView' => 1,
				'foreign_table' => 'tt_news_cat',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 500,
			),
		),
		'newcategoryparent' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.newcategoryparent',
			'config' => array(
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'tx_ttnews_TCAform_selectTree->renderCategoryFields',
				'treeView' => 1,
				'foreign_table' => 'tt_news_cat',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 2,
			),
		),
		'mapping' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.mapping',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'getDefault' => array(
						'title' => 'Get Default Mapping',
						'type' => 'userFunc',
						'userFunc' => 'EXT:ttnews_rss_import/classes/class.tx_ttnewsrssimport_tceforms_hooks.php:tx_ttnewsrssimport_tceforms_hooks->renderMappingWizard',

					)
				),
			)
		),
		'wizOutput' => array(
			'label' => 'Importer Wizard',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'EXT:ttnews_rss_import/classes/class.tx_ttnewsrssimport_tceforms_hooks.php:tx_ttnewsrssimport_tceforms_hooks->wizard',
			),
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, url;;2;;2-2-2, synchronizerss;;;;2-2-2,
			--div--;LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.tabs.news, newsrecordpid, newscategory,newcategoryparent,
			--div--;LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.tabs.import, lastimport, lastimportrss,
			--div--;LLL:EXT:ttnews_rss_import/locallang.xml:tx_ttnewsrssimport_feeds.tabs.wizard, mapping, wizOutput')
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'updateinterval')
	)
);
?>