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
namespace PCT\CustomElements\Filters;


/**
 * Imports
 */
use PCT\CustomElements\Models\RatingsModel;


/**
 * Class file
 * Ratings
 */
class Ratings extends \PCT\CustomElements\Filters\Range
{
	/**
	 * Init
	 */
	public function __construct($arrData=array())
	{
		$this->setData($arrData);
		
		// fetch the attribute the filter works on
		$this->objAttribute = \PCT\CustomElements\Core\AttributeFactory::findById($this->get('attr_id'));
		// point the filter to the target field
		$this->setFilterTarget(RatingsModel::getTable().'.'.$GLOBALS['PCT_CUSTOMELEMENTS']['FILTERS']['ratings']['field'] ?: 'rating');
	
		// use the filter title or use the urlparameter as filter name
		$name = $this->get('urlparam') ? $this->get('urlparam') : standardize($this->get('title'));
		
		// set the filter name
		$this->setName($name);
	}
	
	
	/**
	 * Prepare the sql query array for this filter and return it as array
	 * @return array
	 * 
	 * called from getQueryOption() in \PCT\CustomElements\Filter
	 */	
	public function getQueryOptionCallback()
	{
		$varValue = array_filter($this->getValue() ?: array(),'strlen');
		
		if(empty($varValue))
		{
			return array();
		}
		
		// single values
		if($this->get('mode') != 'between' && is_array($varValue))
		{
			$varValue = implode('', $varValue);
		}
		
		$t = $this->getFilterTarget();
		$where = '';
		switch($this->get('mode'))
		{
			case 'ht': default:
				$where = $t.'>'.$varValue;
				break;
			case 'hte':
				$where = $t.'>='.$varValue;
				break;
			case 'lte':
				$where = $t.'<='.$varValue;
				break;
			case 'lt':
				$where = $t.'<'.$varValue;
				break;
			case 'between':
				if(strlen($varValue[0]) < 1) {$varValue[0] = $this->get('min_value');}
				if(strlen($varValue[1]) < 1) {$varValue[1] = $this->get('max_value');}
				$where = $t.' BETWEEN '.$varValue[0].' AND '.$varValue[1];
				break;
		}
		
		// find matching rating records
		$objRatings = RatingsModel::findPublishedBySourceAndAttributeAndCustom($this->getTable(),$this->get('attr_id'),$where);
		
		if($objRatings === null)
		{
			return array();
		}
		
		$arrOptions = array
		(
			'column'	=> 'id',
			'operation'	=> 'IN',
			'value'		=> $objRatings->fetchEach('pid')
		);
		
		return $arrOptions;
	}
}