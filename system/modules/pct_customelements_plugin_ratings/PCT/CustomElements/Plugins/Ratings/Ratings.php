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
 * Imports
 */
use PCT\CustomElements\Models\RatingsModel;
use Contao\Controller;


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
		if( $objAttribute->get('type') != 'rateit' || ($objAttribute->get('type') == 'rateit' && !$objAttribute->get('allowRatings')) )
		{
			return '';
		}
		
		$intPid = $objAttribute->getActiveRecord()->id;
		$strSource = $objAttribute->getOrigin()->get('table').'_'.$objAttribute->get('id');
		$strTable = $objAttribute->getOrigin()->get('table');
		$intAttribute = $objAttribute->get('id');
		$intRating = 0;
		$objConfig = null;
		$objTemplate->allowVoting = true;
		// allow voting
		if( (boolean)$objAttribute->get('ratings_requireLogin') === true && FE_USER_LOGGED_IN === false)
		{
			$objTemplate->allowVoting = false;
		}
		
		// create Comments config
		if($objAttribute->get('allowComments') && in_array('comments', \ModuleLoader::getActive()))
		{
		   // Adjust the comments headline level
		   $objTemplate->hlc = 'h4';
		   $objTemplate->allowComment = true;
		   $objTemplate->source = $strTable;
		   $objTemplate->attr_id = $objAttribute->get('id');
		   $objTemplate->pid = $intPid;
		   
		   // @var object
		   $objComments = new \Contao\Comments();
		   
		   // Notifies
		   $arrNotifies = array();
		   
		   // Notify admin
		   if($objAttribute->get('com_notify') == 'notify_admin')
		   {
		   	$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
		   }
		   
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
		   $objConfig->notifies = $arrNotifies;
		   
		   $objComments->addCommentsToTemplate($objTemplate, $objConfig, $strSource, $intPid, $arrNotifies);
		}
		
		// add new rating via ajax
		if( \Input::get('attr_id') == $objAttribute->get('id') && (boolean)\Environment::get('isAjaxRequest') === true)
		{
			$intRating = $this->addNewRating(\Input::get('value'),$strTable,$intPid,$intAttribute,($objAttribute->get('ratings_moderate') ? '' : 1));
		}
		
		// send notification
		if($intRating > 0 && $objAttribute->get('ratings_notify') != '')
		{
			$objRating = \PCT\CustomElements\Models\RatingsModel::findByPk($intRating);
			
			// Prepare the notification mail
			$objEmail = new \Email();
			$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
			$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
			$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['ratings_subject'], \Idna::decode(\Environment::get('host')));
			$objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['ratings_message'],'', $objRating->rating, $objRating->source, $objRating->pid);
									  
			// Notifies
			$arrNotifies = array();
			
			// Notify admin
			if($objAttribute->get('ratings_notify') == 'notify_admin')
			{
				$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
			}
			
			// Notify a different person
			if(strlen($objAttribute->get('ratings_notify')) > 0 && $objAttribute->get('ratings_notify') != 'notify_admin')
			{
				$arrNotifies = array($objAttribute->get('com_notify'));
			}
			
			$objEmail->sendTo(array_unique($arrNotifies));
		}

		// reload the page inside the ajax request when a new rating has been placed
		if( $intRating > 0 && (boolean)\Contao\Environment::get('isAjaxRequest') === true )
		{
			Controller::reload();
		}
		
		// add ratings to template
		$this->addRatingsToTemplate($objTemplate,$strTable,$intPid,$intAttribute,$objConfig);
		
		// return empty to bypass the hook return value
		return '';
	}
	
	
	/**
	 * Render the ratings
	 * @param object	The ModelCollection of rating records
	 * @param string	The rating template
	 * @param object	The configuration object for the comments
	 * @return string
	 */
	public static function renderRatings($objRatings,$strTemplate='rating_default',$objConfigComments=null)
	{
		if( empty($strTemplate) )
		{
			$strTemplate = 'rating_default';
		}
		
		// @var object Comments
		$objComments = new \Comments();
		
		$arrReturn = array();
		
		$intTotal = null;
		foreach($objRatings as $i => $objRating)
		{
			if($intTotal === null)
			{
				$intTotal = RatingsModel::countBy(array('source=? AND pid=? AND attr_id=?'),array($objRating->source,$objRating->pid,$objRating->attr_id));
			}
			
			// @var object
			$objAttribute = \PCT\CustomElements\Plugins\CustomCatalog\Core\AttributeFactory::findById( $objRating->attr_id );
			
			$objTemplate = new \FrontendTemplate( $strTemplate );
			$objTemplate->Rating = $objRating;
			$objTemplate->rating = $objRating->rating;
			$objTemplate->attr_id = $objRating->attr_id;
			$objTemplate->source = $objRating->source;
			$objTemplate->pid = $objRating->pid;
			$objTemplate->tstamp = $objRating->tstamp;
			$objTemplate->datetime = \System::parseDate( \Config::get('datimFormat'), $objRating->tstamp );
			$objTemplate->date = \System::parseDate( \Config::get('dateFormat'), $objRating->tstamp );
			$objTemplate->num_rating = $i;
			$objTemplate->ratingLimitExceeded = false;
			$objTemplate->isPersonal = false;
			$objTemplate->total = $intTotal;
			$objTemplate->attribute = $objAttribute;
			$objTemplate->allowVoting = true;
			
			// allow voting
			if( (boolean)$objAttribute->get('ratings_requireLogin') === true && FE_USER_LOGGED_IN === false)
			{
				$objTemplate->allowVoting = false;
			}
			
			if($intTotal < 1)
			{
				$objTemplate->isFirstRating = true;
			}
			
			if($i == 0)
			{
				$objTemplate->isFirst = true;
			}
			
			if($objRating->helpful > 0)
			{
				$objTemplate->helpful = sprintf(($objRating->helpful == 1 ? $GLOBALS['TL_LANG']['MSC']['ratings_helpful_single'] : $GLOBALS['TL_LANG']['MSC']['ratings_helpful']),$objRating->helpful);
			}
			if($objRating->not_helpful > 0)
			{
				$objTemplate->not_helpful = sprintf(($objRating->not_helpful == 1 ? $GLOBALS['TL_LANG']['MSC']['ratings_not_helpful_single'] : $GLOBALS['TL_LANG']['MSC']['ratings_not_helpful']),$objRating->not_helpful);
			}
			
			// author information
			if($objRating->member > 0)
			{
				$objMember = \MemberModel::findByPk( $objRating->member );
				$objTemplate->MemberModel = $objMember;
				$objTemplate->author = $objMember->firstname.' '.$objMember->lastname;
			}
			
			// comments
			if($objRating->comment > 0)
			{
				$objTemplate->hasComment = true;
				if( $objConfigComments === null)
				{
					$objConfigComments = new \StdClass;
				}
				
				#$source = $objRating->source.'_'.$objRating->attr_id.'_'.($objRating->id ?: 0).'_'.$i;
				#$objComments->addCommentsToTemplate($objTemplate,$objConfigComments,$source,$objRating->pid,$objConfigComments->notifies);
				
				// @var object
				$objComment = \CommentsModel::findByPk($objRating->comment);
				$objTemplate->Comment = $objComment;
			}
			
			// member access
			if($objRating->member > 0 && FE_USER_LOGGED_IN === true)
			{
				$objMember = \FrontendUser::getInstance();
				if(!$objMember->id)
				{
					$objMember->authenticate();
				}
					
				// check if rating belongs to current member
				if($objMember->id == $objRating->member)
				{
					$objTemplate->isPersonal = true;
				}
				
				// count number of ratings
				$intCount = RatingsModel::countBySourceAndPidAndAttributeAndMember($objRating->source,$objRating->pid,$objRating->attr_id,$objMember->id);
				if($intCount >= $GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['maxRatingsPerMember'])
				{
					$objTemplate->ratingLimitExceeded = true;
				}
				$objTemplate->ratingsPerMember = $intCount;
			}
			
			// helpful voting form
			$formID = 'form_ratings_helpful_'.$objRating->id;
			$objTemplate->formID = $formID;
			$objTemplate->helpful_label = $GLOBALS['TL_LANG']['MSC']['ratings_helpful_label'];
			$objTemplate->not_helpful_label = $GLOBALS['TL_LANG']['MSC']['ratings_not_helpful_label'];
			$objTemplate->delete_label = $GLOBALS['TL_LANG']['MSC']['ratings_delete_label'];
			// voting form submitted
			if( \Input::post('FORM_SUBMIT') == $formID )
			{
				// voted helpful
				if( \Input::post('helpful') != '' )
				{
					$objRating->__set('helpful',$objRating->helpful + 1);
					// update the record
					$objRating->save();
				}
				// voted not helpful
				else if( \Input::post('not_helpful') != '' )
				{
					$objRating->__set('not_helpful',$objRating->not_helpful + 1);
					// update the record
					$objRating->save();
				}
				// delete record
				else if( \Input::post('delete') != '' )
				{
					// delete related comments
					if($objRating->comment)
					{
						$objComment = \CommentsModel::findByPk($objRating->comment);
						if($objComment !== null)
						{
							$objComment->delete();
						}
					}
					
					// delete record
					$objRating->delete();
				}
					
				// reload page to flush post
				if((boolean)$GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['reloadAfterSubmit'] === true)
				{
					\Controller::reload();
				}
			}
			
			$arrReturn[] = $objTemplate->parse();
		}
		
		return implode('', $arrReturn);
	}
	
	
	/**
	 * Add ratings list to template
	 * @param object	The template object
	 * @param string	The source
	 * @param integer	The entry ID
	 * @param integer	The attribute ID
	 * @param object	Optional config array
	 * @param array		Optional SQL array
	 */
	public function addRatingsToTemplate($objTemplate,$strSource,$intPid,$intAttribute,$objConfigComments=null,$arrOptions=array())
	{
		// @var object
		$objAttribute = \PCT\CustomElements\Plugins\CustomCatalog\Core\AttributeFactory::findById($intAttribute);
		if($objAttribute === null)
		{
			return;
		}
		
		// find ratings records for the current entry
		$objRatings = RatingsModel::findPublishedBySourceAndPidAndAttribute($strSource,$intPid,$intAttribute,$arrOptions);
		if($objRatings === null)
		{
			return;
		}
	
		// set ratings_ template
		$strTemplate = $objAttribute->ratings_template;
		// override via Template object
		if( !empty($objTemplate->ratings_template) )
		{
			$strTemplate = $objTemplate->ratings_template;
		}
		
		$objTemplate->attribute = $objAttribute;
		// render the ratings
		$objTemplate->ratings = $this->renderRatings($objRatings,$strTemplate,$objConfigComments);
		// pass the records
		$objTemplate->RatingsModel = $objRatings;
		// total
		$objTemplate->total = RatingsModel::countBy(array('source=? AND pid=? AND attr_id=?'),array($strSource,$intPid,$intAttribute));
		// average
		$objTemplate->average = RatingsModel::averageRatingBySourceAndPidAndAttribute($strSource,$intPid,$intAttribute);
		// comments config
		$objTemplate->commentsConfig = $objConfigComments;
		
		// count number of ratings for the current member
		if(FE_USER_LOGGED_IN === true)
		{
			$objMember = \FrontendUser::getInstance();
			if(!$objMember->id)
			{
				$objMember->authenticate();
			}
			
			$intCount = RatingsModel::countBySourceAndPidAndAttributeAndMember($strSource,$intPid,$intAttribute,$objMember->id);
			if($intCount >= $GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['maxRatingsPerMember'])
			{
				$objTemplate->ratingLimitExceeded = true;
			}
			$objTemplate->ratingsPerMember = $intCount;
		}
	}
	
	
	/**
	 * Add a new rating record
	 * @param mixed		Value of the rating
	 * @param string	Source
	 * @param integer	Parent id related to the source
	 * @param integer	Id of the rating attribute
	 * @param boolean	Published setting
	 * @param integer	Id of the comment related to the rating
	 * @return integer	Id of the new rating record
	 */
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
		
		// asign to user
		if(FE_USER_LOGGED_IN === true)
		{
			$objMember = \FrontendUser::getInstance();
			if(!$objMember->id)
			{
				$objMember->authenticate();
			}
			$arrSet['member'] = $objMember->id;
		}
		
		// determine counter
		$objCounter = \Database::getInstance()->prepare("SELECT * FROM tl_pct_customelement_ratings WHERE source=? AND pid=? AND attr_id=? ORDER BY counter DESC")->limit(1)->execute($strSource,$intPid,$intAttribute);
		$arrSet['counter'] = $objCounter->counter + 1;
		
		// @var object
		$objModel = new \PCT\CustomElements\Models\RatingsModel();
		// save new rating
		$objModel->setRow($arrSet)->save();
		
		// HOOK: add custom logic
		if (isset($GLOBALS['CUSTOMELEMENTS_HOOKS']['addRating']) && is_array($GLOBALS['CUSTOMELEMENTS_HOOKS']['addRating']))
		{
			foreach ($GLOBALS['CUSTOMELEMENTS_HOOKS']['addRating'] as $callback)
			{
				\Contao\System::importStatic($callback[0])->{$callback[1]}($objModel->id, $arrSet, $this);
			}
		}
		
		return $objModel->id;
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
		
		// @var object
		$objAttribute = \PCT\CustomElements\Core\AttributeFactory::findById( \Input::post('attr_id') );
		
		if( $objAttribute->get('type') != 'rateit' || ($objAttribute->get('type') == 'rateit' && !$objAttribute->get('allowRatings')) )
		{
			return '';
		}
		
		// add new rating
		$intRating = $this->addNewRating(\Input::post('value'),\Input::post('source'),\Input::post('pid'),$objAttribute->get('id'),($objAttribute->get('ratings_moderate') ? '' : 1), $intComment);
		
		// send notification
		if($intRating > 0 && $objAttribute->get('ratings_notify') != '')
		{
			$objRating = \PCT\CustomElements\Models\RatingsModel::findByPk($intRating);
			
			// Prepare the notification mail
			$objEmail = new \Email();
			$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
			$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
			$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['ratings_subject'], \Idna::decode(\Environment::get('host')));
			$objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['ratings_message'],$arrSet['name'], $objRating->rating, $objRating->source, $objRating->pid);
									  
			// Notifies
			$arrNotifies = array();
			
			// Notify admin
			if($objAttribute->get('ratings_notify') == 'notify_admin')
			{
				$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
			}
			
			// Notify a different person
			if(strlen($objAttribute->get('ratings_notify')) > 0 && $objAttribute->get('ratings_notify') != 'notify_admin')
			{
				$arrNotifies = array($objAttribute->get('com_notify'));
			}
			
			$objEmail->sendTo(array_unique($arrNotifies));
		}
	}
}