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
namespace PCT\CustomElements\Plugins;

/**
 * Class file
 * Ratings
 */
class Ratings
{
	/**
	 * Listen to ajax requests from RateIt attribute
	 * @param string
	 * @param string
	 * @param mixed
	 * @param array
	 * @param object
	 * 
	 * called from prepareRendering Hook
	 */
	public function prepareRenderingCallback($strField,$varValue,$objTemplate,$objAttribute)
	{
		if($objAttribute->get('type') != 'rateit' && !$objAttribute->get('allowRatings'))
		{
			return '';
		}
		
		$intPid = $objAttribute->getOrigin()->get('pid');
		$strSource = $objAttribute->getOrigin()->get('table').'_'.$objAttribute->get('id');
		$strTable = $objAttribute->getOrigin()->get('table');
		
		// new rating via comments
		if($objAttribute->get('allowComments') && in_array('comments', \ModuleLoader::getActive()))
		{
			// Adjust the comments headline level
			$intHl = min(intval(str_replace('h', '', $this->hl)), 5);
			$objTemplate->hlc = 'h' . ($intHl + 1);

			$objTemplate->allowComment = true;
			$objTemplate->source = $strTable;
			$objTemplate->attr_id = $objAttribute->get('id');
			$objTemplate->pid = $intPid;
			
			$objComments = new \Contao\Comments();
			
			// Notify the system administrator
			$arrNotifies = array($GLOBALS['TL_ADMIN_EMAIL']);
			
			// Notify a different person
			if(strlen($objAttribute->get('com_notify')) > 0 && $objAttribute->get('com_notify') != 'notify_admin')
			{
				$arrNotifies = array($objAttribute->get('com_notify'));
			}
		
			$objConfig = new \StdClass();
			$objConfig->perPage = $objAttribute->get('com_perPage');
			$objConfig->order = $objAttribute->get('com_sortOrder');
			$objConfig->requireLogin = $objAttribute->get('com_requireLogin');
			$objConfig->disableCaptcha = $objAttribute->get('com_disableCaptcha');
			$objConfig->bbcode = $objAttribute->get('com_bbcode');
			$objConfig->moderate = $objAttribute->get('com_moderate');
			
			$objComments->addCommentsToTemplate($objTemplate, $objConfig, $strSource, $intPid, $arrNotifies);
		}
		
		// new rating via ajax
		if( \Input::post('attr_id') == $objAttribute->get('id') && \Environment::get('isAjaxRequest') )
		{
			$time = time();
			$rating = \Input::post('value');
				
			$this->addNewRating(\Input::post('value'),$strSource,$intPid,$objAttribute->get('id'),($objAttribute->get('ratings_moderate') ? '' : 1));
		}
		
		
		
		// return empty to bypass the hook return value
		return '';
	}
	
	
	protected function addNewRating($varValue,$strSource,$intPid,$intAttribute,$blnPublished=false,$intComment=0)
	{
		$time = time();
			
		// Prepare the record
		$arrSet = array
		(
			'tstamp'    => $time,
			'source'    => $strSource,
			'pid'    	=> $intPid,
			'comment'   => $intComment,
			'rating'	=> $varValue,
			'attr_id'	=> $intAttribute,
			'published' => $blnPublished
		);
		
		// calc counter
		
		$objModel = new \PCT\CustomElements\Models\RatingsModel();
		$objModel->setRow($arrSet)->save();
	}
	
	
	/**
	 * Connect a new comment to the rating
	 * @param integer
	 * @param array
	 * @param object
	 */
	public function addCommentCallback($intComment, $arrSet, $objComment)
	{
		if( \Input::post('PCT_CUSTOMELEMENTS_PLUGIN_RATINGS') == '' )
		{
			return;
		}
		
		$objAttribute = \PCT\CustomElements\Core\AttributeFactory::findById( \Input::post('attr_id') );
		
		if($objAttribute->get('type') != 'rateit' && !$objAttribute->get('allowRatings'))
		{
			return '';
		}
		
		$this->addNewRating(\Input::post('value'),\Input::post('source'),\Input::post('pid'),$objAttribute->get('id'),($objAttribute->get('ratings_moderate') ? '' : 1), $intComment);
	}
}