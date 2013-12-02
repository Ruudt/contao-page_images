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


class PageimageRunonce extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->import('Database');
    }


    /**
     * Convert pageimages_type from string to the matching integer values.
     */
    public static function run()
    {
        $objPageimage = new self();

        $objPageimage->convertPageimagesType();
    }


    /**
     * Convert pageimages_type from string to the matching integer values.
     */
    private function convertPageimagesType()
    {
        if ($this->Database->fieldExists('pageimages_type', 'tl_module'))
        {
            $this->Database->prepare("UPDATE tl_module SET pageimages_type=0 WHERE pageimages_type='all'")->execute();
            $this->Database->prepare("UPDATE tl_module SET pageimages_type=1 WHERE pageimages_type='random'")->execute();
           
            $this->log('Converted pageimages_type in tl_module', __METHOD__, TL_ACCESS);
        }
    }
}

PageimageRunonce::run();