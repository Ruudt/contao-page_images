<?php

/**
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2014
 */


/**
 * Table tl_pageimages_items
 */
$GLOBALS['TL_DCA']['tl_pageimages_items'] = array
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
				'pid' => 'index'
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting'),
			'headerFields'            => array('name','size'),
			'flag'                    => 11,
			'panelLayout'             => 'filter;search,limit',
			'child_record_callback'   => array('tl_pageimages_items', 'showLabel')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_items']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_items']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_items']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages_items']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
			'default'			      => '{source_legend},multiSRC;{settings},pages,alt,noInheritance'
	),

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
		'pages' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages_items']['pages'],
			'exclude'                 => true,
			'inputType'               => 'pageTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'mandatory'=>true, 'tl_class'=>'clr'),
			'sql'                     => "blob NULL",
			'load_callback'			  => array
			(
				array('tl_pageimages_items', 'loadItemPages'),
			),
			'save_callback'			  => array
			(
				array('tl_pageimages_items', 'saveItemPages'),
			)
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages_items']['alt'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'sql'                     => "varchar(200) NOT NULL default ''"
		),
		'multiSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages_items']['multiSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'files'=>true, 'mandatory'=>true, 'extensions'=>Config::get('validImageTypes'), 'isGallery'=>true),
			'sql'                     => "blob NULL"
		),
		'noInheritance' => array
		(
			'exclude'                 => true,
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages_items']['noInheritance'],
			'inputType'               => 'checkbox',
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_pageimages_items extends Backend {
    /**
     * label_callback that shows the pagenames the setting applies to
     */
    function showLabel($row) {
        $this->import('Database');

        // Get page names
		$title = '';
		$pagesIds = deserialize($row['pages']);
		$pagesIds = is_array($pagesIds) ? $pagesIds : array($pagesIds);

		// Possible in earlier versions, this is to avoid errors.
		if (empty($pagesIds))
		{
			return '<strong>No pages selected.</strong>';
		}

        $objTitle = $this->Database->execute("SELECT title FROM tl_page WHERE id=" . implode(" OR id=", $pagesIds));
		if ($objTitle->numRows >= 1) while ($objTitle->next()) {
			$title .= $title == '' ? $objTitle->title : ', ' . $objTitle->title;
		}

        list($image, $arrImages) = $this->getRandomImages($row['multiSRC']);

		if (is_array($arrImages) && count($arrImages) > 1) {
			return '<strong>"'.$title.'"</strong>'.($row['noInheritance']?' [Not inherited by subpages]':' [Inherited by subpages]').'<hr /><table><tbody><tr><td style="vertical-align: top; padding-right: 12px; border-right: 1px solid #ccc;"><em>'.$GLOBALS['TL_LANG']['tl_pageimages_items']['random_thumbnails'].'</em><br />'. implode(' ', $arrImages) . '</td><td style="vertical-align: top; padding-left: 12px;"><em>'.$GLOBALS['TL_LANG']['tl_pageimages_items']['example'].'</em><br />' . $image . '</td></tr></tbody></table>';
		} else {
			return '<strong>"'.$title.'"</strong>'.($row['noInheritance']?' [Not inherited by subpages]':' [Inherited by subpages]').'<hr /><em>'.$GLOBALS['TL_LANG']['tl_pageimages_items']['example'].'</em><br />' . $image;
		}
    }
	

	/**
	 * Returns a random image of the collection as well as an array of all images
	 */
	protected function getRandomImages($multiSRC)
	{
		$images = array();
		$arrImages = array();

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
			// Directory
			if (is_dir(TL_ROOT . '/' . $objFiles->path))
			{
				$subfiles = scan(TL_ROOT . '/' . $objFiles->path);

				foreach ($subfiles as $subfile)
				{
					if (strncmp($subfile, '.', 1) === 0 || is_dir(TL_ROOT . '/' . $objFiles->path . '/' . $subfile))
					{
						continue;
					}

					$objFile = new \File($objFiles->path . '/' . $subfile);

					if ($objFile->isGdImage || $objFile->extension == 'swf')
					{
						$images[] = $objFiles->path . '/' . $subfile;
						$arrImages[] = $this->getImageHTML($objFile);
					}
				}

				continue;
			}

			// File
			if (is_file(TL_ROOT . '/' . $objFiles->path))
			{
				$objFile = new \File($objFiles->path);

				if ($objFile->isGdImage || ($objFile->extension == 'swf'))
				{
					$images[] = $objFiles->path;
					$arrImages[] = $this->getImageHTML($objFile);
				}
			}
		}

		$images = array_unique($images);
		$arrImages = array_unique($arrImages);

		if (!is_array($images) || count($images) < 1)
		{
			return;
		}

		$i = mt_rand(0, (count($images)-1));

		$objImage = new File($images[$i]);

		return array($this->getImageHTML($objImage, $width=150, $height=75), $arrImages);

	}

	/**
	 * Returns the HTML for the file provided
	 */
	private function getImageHTML($objFile, $width=0, $height=0)
	{
        if ($objFile->extension == 'swf' )
		{
            return '<object width="'.($width > 0 ? $width : 90).'" height="'.($height > 0 ? $height : 70).'" data="'.$objFile->value.'" type="application/x-shockwave-flash">
<param name="loop" value="true" />
<param name="menu" value="false" />
<param name="quality" value="best" />
<param name="scale" value="noscale" />
<param name="bgcolor" value="#ffffff" />
<param name="swliveconnect" value="false" />
<param name="movie" value="'.$objFile->value.'" />
<param name="wmode" value="transparent" />
</object>';
        }
		elseif($objFile->isGdImage)
		{
			if ($GLOBALS['TL_CONFIG']['thumbnails'] && $objFile->height <= 3000 && $objFile->width <= 3000 && $objFile->height)
			{
				$maxWidth = $width > 0 ? $width : 40;
				$maxHeight = $height > 0 ? $height : 40;
				$_height = ($objFile->height < $maxHeight) ? $objFile->height : $maxHeight;
				$_width = (($objFile->width * $_height / $objFile->height) > (4*$maxWidth)) ? $maxWidth : '';

				$image = $this->getImage($objFile->value, $_width, $_height);
			}
			
			if ($image)
			{
				return '<img src="'.$image.'" alt="" style="margin-top: 6px;" />';
			}
        }
		
		return null;
	}


	/**
	 * Load page ids from table tl_pageimages_pages.
	 */
	public function loadItemPages($varValue, DataContainer $dc)
	{
		$varValue = $this->Database->execute("SELECT pageId FROM tl_pageimages_pages WHERE pid={$dc->id}")->fetchEach('pageId');
		return !empty($varValue) ? serialize($varValue) : null;
	}


	/**
	 * Save page ids to table tl_pageimages_pages.
	 */
	public function saveItemPages($varValue, DataContainer $dc)
	{
		$arrIds = deserialize($varValue);

		if (version_compare(VERSION, '3.0', '>=') && !is_array($arrIds) && !empty($arrIds))
		{
			$arrIds = array($arrIds);
		}

		if (is_array($arrIds) && count($arrIds))
		{
			$time = time();
			$this->Database->query("DELETE FROM tl_pageimages_pages WHERE pid={$dc->id} AND pageId NOT IN (" . implode(',', $arrIds) . ")");
			$objPages = $this->Database->execute("SELECT pageId FROM tl_pageimages_pages WHERE pid={$dc->id}");
			$arrIds = array_diff($arrIds, $objPages->fetchEach('pageId'));

			foreach( $arrIds as $id )
			{
				$sorting = $this->Database->executeUncached("SELECT MAX(sorting) AS sorting FROM tl_pageimages_pages WHERE pageId=$id")->sorting + 128;
				$this->Database->query("INSERT INTO tl_pageimages_pages (pid,tstamp,pageId,sorting) VALUES ({$dc->id}, $time, $id, $sorting)");
			}
		}
		else
		{
			$this->Database->query("DELETE FROM tl_pageimages_pages WHERE pid={$dc->id}");
		}

		return $varValue;
	}
}
