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
 * Table tl_pageimages_pages
 */
$GLOBALS['TL_DCA']['tl_pageimages_pages'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_pageimages',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
				'pageId' => 'index'
			)
		),
	),

	// List
	// 'list' => array
	// (
		// 'sorting' => array
		// (
			// 'mode'                    => 4,
			// 'fields'                  => array('sorting'),
			// 'headerFields'            => array('name','size'),
			// 'flag'                    => 11,
			// 'panelLayout'             => 'filter;search,limit'
		// ),
		// 'global_operations' => array
		// (
			// 'all' => array
			// (
				// 'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				// 'href'                => 'act=select',
				// 'class'               => 'header_edit_all',
				// 'attributes'          => 'onclick="Backend.getScrollOffset();"'
			// )
		// ),
		// 'operations' => array
		// (
			// 'edit' => array
			// (
				// 'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_pages']['edit'],
				// 'href'                => 'act=edit',
				// 'icon'                => 'edit.gif'
			// ),
			// 'copy' => array
			// (
				// 'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_pages']['copy'],
				// 'href'                => 'act=copy',
				// 'icon'                => 'copy.gif'
			// ),
			// 'delete' => array
			// (
				// 'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_pages']['delete'],
				// 'href'                => 'act=delete',
				// 'icon'                => 'delete.gif',
				// 'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			// ),
			// 'show' => array
			// (
				// 'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_pages']['show'],
				// 'href'                => 'act=show',
				// 'icon'                => 'show.gif'
			// )
		// )
	// ),

	// Palettes
	// 'palettes' => array
	// (
			// 'default'			      => '{settings},multiSRC,pages,alt,noInheritance'
	// ),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'pageId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages_pages']['pageId'],
			'exclude'                 => true,
			'inputType'               => 'pageTree',
			'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
	)
);