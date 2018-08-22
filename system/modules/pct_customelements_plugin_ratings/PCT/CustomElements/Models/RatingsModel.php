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
	public static function findPublishedBySourceAndPidAndAttribute($strSource, $intPid, $intAttribute, array $arrOptions=array())
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
}