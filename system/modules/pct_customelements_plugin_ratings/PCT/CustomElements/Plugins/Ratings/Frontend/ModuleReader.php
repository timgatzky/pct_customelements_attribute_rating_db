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
namespace PCT\CustomElements\Plugins\Ratings\Frontend;


/**
 * Imports
 */
use PCT\CustomElements\Plugins\CustomCatalog\Core\CustomCatalogFactory;
use PCT\CustomElements\Plugins\CustomCatalog\Core\AttributeFactory;
use PCT\CustomElements\Plugins\CustomCatalog\Frontend\ModuleReader as CC_ModuleReader;
use PCT\CustomElements\Models\RatingsModel;


/**
 * Class file
 * ModuleReader
 */
class ModuleReader extends \Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_customcatalog_ratings';
	
	/**
	 * Ratings template
	 * @var string
	 */
	protected $strRatingsTemplate = 'ratings_default';
	

	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['customcatalogratings'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		// Set the item from the auto_item parameter
		if (!isset($_GET[ $GLOBALS['PCT_CUSTOMCATALOG']['urlItemsParameter'] ]) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet($GLOBALS['PCT_CUSTOMCATALOG']['urlItemsParameter'], \Input::get('auto_item'));
		}
		
		// Do not index or cache the page if no news item has been specified
		if (!\Input::get($GLOBALS['PCT_CUSTOMCATALOG']['urlItemsParameter']) || empty($this->customcatalog) || empty($this->customcatalog_visibles))
		{
			\System::log('No CustomCatalog or ratings attribute selected in module id='.$this->id,__METHOD__,TL_ERROR);
		
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}
		
		if (strlen($this->customcatalog_template) > 0)
		{
			$this->strRatingsTemplate = $this->customcatalog_template;
		}
		
		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objCC = CustomCatalogFactory::findByModule($this->objModel);
		if(!$objCC)
		{
			return '';
		}
		
		$strLanguage = '';
		if( $objCC->get('multilanguage') && ($this->customcatalog_filter_actLang || $objCC->get('aliasField') > 0) )
		{
			$objMultilanguage = new \PCT\CustomElements\Plugins\CustomCatalog\Core\Multilanguage;
			$strLanguage = $objMultilanguage->getActiveFrontendLanguage();
		}
		
		// render the regular details page of a customcatalog entry
		$objEntry = $objCC->findPublishedItemByIdOrAlias(\Input::get($GLOBALS['PCT_CUSTOMCATALOG']['urlItemsParameter']),$strLanguage);
	
		// show 404 if entry does not exist
		if($objEntry->id < 1)
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			
			// throw a page not found exception
			if(version_compare(VERSION, '4','>='))
			{
				throw new \Contao\CoreBundle\Exception\PageNotFoundException('Page not found: ' . \Environment::get('uri'));
			}
			else
			{
				/** @var \PageError404 $objHandler */
				$objHandler = new $GLOBALS['TL_PTY']['error_404']();
				$objHandler->generate($objPage->id);
			}
			return '';
		}
		
		$objAttribute = AttributeFactory::findByCustomCatalog($this->customcatalog_visibles,$objCC->id);
		
		$limit = null;
		$offset = intval($this->customcatalog_offset);

		// Maximum number of items
		if ($this->customcatalog_limit > 0)
		{
			$limit = $this->customcatalog_limit;
		}

		$this->Template->ratings = array();
		
		// Get the total number of items
		$intTotal = RatingsModel::countBy(array('source=? AND pid=? AND attr_id=?'),array($objCC->getTable(),$objEntry->id,$objAttribute->id));
		
		if ($intTotal < 1)
		{
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['ratings_emptyList'];
			return '';
		}
		
		$total = $intTotal - $offset;

		// Split the results
		if ($this->customcatalog_perPage > 0 && (!isset($limit) || $this->customcatalog_limit > $this->customcatalog_perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = $GLOBALS['PCT_CUSTOMCATALOG_RATINGS']['urlPaginationParameter'] . $this->id;
			$page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->customcatalog_perPage), 1))
			{
				/** @var \PageModel $objPage */
				global $objPage;

				/** @var \PageError404 $objHandler */
				$objHandler = new $GLOBALS['TL_PTY']['error_404']();
				$objHandler->generate($objPage->id);
			}

			// Set limit and offset
			$limit = $this->customcatalog_perPage;
			$offset += (max($page, 1) - 1) * $this->customcatalog_perPage;
			$skip = intval($this->customcatalog_offset);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->customcatalog_perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		#$objArticles = $this->fetchItems($this->news_archives, $blnFeatured, ($limit ?: 0), $offset);
		
		// order
		$strModelTable = RatingsModel::getTable();
		$strSorting = $this->customcatalog_sorting;
		$strSortField = $this->customcatalog_sortField;
		$arrOptions['limit'] = $limit;
		$arrOptions['offset'] = $offset;
		$arrOptions['order'] = "$strModelTable.tstamp $strSorting";
		if( !empty($this->customcatalog_sortField) )
		{
			$arrOptions['order'] = "$strModelTable.$strSortField $strSorting";
		}
		// random order
		if( $this->customcatalog_sorting == 'rand')
		{
			$arrOptions['order'] = 'RAND()';
		}
		// custom order
		if( !empty($this->customcatalog_sqlSorting) )
		{
			$arrOptions['order'] = $this->customcatalog_sqlSorting;
		}
		
		// find ratings records for the current entry
		$objRatings = RatingsModel::findPublishedBySourceAndPidAndAttribute($objCC->getTable(),$objEntry->id,$objAttribute->id,$arrOptions);
		
		// prepare the rating records
		$this->Template->ratings = $this->parseRatings($objRatings);
		
		$arrCssID = deserialize($this->cssID);
		$arrClasses = explode(' ', $arrCssID[1]);
		$arrClasses[] = $objCC->getTable();
		$arrCssID[1] = implode(' ', array_filter(array_unique($arrClasses),'strlen') );
		
		$this->cssID = $arrCssID;
		
		// back link
		$this->Template->referer = '{{env::referer}}';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
		
		$this->Template->CustomCatalog = $objCC;
	}
	
	
	/**
	 * Prepare the rating records for template output
	 * @param object	ModelCollection of Rating records
	 * @return string	Html output
	 */
	protected function parseRatings($objRatings)
	{
		if( empty($objRatings) )
		{
			return '';
		}
		
		// @var object Comments
		$objComments = new \Comments();
		
		$arrReturn = array();
		
		foreach($objRatings as $objRating)
		{
			$objTemplate = new \FrontendTemplate( $this->strRatingsTemplate );
			$objTemplate->Rating = $objRating;
			$objTemplate->rating = $objRating->rating;
			
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
				$objConfig = new \StdClass;
				$objConfig->perPage = $this->perPage;
				$objConfig->order = $this->com_order;
				$objConfig->template = $this->com_template;
				$objConfig->requireLogin = $this->com_requireLogin;
				$objConfig->disableCaptcha = $this->com_disableCaptcha;
				$objConfig->bbcode = $this->com_bbcode;
				$objConfig->moderate = $this->com_moderate;
				$objComments->addCommentsToTemplate($objTemplate,$objConfig,$objRating->source.'_'.$objRating->attr_id,$objRating->pid,'');
			}
			
			// helpful voting form
			$formID = 'form_ratings_helpful_'.$objRating->id;
			$objTemplate->formID = $formID;
			$objTemplate->helpful_label = $GLOBALS['TL_LANG']['MSC']['ratings_helpful_label'];
			$objTemplate->not_helpful_label = $GLOBALS['TL_LANG']['MSC']['ratings_not_helpful_label'];
			// voting form submitted
			if( \Input::post('FORM_SUBMIT') == $formID )
			{
				// voted helpful
				if( \Input::post('helpful') != '' )
				{
					$objRating->__set('helpful',$objRating->helpful + 1);
				}
				// voted not helpful
				else if( \Input::post('not_helpful') != '' )
				{
					$objRating->__set('not_helpful',$objRating->not_helpful + 1);
				}
				// update the record
				$objRating->save();
			}
			
			$arrReturn[] = $objTemplate->parse();
		}
		
		return implode('', $arrReturn);
	}
}
