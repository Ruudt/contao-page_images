<?php

/**
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2012
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = preg_replace('/({alexf_legend(:hide)?})/', '{pageimages_legend},pageimages_categories,pageimages_categoriesp;\1', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['pageimages_categories'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['pageimages_categories'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'			  => 'tl_pageimages.name',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['pageimages_categoriesp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['pageimages_categoriesp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);