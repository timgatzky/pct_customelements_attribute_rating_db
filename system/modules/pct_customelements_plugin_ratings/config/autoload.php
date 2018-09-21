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


$path = 'system/modules/pct_customelements_plugin_ratings';

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'PCT\CustomElements',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'PCT\CustomElements\Filters\Ratings'										=> $path.'/PCT/CustomElements/Filters/Ratings/Ratings.php',	
	'PCT\CustomElements\Filters\RatingsSorting'									=> $path.'/PCT/CustomElements/Filters/Ratings/RatingsSorting.php',	
	'PCT\CustomElements\Models\RatingsModel'									=> $path.'/PCT/CustomElements/Models/RatingsModel.php',
	'PCT\CustomElements\Plugins\Ratings\Frontend\ModuleReader'					=> $path.'/PCT/CustomElements/Plugins/Ratings/Frontend/ModuleReader.php',
	'PCT\CustomElements\Plugins\Ratings'										=> $path.'/PCT/CustomElements/Plugins/Ratings/Ratings.php',	
	'PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementRatings'		=> $path.'/PCT/CustomElements/Plugins/Ratings/Backend/TableCustomElementRatings.php',	
	'PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementAttribute'	=> $path.'/PCT/CustomElements/Plugins/Ratings/Backend/TableCustomElementAttribute.php',	
	'PCT\CustomElements\Plugins\Ratings\Backend\TableModule'					=> $path.'/PCT/CustomElements/Plugins/Ratings/Backend/TableModule.php',	
	'Contao\PCT_RatingsModel'													=> $path.'/Contao/PCT_RatingsModel.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'customelement_attr_ratings'				=> $path.'/templates/attributes',
	'customcatalog_filter_ratings'				=> $path.'/templates/filters',
	'customcatalog_filter_ratings_sorting'		=> $path.'/templates/filters',
	'form_comment_ratings'						=> $path.'/templates/forms',
	'form_comment_ratings_simple'				=> $path.'/templates/forms',
	'mod_customcatalog_ratings' 				=> $path.'/templates/modules',
	'rating_default'							=> $path.'/templates/ratings',
	'rating_simple'								=> $path.'/templates/ratings',

));