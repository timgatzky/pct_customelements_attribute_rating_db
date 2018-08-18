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
 * Namespace
 */
namespace PCT\CustomElements\Plugins\Ratings\Backend;

/**
 * Class file
 * TableCustomElementTags
 */
class TableCustomElementRatings extends \Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	
	/**
	 * Render the tag list record
	 * @param array
	 * @param string
	 * @return string
	 */
	public function listRecord($arrRow, $strLabel)
	{
		if(strlen($arrRow['translations']) > 0)
		{
			$arrTranslations = deserialize($arrRow['translations']);
			$lang = \Input::get('language') ?: \Input::get('lang') ?: $GLOBALS['TL_LANGUAGE'];
			$strLabel = $arrTranslations[$lang]['label'] ?: $strLabel;
		}
		
		return '<a href="'.$this->addToUrl('node='.$arrRow['id']).'" title="'.$GLOBALS['TL_LANG']['MSC']['selectNode'].'">'.$strLabel.'</a>';
	}
	

	/**
	 * Return the comments button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function getCommentsButton($row, $href, $label, $title, $icon, $attributes)
	{
		if($row['comment'] < 1)
		{
			$icon = str_replace('comments','comments_',$icon);
			return '<span title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</span> ';
		}
		
		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['comment']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
	}
	
	
	/**
	 * Return the attribute button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function getAttributeButton($row, $href, $label, $title, $icon, $attributes)
	{
		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['attr_id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
	}
	
	
	/**
	 * Return the "toggle visibility" button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function getToggleButton($row, $href, $label, $title, $icon, $attributes)
	{
		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if(!$row['published'])
		{
			$icon = 'invisible.'.(version_compare(VERSION, '4.4','>=') ? 'svg' : 'gif');
		}
		
		if(\Input::get('tid') != '')
		{
			\Database::getInstance()->prepare("UPDATE tl_pct_customelement_ratings %s WHERE id=?")->set( array('published' => ($row['published'] ? 0 : 1)) )->execute( \Input::get('tid') );
		}
		
		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label, 'data-state="' . (!$row['published'] ? 0 : 1) . '"').'</a> ';
	}
	
	/**
	 * Load assets
	 */
	public function loadAssets()
	{
		if(version_compare(VERSION, '4','>='))
		{
			$GLOBALS['TL_CSS'][] = PCT_CUSTOMELEMENTS_TAGS_PATH.'/assets/css/styles.css';
		}
		else
		{
			$GLOBALS['TL_CSS'][] = PCT_CUSTOMELEMENTS_TAGS_PATH.'/assets/css/styles_c3.css';
		}
	}
}