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
 * Class ModulePageImages
 *
 * @copyright  Ruud Walraven 2011
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 */
class ModulePageImages extends \PageImages
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_pageimages';

	
    /**
     * Generate module
     */
    protected function compile()
	{
        global $objPage;

		if ($this->objSet === null)
		{
			return;
		}

		$pageImage = $this->getPageImage($objPage->id);

		$this->Template->has_pageimage = count($pageImage) ? true : false;

        // Set data to template
		if ($this->Template->has_pageimage)
		{
			$objTemplate = new \FrontendTemplate($this->pageimages_layout);

			// Adds variables to the main template.
			// Note: These variables are not used by the default templates,		
			//       but custom templates can use them.
			$this->addImageToTemplate($objTemplate, $pageImage);
				
			$objTemplate->headline = $this->Template->headline;
			$objTemplate->hl = $this->Template->hl;
			$objTemplate->pageimage = $this->getImageHTML($pageImage);
			$objTemplate->style = count($this->Template->style) ? implode(' ', $this->Template->arrStyle) : '';
			$objTemplate->cssID = strlen($this->Template->cssID[0]) ? ' id="' . $this->cssID[0] . '"' : '';
			$objTemplate->class = trim('ce_' . $this->Template->type . ' ' . $this->Template->cssID[1]);
			$objTemplate->imageData = $pageImage['data']; // Thanks to JSk

			$this->Template->pageimage = $objTemplate->parse();
		}

		// HOOK: add custom logic
		if (isset($GLOBALS['PI_HOOKS']['compilePageImages']) && is_array($GLOBALS['PI_HOOKS']['compilePageImages']))
		{
			foreach ($GLOBALS['PI_HOOKS']['compilePageImages'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($this->Template, $pageImage, $this->objSet);
			}
		}
	}
}