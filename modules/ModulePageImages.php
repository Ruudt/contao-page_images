<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * @package     PageImages
 * @author      Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright   Ruud Walraven 2011 - 2013
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace PageImages;


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

        $pageImages = $this->getPageImages($objPage->id, $this->arrData['pageimages_type']);
        $this->Template->has_pageimage = (bool) count($pageImages);

        if ($this->Template->has_pageimage)
        {
            $objTemplate = new \FrontendTemplate($this->pageimages_layout);
            $objTemplate->headline = $this->Template->headline;
            $objTemplate->hl = $this->Template->hl;
            $objTemplate->style = count($this->Template->style) ? implode(' ', $this->Template->arrStyle) : '';
            $objTemplate->cssID = strlen($this->Template->cssID[0]) ? ' id="' . $this->cssID[0] . '"' : '';
            $objTemplate->class = trim('ce_' . $this->Template->type . ' ' . $this->Template->cssID[1]);

            switch ($this->arrData['pageimages_type'])
            {
                case '1':
                case 'random':
                    // Adds variables to the main template.
                    // Note: These variables are not used by the default templates,        
                    //       but custom templates can use them.
                    $this->addImageToTemplate($objTemplate, $pageImages);

                    $objTemplate->pageimage = $this->getImageHTML($pageImages);
                    $objTemplate->imageData = $pageImages['data']; // Thanks to JSk

                    break;

                // Nr of images has been handled by getPageImages
                case '0':
                case 'all':
                default:
                    $strImages = '';
                    foreach ($pageImages as $img)
                    {
                        $strImages .= $this->getImageHTML($img);
                    }

                    $objTemplate->pageimages = $strImages;

                    break;
            }

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