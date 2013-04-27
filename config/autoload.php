<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Pageimages
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'PageImages',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'PageImages\PageImages'           => 'system/modules/pageimages/classes/PageImages.php',

	// Models
	'PageImages\PageimagesModel'      => 'system/modules/pageimages/models/PageimagesModel.php',
	'PageImages\PageimagesPagesModel' => 'system/modules/pageimages/models/PageimagesPagesModel.php',

	// Modules
	'PageImages\ModulePageImages'     => 'system/modules/pageimages/modules/ModulePageImages.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_pageimages'     => 'system/modules/pageimages/templates',
	'pageimagesflash'    => 'system/modules/pageimages/templates',
	'pageimagesimage'    => 'system/modules/pageimages/templates',
	'pageimages_default' => 'system/modules/pageimages/templates',
));
