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
 * Class PageImages
 *
 * @copyright  Ruud Walraven 2011
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 */
abstract class PageImages extends \Module
{
	/**
	 * 
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
     * Generate module
     */
    protected function getPageImage($pageId)
	{
		// Retrieve images, looking up the ancestors and linking that to the pageimages_items
		if ($pageImage = $this->findPageImage($pageId))
		{
			$pageImage['size'] = $this->objSet->size;
			return $pageImage;
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
	 */
	protected function findPageImage($pageId, $inheriting=false)
	{
		$objPageItem = \PageimagesPagesModel::findParentAndItemsById($pageId, $this->pageimages);

		if ($objPageItem !== null)
		{
			if (!$inheriting || !$objPageItem->noInheritance)
			{
				$hasPageImage = false;

				// Get a random image
				$multiSRC = deserialize($objPageItem->multiSRC);
				if (is_array($multiSRC) && count($multiSRC))
				{
					if ($pageImage = $this->getRandomImage($multiSRC))
					{
						$pageImage['multiSRC']		= $multiSRC;
						$pageImage['pageId']		= $objPageItem->pageId;
						$pageImage['noInheritance']	= $objPageItem->noInheritance;
						$pageImage['alt']			= $objPageItem->alt ? $objPageItem->alt : $pageImage['alt'];
						$pageImage['data']			= $objPageItem->row(); // Thanks to JSk
					}
				}

				if ($pageImage)
				{
					// Return image when found
					return $pageImage;
				}
			}
		}
		else
		{
			$objPage = \PageModel::findPublishedById($pageId);

			if ($objPage !== null && $objPage->pid)
			{
				// Continue searching while not at root
				return $this->findPageImage($objPage->pid, true);
			}
		}

		// Get the default image if no specific has been found
		if ($pageImage = $this->getRandomImage($this->objSet->multiSRC))
		{
			$pageImage['multiSRC']		= $multiSRC;
			$pageImage['pageId']		= 0;
			$pageImage['noInheritance']	= 0;
			$pageImage['alt']			= $this->objSet->alt ? $this->objSet->alt : $pageImage['alt'];
			$pageImage['data']			= $this->objSet->row(); // Thanks to JSk
		}

		return $pageImage;
	}


	/**
	 * Returns The image HTML
	 * @param $img Image source
	 * @param $width Image width
	 * @param $height Image height
	 * @return string
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
	
	/**
	 * Returns a random existing image from the multiSRC
	 * null if no existing image files in multiSRC
	 */
	protected function getRandomImage($multiSRC)
	{
		global $objPage;
		$images = array();

		$multiSRC = deserialize($multiSRC);

		if (!is_array($multiSRC) || empty($multiSRC))
		{
			return '';
		}

		// Check for version 3 format
		if (!is_numeric($multiSRC[0]))
		{
			return '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
		}

		// Get the file entries from the database
		$objFiles = \FilesModel::findMultipleByIds($multiSRC);

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
				$objSubfiles = \FilesModel::findByPid($objFiles->id);

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

		$i = mt_rand(0, (count($images)-1));

		$arrImage = $images[$i];
		$arrImage['size'] = $this->imgSize;

		if (!$this->useCaption)
		{
			$arrImage['caption'] = null;
		}
		elseif ($arrImage['caption'] == '')
		{
			$arrImage['caption'] = $arrImage['title'];
		}

		return $arrImage;
	}
}