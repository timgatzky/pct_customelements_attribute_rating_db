<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements
 * @subpackage	pct_customelements_plugin_customcatalog
 * @link		http://contao.org
 */

/**
 * Imports
 */
use PCT\CustomElements\Plugins\CustomCatalog\Helper\DcaHelper as DcaHelper;

/**
 * Table tl_module
 */
$objDcaHelper = DcaHelper::getInstance()->setTable('tl_module');
$objActiveRecord = $objDcaHelper->getActiveRecord();


/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('PCT\CustomElements\Plugins\Ratings\Backend\TableModule', 'modifyDca');


/**
 * Palettes
 */
// customcatalogratings
$strType = 'customcatalogratings';
$arrPalettes = $objDcaHelper->getPalettesAsArray('default');
array_insert($arrPalettes['title_legend'],1,'headline');
$arrPalettes['config_legend'] 				= array('customcatalog','customcatalog_visibles');
$arrPalettes['list_legend']					= array('customcatalog_limit','customcatalog_offset','customcatalog_perPage','customcatalog_sortField','customcatalog_sorting');
$arrPalettes['comment_legend:hide'] 		= array('com_order','perPage','com_moderate','com_bbcode','com_protected','com_requireLogin','com_disableCaptcha');
$arrPalettes['template_legend:hide'] 		= array('customcatalog_template','customTpl','com_template');
$arrPalettes['advanced_sql_legend:hide'] 	= array('customcatalog_sqlSorting');
$arrPalettes['protected_legend:hide'] 		= array('protected');
$arrPalettes['expert_legend:hide'] 			= array('guests','cssID','space');
$GLOBALS['TL_DCA']['tl_module']['palettes'][$strType] = $objDcaHelper->generatePalettes($arrPalettes);

if($objActiveRecord->type == $strType)
{
	// customcatalog_visibles
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_visibles']['label'] = &$GLOBALS['TL_LANG']['tl_module']['customcatalog_visibles_ratings'];
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_visibles']['inputType'] = 'select';
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_visibles']['eval']['multiple'] = false;
	// customcatalog_sortField 
	unset($GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_sortField']['options_callback']);
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_sortField']['options'] = array('tstamp','rating','helpful','counter');
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_sortField']['reference'] = &$GLOBALS['TL_LANG']['tl_module']['customcatalog_sortField_ratings'];
	// customcatalog_template
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_template']['default'] = 'rating_default';
	$GLOBALS['TL_DCA'][$objDcaHelper->getTable()]['fields']['customcatalog_template']['options_callback'] = array('PCT\CustomElements\Plugins\Ratings\Backend\TableModule','getRatingsTemplate');
}