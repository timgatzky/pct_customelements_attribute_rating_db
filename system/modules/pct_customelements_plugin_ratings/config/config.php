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
	 * Register filter
	 */
	$GLOBALS['PCT_CUSTOMELEMENTS']['FILTERS']['ratings'] = array
	(
		'label'		=> &$GLOBALS['TL_LANG']['PCT_CUSTOMELEMENTS']['FILTERS']['ratings'],
		'path' 		=> PCT_CUSTOMELEMENTS_RATINGS_PATH,
		'class'		=> 'PCT\CustomElements\Filters\Ratings',
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
if(TL_MODE == 'BE')
{
	$GLOBALS['TL_HOOKS']['loadDataContainer'][] 				= array('PCT\CustomElements\Backend\TableCustomElementTags','loadAssets');
}

#$GLOBALS['CUSTOMELEMENT_HOOKS']['renderField'][] 			= array('PCT\CustomElements\Attributes\Tags','prepareField');
$GLOBALS['CUSTOMELEMENTS_HOOKS']['prepareRendering'][] 		= array('PCT\CustomElements\Plugins\Ratings','prepareRenderingCallback');
$GLOBALS['TL_HOOKS']['addComment'][] 						= array('PCT\CustomElements\Plugins\Ratings','addCommentCallback');
#$GLOBALS['CUSTOMELEMENTS_HOOKS']['processWildcardValue'][] 	= array('PCT\CustomElements\Attributes\Tags','processWildcardValue');