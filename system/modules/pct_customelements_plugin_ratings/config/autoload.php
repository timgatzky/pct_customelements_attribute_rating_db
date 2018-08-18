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
	'PCT\CustomElements\Plugins\Ratings'								=> $path.'/PCT/CustomElements/Plugins/Ratings/Ratings.php',	
	'PCT\CustomElements\Plugins\Ratings\Backend\TableCustomElementRatings'	=> $path.'/PCT/CustomElements/Plugins/Ratings/Backend/TableCustomElementRatings.php',	
	'PCT\CustomElements\Filters\Ratings'									=> $path.'/PCT/CustomElements/Filters/Tags/Tags.php',	
	'Contao\PCT_RatingsModel'											=> $path.'/Contao/PCT_RatingsModel.php',
	'PCT\CustomElements\Models\RatingsModel'							=> $path.'/PCT/CustomElements/Models/RatingsModel.php',	
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'customelement_attr_ratings'		=> $path.'/templates',
	'customcatalog_filter_ratings'		=> $path.'/templates',
	'form_comment_ratings'				=> $path.'/templates',
));