<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * @copyright  Ruud Walraven 2011
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 * @package    PageImages
 * @license    GPL
 */


/**
 * Add a palette to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['pageimages'] = '{title_legend},name,headline,type;{template_legend},pageimages_type,pageimages_layout,useCaption,fullsize;{config_legend},pageimages;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['pageimages'] = array
(
	'label'						=> &$GLOBALS['TL_LANG']['tl_module']['pageimages'],
	'exclude'					=> true,
	'inputType'					=> 'radio',
	'foreignKey'				=> 'tl_pageimages.name',
	'eval'						=> array('mandatory'=>true),
	'sql'						=> "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['pageimages_layout'] = array
(
	'label'						=> &$GLOBALS['TL_LANG']['tl_module']['pageimages_layout'],
	'default'					=> 'pageimages_default',
	'exclude'					=> true,
	'inputType'					=> 'select',
	'options_callback'			=> array('tl_module_pageimages', 'getPageimagesTemplates'),
	'eval'						=> array('tl_class'=>'w50'),
	'sql'						=> "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['pageimages_type'] = array
(
	'label'						=> &$GLOBALS['TL_LANG']['tl_module']['pageimages_type'],
	'default'					=> 'random',
	'exclude'					=> true,
	'inputType'					=> 'select',
	'options'					=> array('random', 'all'),
	'reference'					=> &$GLOBALS['TL_LANG']['tl_module']['pageimages_type_options'],
	'eval'						=> array('tl_class'=>'w50'),
	'sql'						=> "varchar(64) NOT NULL default 'random'"
);


class tl_module_pageimages extends Backend {
	/**
	 * Return pageimages templates as array
	 * @param object
	 * @return array
	 */
	public function getPageimagesTemplates(DataContainer $dc)
	{
		$intPid = $dc->activeRecord->pid;

		if (Input::get('act') == 'overrideAll')
		{
			$intPid = Input::get('id');
		}
			
		return $this->getTemplateGroup('pageimages_', $intPid);
	}
}