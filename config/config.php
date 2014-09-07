<?php

/**
 * @copyright  Ruud Walraven 2011
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 */


/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD']['miscellaneous'], 0, array
(
    'pageimages' => 'ModulePageImages'
));

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['pageimages'] = array
(
	'tables'	=> array('tl_pageimages','tl_pageimages_items'),
	'icon'		=> 'system/modules/pageimages/assets/icon.gif'
);

/**
 * Permissions are access settings for user and groups (fields in tl_user and tl_user_group)
 */
$GLOBALS['TL_PERMISSIONS'][] = 'pageimages_categories';
$GLOBALS['TL_PERMISSIONS'][] = 'pageimages_categoriesp';