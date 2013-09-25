<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * @package		PageImages
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2012
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace PageImages;


/**
 * Reads and writes pageimages
 * 
 * @package		PageImages
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2012
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class PageimagesModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pageimages';


	/**
	 * Find published page image items by -ID
	 * 
	 * @param int $varId   The numeric ID
	 * 
	 * @return \Model|null The PageimageModel or null if there are no items
	 */
	public static function findSetById($varId)
	{
		$t = static::$strTable;
		$arrColumns = array("$t.id=?");

		return static::findBy($arrColumns, (is_numeric($varId) ? $varId : 0));
	}
}
