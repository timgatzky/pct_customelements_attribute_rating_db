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
 * Table tl_pct_customelement_filter
 */
$objDcaHelper = \PCT\CustomElements\Plugins\CustomCatalog\Helper\DcaHelper::getInstance()->setTable('tl_pct_customelement_filter');

/**
 * Load language file
 */
\PCT\CustomElements\Loader\FilterLoader::loadLanguageFile($objDcaHelper->getTable(),$strType);


/**
 * Palettes
 */
// ratings
$strType = 'ratings';
$arrPalettes = $objDcaHelper->getPalettesAsArray('default');
$arrPalettes['settings_legend'][] = 'attr_id';
$arrPalettes['settings_legend'][] = 'label';
$arrPalettes['settings_legend'][] = 'min_value';
$arrPalettes['settings_legend'][] = 'max_value';
$arrPalettes['settings_legend'][] = 'steps_value';
$arrPalettes['settings_legend'][] = 'mode';
$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['palettes'][$strType] = $objDcaHelper->generatePalettes($arrPalettes);

if($objDcaHelper->getActiveRecord()->type == $strType)
{
	if(\Input::get('act') == 'edit' && \Input::get('table') == $objDcaHelper->getTable())
	{
		// Show template info
		\Message::addInfo(sprintf($GLOBALS['TL_LANG']['PCT_CUSTOMCATALOG']['MSC']['templateInfo_filter'], 'customcatalog_filter_ratings'));
	}
	
	// set the template selection default value
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['template']['default'] = 'customcatalog_filter_ratings';
	
	// set attribute selection to number attributes only
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['attr_id']['options_values'] = array('rateit','text');

	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['mode']['label'] = $GLOBALS['TL_LANG']['tl_pct_customelement_filter']['range_mode'];
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['mode']['inputType'] = 'select';
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['mode']['options'] = array('ht','hte','lt','lte','between');
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['mode']['reference'] = $GLOBALS['TL_LANG']['tl_pct_customelement_filter']['range_mode'];
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['mode']['eval'] = array('tl_class'=>'clr','chosen'=>true);	
}

// ratings_sorting
$strType = 'ratings_sorting';
$arrPalettes = $objDcaHelper->getPalettesAsArray('default');
$arrPalettes['settings_legend'][] = 'attr_id';
$arrPalettes['settings_legend'][] = 'includeReset';
$arrPalettes['settings_legend'][] = 'isRadio';
$arrPalettes['settings_legend'][] = 'defaultMulti';
$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['palettes'][$strType] = $objDcaHelper->generatePalettes($arrPalettes);
if($objDcaHelper->getActiveRecord()->type == $strType)
{
	if(\Input::get('act') == 'edit' && \Input::get('table') == $objDcaHelper->getTable())
	{
		// Show template info
		\Message::addInfo(sprintf($GLOBALS['TL_LANG']['PCT_CUSTOMCATALOG']['MSC']['templateInfo_filter'], 'customcatalog_filter_ratings_sorting'));
	}
	
	// set the template selection default value
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['template']['default'] = 'customcatalog_filter_select';
	
	// let user choose the fields
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['defaultMulti']['label'] = &$GLOBALS['TL_LANG']['tl_pct_customelement_filter']['defaultMulti']['ratings_sorting'];
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['defaultMulti']['inputType'] = 'checkboxWizard';
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['defaultMulti']['options'] = array('tstamp','rating','helpful','not_helpful');
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['defaultMulti']['reference'] = &$GLOBALS['TL_LANG']['tl_pct_customelement_filter']['defaultMulti']['ratings_sorting'];
	$GLOBALS['TL_DCA']['tl_pct_customelement_filter']['fields']['defaultMulti']['eval'] = array('tl_class'=>'clr','multiple'=>true);
}