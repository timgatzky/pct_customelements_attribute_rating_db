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
 * Constants
 */
define('PCT_CUSTOMELEMENTS_RATINGS_PATH', 'system/modules/pct_customelements_plugin_ratings');
define('PCT_CUSTOMELEMENTS_RATINGS_VERSION', '1.0.0');

$blnInstallTool = true;
if(strlen(strpos(\Environment::getInstance()->scriptName, '/contao/install.php')) > 0 || strlen(strpos(\Environment::getInstance()->requestUri, '/contao/install')) > 0)
{
	$blnInstallTool = true;
}

$blnInitialize = true;
if( !in_array('pct_customelements_plugin_customcatalog', \Config::getInstance()->getActiveModules()) || !class_exists('Database',true) )
{
	$blnInitialize = false;
}

if($blnInstallTool == false && count(\Session::getInstance()->getData()) > 0)
{
	// plugin not active
	if( !in_array('ratings',\PCT\CustomElements\Core\PluginFactory::getActivePlugins()) )
	{
		$blnInitialize = false;
	}
}


/**
 * Globals
 */
$GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['urlPaginationParameter'] = 'page_r';
$GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['urlRatingFilterParameter'] = 'rating_%s';
if(!isset($GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['maxRatingsPerMember']))
{
	$GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['maxRatingsPerMember'] = 1; // define the number of ratings per member allowed
}
if(!isset($GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['reloadAfterSubmit']))
{
	$GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['reloadAfterSubmit'] = 1; // reload page after form submits like: helpful, not helpful, delete
}


/**
 * Register the plugin
 */
$GLOBALS['PCT_CUSTOMELEMENTS']['PLUGINS']['ratings'] = array();

if($blnInitialize)
{
	/**
	 * Back end modules
	 */
	array_insert($GLOBALS['BE_MOD']['content'], count($GLOBALS['BE_MOD']['content']), array
	(
		'pct_customelements_ratings' => array
		(
			'tables' 		=> array('tl_pct_customelement_ratings'),
			'icon' 			=> PCT_CUSTOMELEMENTS_RATINGS_PATH.'/assets/img/icon.png',
		)
	));
	
	
	/**
	 * Front end modules
	 */
	$GLOBALS['FE_MOD']['pct_customcatalog_node']['customcatalogratings'] = 'PCT\CustomElements\Plugins\Ratings\Frontend\ModuleReader';
	
	
	/**
	 * Register filter
	 */
	$GLOBALS['PCT_CUSTOMELEMENTS']['FILTERS']['ratings'] = array
	(
		'label'		=> &$GLOBALS['TL_LANG']['PCT_CUSTOMELEMENTS']['FILTERS']['ratings'],
		'path' 		=> PCT_CUSTOMELEMENTS_RATINGS_PATH,
		'class'		=> 'PCT\CustomElements\Filters\Ratings',
		'icon'		=> 'fa fa-star',
		'field' 	=> 'rating' // default filter field in tl_pct_customelement_ratings
	);
	$GLOBALS['PCT_CUSTOMELEMENTS']['FILTERS']['ratings_sorting'] = array
	(
		'label'		=> &$GLOBALS['TL_LANG']['PCT_CUSTOMELEMENTS']['FILTERS']['ratings_sorting'],
		'path' 		=> PCT_CUSTOMELEMENTS_RATINGS_PATH,
		'class'		=> 'PCT\CustomElements\Filters\RatingsSorting',
		'icon'		=> 'fa fa-star',
	);
}


/**
 * Register the model classes
 */
$GLOBALS['TL_MODELS']['tl_pct_customelement_ratings'] = 'Contao\PCT_RatingsModel';


/**
 * Hooks
 */
$GLOBALS['CUSTOMELEMENTS_HOOKS']['prepareRendering'][] 		= array('PCT\CustomElements\Plugins\Ratings','prepareRenderingCallback');
$GLOBALS['TL_HOOKS']['addComment'][] 						= array('PCT\CustomElements\Plugins\Ratings','addCommentCallback');
