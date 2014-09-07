<?php

/**
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2012
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace('{account_legend}', '{pageimages_legend},pageimages_categories,pageimages_categoriesp;{account_legend}', $GLOBALS['TL_DCA']['tl_user']['palettes']['extend']);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace('{account_legend}', '{pageimages_legend},pageimages_categories,pageimages_categoriesp;{account_legend}', $GLOBALS['TL_DCA']['tl_user']['palettes']['custom']);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['pageimages_categories'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['pageimages_categories'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'			  => 'tl_pageimages.name',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_user']['fields']['pageimages_categoriesp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['pageimages_categoriesp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);