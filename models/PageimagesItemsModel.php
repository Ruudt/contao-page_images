<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * @package		PageImages
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2014
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace PageImages;


/**
 * Reads and writes pageimagesitems
 * 
 * @package		PageImages
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2014
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class PageimagesItemsModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pageimages_items';
}
