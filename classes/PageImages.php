<?php

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace PageImages;


/**
 * Class PageImages
 *
 * @copyright  Ruud Walraven 2013
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 */
abstract class PageImages extends \Module
{
    /**
     * Generate the module
     * @param boolean
     * @return string
     */
    public function generate()
    {
        // Get the image set
        $this->objSet = \PageimagesModel::findSetById($this->pageimages);

        return parent::generate();
    }

    /**
     * Get the page image(s)
     */
    protected function getPageImages($pageId, $count='1')
    {
        // Retrieve images, looking up the ancestors and linking that to the pageimages_items
        if ($pageImages = $this->findPageImages($pageId))
        {
            shuffle($pageImages);

            switch ($count)
            {
                case '1':
                case 'random':
                    $pageImage = $pageImages[0];
                    $pageImage['size'] = $this->objSet->size;

                    return $pageImage;

                case 'all':
                    $count = 0;

                    // no break here

                case '0':
                default:
                    $count = intval($count) > 0 ? min(intval($count), count($pageImages)) : count($pageImages);
                    for ($i = 0; $i < $count; $i++)
                    {
                        $pageImages[$i]['size'] = $this->objSet->size;
                    }
                    return array_slice($pageImages, 0, $count);
            }
        }
        else
        {
            // Make sure to return an empty array if no image was found
            return array();  
        }
    }

    /**
     * Check the parent pages recursively to find a page that has an image set
     * and then return that page. Returns false if none found.
     * TODO: Check navigation extension to sort out returning multiple images
     */
    protected function findPageImages($pageId, $inheriting=false)
    {
        $objPageItem = \PageimagesPagesModel::findParentAndItemsById($pageId, $this->pageimages);

        if ($objPageItem !== null)
        {
            $pageImages = array();

            while( $objPageItem->next() )
            {
                if ($inheriting && $objPageItem->noInheritance)
                {
                    continue;
                }

                // Get a random image
                $multiSRC = deserialize($objPageItem->multiSRC);

                if (is_array($multiSRC) && !empty($multiSRC))
                {
                    if (!$tmpPageImages = $this->getImages($multiSRC))
                    {
                        continue;
                    }

                    foreach ($tmpPageImages as $key => $tmp)
                    {
                        $tmpPageImages[$key]['multiSRC']      = $multiSRC; // Will be removed in future version (deprecated)
                        $tmpPageImages[$key]['uuid']          = $tmpPageImages[$key]['uuid'] ?: $multiSRC[$key];
                        $tmpPageImages[$key]['pageId']        = $objPageItem->pageId;
                        $tmpPageImages[$key]['noInheritance'] = $objPageItem->noInheritance;
                        $tmpPageImages[$key]['alt']           = $objPageItem->alt ? $objPageItem->alt : $tmpPageImage['alt'];
                        $tmpPageImages[$key]['data']          = $objPageItem->row(); // Thanks to JSK
                    }

                    $pageImages = array_merge_recursive($pageImages, $tmpPageImages);
                    // Could add multiSRC here, needs getPageImages to filter out non numeric keys
                    // $pageImages['multiSRC'] = array_merge($pageImages['multiSRC'], $multiSRC);
                }
            }

            if (count($pageImages))
            {
                // Return image when found
                return $pageImages;
            }
        }
        else
        {
            $objPage = \PageModel::findPublishedById($pageId);
            
            if ($objPage !== null && $objPage->pid)
            {
                // Continue searching while not at root
                return $this->findPageImages($objPage->pid, true);
            }
        }
        
        // Get the default image if no specific has been found
        if ($tmpPageImage = $this->getImages($this->objSet->multiSRC))
        {
            $i = 0;
            
            foreach ($tmpPageImage as $key => $pageImage)
            {
                $tmpPageImage['multiSRC']      = $this->objSet->multiSRC; // Will be removed in future versions (deprecated)
                $tmpPageImage['uuid']          = $tmpPageImage['uuid'] ?: $this->objSet->multiSRC[$key];
                $tmpPageImage['pageId']        = 0;
                $tmpPageImage['noInheritance'] = 0;
                $tmpPageImage['alt']           = $this->objSet->alt ? $this->objSet->alt : $tmpPageImage['alt'];
                $tmpPageImage['data']          = $this->objSet->row(); // Thanks to JSk

                $pageImages[$i] = $tmpPageImage;

                $i++;
            }

        }

        return $pageImages;
    }

    /**
     * Returns a random existing image from the multiSRC
     * null if no existing image files in multiSRC
     */
    protected function getImages($multiSRC)
    {
        global $objPage;
        $images = array();

        $multiSRC = deserialize($multiSRC);

        if (!is_array($multiSRC) || empty($multiSRC))
        {
            return '';
        }

        // Get the file entries from the database
        $objFiles = \FilesModel::findMultipleByUuids($multiSRC);

        if ($objFiles === null)
        {
            return '';
        }

        // Get all images
        while ($objFiles->next())
        {
            // Continue if the files has been processed or does not exist
            if (isset($images[$objFiles->path]) || !file_exists(TL_ROOT . '/' . $objFiles->path))
            {
                continue;
            }

            // Single files
            if ($objFiles->type == 'file')
            {
                $objFile = new \File($objFiles->path);

                if (!$objFile->isGdImage || ($objFile->extension == 'swf'))
                {
                    continue;
                }

                $arrMeta = $this->getMetaData($objFiles->meta, $objPage->language);

                // Use the file name as title if none is given
                if ($arrMeta['title'] == '')
                {
                    $arrMeta['title'] = specialchars(str_replace('_', ' ', preg_replace('/^[0-9]+_/', '', $objFile->filename)));
                }

                // Add the image
                $images[$objFiles->path] = array
                (
                    'id'        => $objFiles->id,
                    'uuid'      => $objFile->uuid,
                    'name'      => $objFile->basename,
                    'singleSRC' => $objFiles->path,
                    'alt'       => $arrMeta['title'],
                    'imageUrl'  => $arrMeta['link'],
                    'caption'   => $arrMeta['caption']
                );
            }

            // Folders
            else
            {
                $objSubfiles = \FilesModel::findByPid(isset($objFiles->uuid) ? $objFiles->uuid : $objFiles->id);

                if ($objSubfiles === null)
                {
                    continue;
                }

                while ($objSubfiles->next())
                {
                    // Skip subfolders
                    if ($objSubfiles->type == 'folder')
                    {
                        continue;
                    }

                    $objFile = new \File($objSubfiles->path);

                    if (!$objFile->isGdImage || ($objFile->extension == 'swf'))
                    {
                        continue;
                    }

                    $arrMeta = $this->getMetaData($objSubfiles->meta, $objPage->language);

                    // Use the file name as title if none is given
                    if ($arrMeta['title'] == '')
                    {
                        $arrMeta['title'] = specialchars(str_replace('_', ' ', preg_replace('/^[0-9]+_/', '', $objFile->filename)));
                    }

                    // Add the image
                    $images[$objSubfiles->path] = array
                    (
                        'id'        => $objSubfiles->id,
                        'uuid'      => $objSubfiles->uuid,
                        'name'      => $objFile->basename,
                        'singleSRC' => $objSubfiles->path,
                        'alt'       => $arrMeta['title'],
                        'imageUrl'  => $arrMeta['link'],
                        'caption'   => $arrMeta['caption']
                    );
                }
            }
        }

        $images = array_values($images);

        if (empty($images))
        {
            return;
        }

        foreach ($images as $key => $arrImage)
        {
            $images[$key]['size'] = $this->imgSize;

            if (!$this->useCaption)
            {
                $images[$key]['caption'] = null;
            }
            elseif ($images[$key]['caption'] == '')
            {
                $images[$key]['caption'] = $images[$key]['title'];
            }
        }
        
        return $images;
    }

    /**
     * Returns The image HTML
     */
    protected function getImageHTML($arrItem)
    {
        if(substr($arrItem['singleSRC'], -3) == 'swf')
        {
            $objTemplate = new \FrontendTemplate('pageimagesflash');
            $this->addImageToTemplate($objTemplate, $arrItem);
            $this->setFlashSize($objTemplate, $arrItem);
        }
        else
        {
            $objTemplate = new \FrontendTemplate('pageimagesimage');
            $this->addImageToTemplate($objTemplate, $arrItem);
        }

        $objTemplate->row = $arrItem['data']; 

        return $objTemplate->parse();
    }

    /**
     * The addImageToTemplate function calls the getimage function that does not actually resize a flash file
     * This function calculates the resized size for the flash file
     */
    protected function setFlashSize($objTemplate, $arrItem)
    {
        $size = deserialize($arrItem['size']);
        $imgSize = getimagesize(TL_ROOT .'/'. $arrItem['singleSRC']);
        $intMaxWidth = (TL_MODE == 'BE') ? 320 : $GLOBALS['TL_CONFIG']['maxImageWidth'];

        // Adjust image size
        if ($intMaxWidth > 0 && ($size[0] > $intMaxWidth || (!$size[0] && !$size[1] && $imgSize[0] > $intMaxWidth)))
        {
            $arrMargin = deserialize($arrItem['imagemargin']);

            // Subtract margins
            if (is_array($arrMargin) && $arrMargin['unit'] == 'px')
            {
                $intMaxWidth = $intMaxWidth - $arrMargin['left'] - $arrMargin['right'];
            }

            // See #2268 (thanks to Thyon)
            $ratio = ($size[0] && $size[1]) ? $size[1] / $size[0] : $imgSize[1] / $imgSize[0];

            $size[0] = $intMaxWidth;
            $size[1] = floor($intMaxWidth * $ratio);
        }

        $width = $size[0];
        $height = $size[1];
        $mode = $size[2];
        
        // No resizing required
        if ($imgSize[0] != $width || $imgSize[1] != $height)
        {
            $intPositionX = 0;
            $intPositionY = 0;
            $intWidth = $width;
            $intHeight = $height;

            // Mode-specific changes
            if ($intWidth && $intHeight)
            {
                switch ($mode)
                {
                    case 'proportional':
                        if ($imgSize[0] >= $imgSize[1])
                        {
                            unset($height, $intHeight);
                        }
                        else
                        {
                            unset($width, $intWidth);
                        }
                        break;

                    case 'box':
                        if (ceil($imgSize[1] * $width / $imgSize[0]) <= $intHeight)
                        {
                            unset($height, $intHeight);
                        }
                        else
                        {
                            unset($width, $intWidth);
                        }
                        break;
                }
            }

            // Resize width and height and crop the image if necessary
            if ($intWidth && $intHeight)
            {
                $intWidth = $width;
                $intHeight = $height;
            }
            elseif ($intWidth)
            {
                $intHeight = ceil($imgSize[1] * $width / $imgSize[0]);
            }
            elseif ($intHeight)
            {
                $intWidth = ceil($imgSize[0] * $height / $imgSize[1]);
            }
        }

        $objTemplate->arrSize = array
        (
            0 => $intWidth,
            1 => $intHeight,
            2 => 12,
            3 => 'width="'.$intWidth.'" height="'.$intHeight.'"',
            'mime' => 'application/x-shockwave-flash'
        );
        $objTemplate->imgSize = $objTemplate->arrSize[3];
    }
}
