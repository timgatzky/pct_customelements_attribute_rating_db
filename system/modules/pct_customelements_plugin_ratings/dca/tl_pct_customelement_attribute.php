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
 * Imports
 */
use PCT\CustomElements\Helper\DcaHelper as DcaHelper;

/**
 * Load language files
 */ 
\System::loadLanguageFile('tl_news_archive');
\System::loadLanguageFile('tl_pct_customelement_attribute');
if(is_array($GLOBALS['TL_LANG']['tl_news_archive']) && is_array($GLOBALS['TL_LANG']['tl_pct_customelement_attribute']))
{
	$GLOBALS['TL_LANG']['tl_pct_customelement_attribute'] = array_merge($GLOBALS['TL_LANG']['tl_pct_customelement_attribute'],$GLOBALS['TL_LANG']['tl_news_archive']);
}


/**
 * Table tl_pct_customelement_attribute
 */
$objDcaHelper = DcaHelper::getInstance()->setTable('tl_pct_customelement_attribute');


/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_pct_customelement_attribute']['config']['onload_callback'][] = array('PCT\CustomElements\Attributes\Tags\TableCustomElementAttribute','setTabletreeOptions');


/**
 * Palettes
 */
$strType = 'rateit';
$arrPalettes = $objDcaHelper->getPalettesAsArray($strType);
#$arrPalettes['comments_legend'][] = 'allowComments';
array_insert($arrPalettes,3,array('ratings_legend' => array('allowRatings') ) );
array_insert($arrPalettes,4,array('comments_legend' => array('allowComments') ) );
$GLOBALS['TL_DCA']['tl_pct_customelement_attribute']['palettes'][$strType] = $objDcaHelper->generatePalettes($arrPalettes);


/**
 * Subpalettes
 */
$objDcaHelper->addSubpalette('allowRatings',array('ratings_notify','ratings_sortOrder','ratings_moderate'));
$objDcaHelper->addSubpalette('allowComments',array('com_notify','com_order','com_perPage','com_moderate','com_bbcode','com_requireLogin','com_disableCaptcha'));


/**
 * Fields
 */
if($objDcaHelper->getActiveRecord()->type == $strType && in_array('ratings',\PCT\CustomElements\Core\PluginFactory::getActivePlugins()))
{
	if(\Input::get('act') == 'edit' && \Input::get('table') == $objDcaHelper->getTable() && $objDcaHelper->getActiveRecord()->allowRatings)
	{
		\Message::reset();
		// Show template info
		\Message::addInfo(sprintf($GLOBALS['TL_LANG']['PCT_CUSTOMCATALOG']['MSC']['templateInfo_attribute'], 'customelement_attr_ratings'));
		
		// hide the internal counter field
		unset($GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['rateit_counter']);		
	}
}

$objDcaHelper->addFields(array
(
	'allowRatings' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['allowRatings'],
		'exclude'                 => true,
		'filter'                  => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('submitOnChange'=>true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'ratings_notify' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['ratings_notify'],
		'default'                 => 'notify_admin',
		'exclude'                 => true,
		'inputType'               => 'select',
		'options'                 => array('notify_admin'),
		'reference'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute'],
		'eval'					  => array('includeBlankOption'=>true,'tl_class'=>''),
		'sql'                     => "varchar(16) NOT NULL default ''"
	),
	'ratings_sortOrder' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['ratings_sortOrder'],
		'default'                 => 'tstamp_asc',
		'exclude'                 => true,
		'inputType'               => 'select',
		'options'                 => array('tstamp_asc', 'tstamp_desc','rating_asc','rating_desc'),
		'reference'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['ratings_sortOrder'],
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "varchar(32) NOT NULL default ''"
	),
	'ratings_perPage' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['ratings_perPage'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('rgxp'=>'natural', 'tl_class'=>'w50'),
		'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
	),
	'ratings_moderate' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['ratings_moderate'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'clr w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	
	// Comments
	'allowComments' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['allowComments'],
		'exclude'                 => true,
		'filter'                  => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('submitOnChange'=>true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'com_notify' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['notify'],
		'default'                 => 'notify_admin',
		'exclude'                 => true,
		'inputType'               => 'select',
		'options'                 => array('notify_admin'),
		'reference'               => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute'],
		'eval'					  => array('includeBlankOption'=>true,'tl_class'=>''),
		'sql'                     => "varchar(16) NOT NULL default ''"
	),
	'com_order' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['sortOrder'],
		'default'                 => 'ascending',
		'exclude'                 => true,
		'inputType'               => 'select',
		'options'                 => array('ascending', 'descending'),
		'reference'               => &$GLOBALS['TL_LANG']['MSC'],
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "varchar(32) NOT NULL default ''"
	),
	'com_perPage' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['perPage'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('rgxp'=>'natural', 'tl_class'=>'w50'),
		'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
	),
	'com_moderate' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['moderate'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'com_bbcode' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['bbcode'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'com_requireLogin' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['requireLogin'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'com_disableCaptcha' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['disableCaptcha'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	)
));

