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
namespace PCT\CustomElements\Models;

/**
 * Class
 * RatingsModel
 */
class RatingsModel extends \Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pct_customelement_ratings';
	
	
	/**
	 * Find all published ratings by their parent ID and source and attribute ID
	 * @param string	The source
	 * @param integer 	The entry ID
	 * @param integer	The attribute ID
	 * @param array   	An optional options array
	 *
	 * @return \Model\Collection|null
	 */
	public static function findPublishedBySourceAndPidAndAttribute($strSource, $intPid, $intAttribute, $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid=? AND $t.source=? AND $t.attr_id=? AND $t.published=1");
		$arrValues = array($intPid,$strSource,$intAttribute);

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.rating";
		}
		
		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}
	
	
	/**
	 * Find all published ratings by their source and attribute ID and optional a custom condition
	 * @param string	The source
	 * @param integer 	The entry ID
	 * @param string	A custom condition
	 * @param array   	An optional options array
	 *
	 * @return \Model\Collection|null
	 */
	public static function findPublishedBySourceAndAttributeAndCustom($strSource, $intAttribute, $strCustom='', $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.source=? AND $t.attr_id=? AND $t.published=1".(strlen($strCustom) > 0 ? ' AND '.$strCustom : '') );
		$arrValues = array($strSource,$intAttribute);
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.rating";
		}
		
		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}

	
	/**
	 * Calculate the average rating of all published ratings
	 * @param string	The source
	 * @param integer 	The entry ID
	 * @param integer	The attribute ID
	 * @param array   	An optional options array
	 *
	 * @return number
	 */
	public static function averageRatingBySourceAndPidAndAttribute($strSource, $intPid, $intAttribute, array $arrOptions=array())
	{
		$t = static::$strTable;
		$objResult = \Database::getInstance()->prepare("SELECT AVG(rating) as average FROM $t WHERE $t.pid=? AND $t.source=? AND $t.attr_id=? AND $t.published=1")->limit(1)->execute($intPid,$strSource,$intAttribute);
		return $objResult->average ?: 0;
	}
}