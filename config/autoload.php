<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Pageimages
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Contao\\PageImages'           => 'system/modules/pageimages/classes/PageImages.php',

	// Models
	'Contao\\PageimagesModel'      => 'system/modules/pageimages/models/PageimagesModel.php',
	'Contao\\PageimagesPagesModel' => 'system/modules/pageimages/models/PageimagesPagesModel.php',

	// Modules
	'Contao\\ModulePageImages'     => 'system/modules/pageimages/modules/ModulePageImages.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_pageimages'     => 'system/modules/pageimages/templates',
	'nav_defaultdddd'    => 'system/modules/pageimages/templates',
	'pageimages_default' => 'system/modules/pageimages/templates',
	'pageimagesflash'    => 'system/modules/pageimages/templates',
	'pageimagesimage'    => 'system/modules/pageimages/templates',
));
