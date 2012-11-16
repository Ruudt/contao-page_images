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
namespace Contao;


/**
 * Reads and writes pageimages pages
 * 
 * @package		PageImages
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2012
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class PageimagesPagesModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pageimages_pages';


	/**
	 * Find published news items by their parent ID and ID or alias
	 * 
	 * @param mixed $varId   The numeric ID or alias name
	 * 
	 * @return \Model|null The NewsModel or null if there are no news
	 */
	public static function findParentAndItemsById($varId, $pageimagesPid)
	{
		// @todo LEFT JOIN is supposed to return all results from left + whatever records where found right. But the where clause somehow messes it up...
		// That's why I'm using inner joins and doing the second lookup to find the parent page later on...
		$objPages = \Database::getInstance()->prepare("SELECT `tl_pageimages_items`.*, `tl_pageimages_pages`.`pageId`, `tl_page`.`pid` AS `pagePid` FROM ((`tl_page` INNER JOIN `tl_pageimages_pages` ON `tl_page`.`id` = `tl_pageimages_pages`.`pageId`) INNER JOIN `tl_pageimages_items` ON `tl_pageimages_pages`.`pid` = `tl_pageimages_items`.`id`) WHERE `tl_page`.`id`=? AND `tl_pageimages_items`.`pid`=?")
											->execute($varId, $pageimagesPid);
		return $objPages->numRows ? $objPages : null;
	}
}
