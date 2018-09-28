<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2018, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		tl_pct_customelement_ratings
 * @link		http://contao.org
 * @license     LGPL
 */

/**
 * Load language files
 */ 
\System::loadLanguageFile('tl_comments');
\System::loadLanguageFile('tl_pct_customelement_ratings');
if(is_array($GLOBALS['TL_LANG']['tl_comments']) && is_array($GLOBALS['TL_LANG']['tl_pct_customelement_ratings']))
{
	$GLOBALS['TL_LANG']['tl_pct_customelement_ratings'] = array_merge($GLOBALS['TL_LANG']['tl_comments'],$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']);
}


/**
 * Table tl_pct_customelement_ratings
 */
$GLOBALS['TL_DCA']['tl_pct_customelement_ratings'] = array
(
	// Config
	'config' => array
	(
		'label'                       => $GLOBALS['TL_LANG']['tl_pct_customelement_tags']['config']['label'] ? $GLOBALS['TL_LANG']['tl_pct_customelement_tags']['config']['label'] : 'Tags',
		'dataContainer'				  => 'Table',
		'closed'                      => true,
		'notCopyable'                 => true,
		'ondelete_callback'			  => array
		(
			array('PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementRatings','deleteRelatedComment'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' 	=> 'primary',
			)
		),
	),
		// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('tstamp DESC','source','rating','helpful','not_helpful'),
			'icon'                    => PCT_CUSTOMELEMENTS_RATINGS_PATH.'/assets/img/icon.png',
			'flag'                    => 8,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('source','pid','rating','helpful','not_helpful'),
			'format'                  => '%s.id=%s | Sterne: <b>%s</b> | Hilfreich: <b>%s</b> | Nicht hilfreich: <b>%s</b>',
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			),
		),
		'operations' => array
		(
			'comments' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['comments'],
				'href'                => 'do=comments&act=edit&rt='.REQUEST_TOKEN,
				'icon'                => PCT_CUSTOMELEMENTS_RATINGS_PATH.'/assets/img/comments.'.(version_compare(VERSION, '4.4','>=') ? 'svg' : 'gif'),
				'button_callback'      => array('PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementRatings', 'getCommentsButton')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_comments']['toggle'],
				'icon'                => 'visible.'.(version_compare(VERSION, '4.4','>=') ? 'svg' : 'gif'),
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementRatings', 'getToggleButton')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.'.(version_compare(VERSION, '4.4','>=') ? 'svg' : 'gif')
			),
			'attribute' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['attribute'],
				'href'                => 'do=pct_customelements&table=tl_pct_customelement_attribute&act=edit&rt='.REQUEST_TOKEN,
				'icon'                => PCT_CUSTOMELEMENTS_RATINGS_PATH.'/assets/img/attribute.'.(version_compare(VERSION, '4.4','>=') ? 'svg' : 'gif'),
				'button_callback'      => array('PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementRatings', 'getAttributeButton')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_tags']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.'.(version_compare(VERSION, '4.4','>=') ? 'svg' : 'gif'),
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			)
		)
	),
	// Fields
	'fields' => array
	(
		'id' => array
		(
			'eval'					  => array('doNotCopy'=>true),
			'sql'                     => "int(10) unsigned NOT NULL auto_increment",
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'tstamp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['tstamp'],
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'eval'                    => array('rgxp'=>'datim'),
			'eval'					  => array('doNotCopy'=>true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'attr_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['attr_id'],
			'filter'                  => true,
			'sorting'                 => true,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'source' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_comments']['source'],
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 3,
			'reference'               => &$GLOBALS['TL_LANG']['tl_comments'],
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('doNotCopy'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'counter' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'rating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['rating'],
			'sorting'                 => true,
			'filter'                  => true,
			'eval'                    => array('rgxp'=>'alnum'),
			'sql'                     => "varchar(32) NOT NULL default ''",
		),
		'comment' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['comment'],
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'member' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['member'],
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'helpful' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['helpful'],
			'exclude'                 => true,
			#'filter'                  => true,
			'sorting'				  => true,
			'inputType'               => 'input',
			'eval'					  => array('readonly'=>true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'not_helpful' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_ratings']['not_helpful'],
			'exclude'                 => true,
			#'filter'                  => true,
			'sorting'				  => true,
			'inputType'               => 'input',
			'eval'					  => array('readonly'=>true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
	)
);

if($GLOBALS['TL_LANGUAGE'] != 'de')
{
	$GLOBALS['TL_DCA']['tl_pct_customelement_ratings']['list']['label']['format'] = '%s.id=%s | Stars: <b>%s</b> | Helpful: <b>%s</b> | Not helpful: <b>%s</b>';	
}
