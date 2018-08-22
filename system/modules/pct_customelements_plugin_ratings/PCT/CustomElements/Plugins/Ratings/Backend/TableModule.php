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
 * TableModule
 */
class TableModule extends \Backend
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
	public function modifyDca($objDC)
	{
		if($objDC->activeRecord === null)
		{
			$objDC->activeRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);
		}
		
		if($objDC->activeRecord->type != 'customcatalogratings')
		{
			return;
		}
	}
	
	
	/**
	 * Return rating_ Templates
	 * @return array
	 */
	public function getRatingsTemplate()
	{
		return $this->getTemplateGroup('rating');
	}	 
}