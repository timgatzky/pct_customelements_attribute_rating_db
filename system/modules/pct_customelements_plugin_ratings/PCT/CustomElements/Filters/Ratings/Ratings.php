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
 * Class file
 * Ratings
 */
class Ratings extends \PCT\CustomElements\Filter
{
	/**
	 * Init
	 */
	public function __construct($arrData=array())
	{
		$this->setData($arrData);
		
		// fetch the attribute the filter works on
		$this->objAttribute = \PCT\CustomElements\Core\AttributeFactory::findById($this->get('attr_id'));
		// point the filter to the attribute
		$this->setFilterTarget($this->objAttribute->alias);
	
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
		
		$t = $this->getTable().'.'.$this->getFilterTarget();
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
		
		$options = array
		(
			'column'	=> $t,
			'where'		=> '('.$where.')',
		);
		return $options;
	}
	
	
	/**
	 * Render the filter and return string
	 * @param string	Name of the attribute
	 * @param mixed		Active filter values
	 * @param object	Template object
	 * @param object	The current filter object
	 * @return string
	 */
	public function renderCallback($strName,$varValue,$objTemplate,$objFilter)
	{
		$objTemplate->name = $strName;
		$objTemplate->label = $this->get('label') ?:  $this->get('title');
		
		$objTemplate->minValue = $objTemplate->actMinValue = $this->get('min_value');
		$objTemplate->maxValue = $objTemplate->actMaxValue = $this->get('max_value');
		$objTemplate->stepValue = $this->get('steps_value') ?: 1;
		
		// use jquery slider and split the submitted value
		if($this->get('mode') == 'between')
		{
			$objTemplate->actMinValue = $varValue[0] ?: $objTemplate->minValue;
			$objTemplate->actMaxValue = $varValue[1] ?: $objTemplate->maxValue;
			$objTemplate->useJquery = true;
		}
		else
		{
			$varValue = $varValue[0];
		}
		
		$objTemplate->value = $varValue;
			
		return $objTemplate->parse();
	}
}