<?php

/**
 * @author		Ruud Walraven <ruud.walraven@gmail.com>
 * @copyright	Ruud Walraven 2011 - 2012
 */

/**
 * Table tl_pageimages
 */
$GLOBALS['TL_DCA']['tl_pageimages'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_pageimages_items'),
		'enableVersioning'            => true,
		'switchToEdit'                => true,
		'onload_callback'			  => array
		(
			array('tl_pageimages', 'checkPermission'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('name'),
			'flag'                    => 1,
			'panelLayout'             => 'search,limit',
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages']['edit'],
				'href'                => 'table=tl_pageimages_items',
				'icon'                => 'edit.gif'
			),
            'editheader' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_pageimages']['editheader'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
                'button_callback'     => array('tl_pageimages', 'editHeader')
            ),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				'button_callback'     => array('tl_pageimages', 'copyPageimages')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
				'button_callback'     => array('tl_pageimages', 'deletePageimages')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pageimages']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'	=>	array('name'),
		'default'       => 'name;{source_legend},multiSRC;{image_legend},alt,size'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages']['name'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(200) NOT NULL default ''"
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages']['alt'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'sql'                     => "varchar(200) NOT NULL default ''"
		),
		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages']['size'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
            'options'                 => System::getImageSizes(),
            'reference'               => &$GLOBALS['TL_LANG']['MSC'],
            'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
		'multiSRC' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_pageimages']['multiSRC'],
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'files'=>true, 'extensions'=>Config::get('validImageTypes'), 'isGallery'=>true),
            'sql'                     => "blob NULL"
        )
	)
);


/**
 * tl_pageimages class.
 */
class tl_pageimages extends Backend
{

	/**
	 * Check permissions to edit table tl_pageimages.
	 */
	public function checkPermission()
	{
		$this->import('BackendUser', 'User');

		if ($this->User->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (!is_array($this->User->pageimages_categories) || count($this->User->pageimages_categories) < 1)
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->pageimages_categories;
		}

		$GLOBALS['TL_DCA']['tl_pageimages']['list']['sorting']['root'] = $root;

		// Check permissions to add new categories
		if (!$this->User->hasAccess('create', 'pageimages_categoriesp'))
		{
			$GLOBALS['TL_DCA']['tl_pageimages']['config']['closed'] = true;
		}

		// Check current action
		switch ($this->Input->get('act'))
		{
			case 'create':
			case 'select':
				// Allow
				break;

			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array($this->Input->get('id'), $root))
				{
					$arrNew = $this->Session->get('new_records');

					if (is_array($arrNew['tl_pageimages']) && in_array($this->Input->get('id'), $arrNew['tl_pageimages']))
					{
						// Add permissions on user level
						if ($this->User->inherit == 'custom' || !$this->User->groups[0])
						{
							$objUser = $this->Database->prepare("SELECT pageimages_categories, pageimages_categoriesp FROM tl_user WHERE id=?")
													   ->limit(1)
													   ->execute($this->User->id);

							$arrCatp = deserialize($objUser->pageimages_categoriesp);

							if (is_array($arrCatp) && in_array('create', $arrCatp))
							{
								$arrCats = deserialize($objUser->pageimages_categories);
								$arrCats[] = $this->Input->get('id');

								$this->Database->prepare("UPDATE tl_user SET pageimages_categories=? WHERE id=?")
											   ->execute(serialize($arrCats), $this->User->id);
							}
						}

						// Add permissions on group level
						elseif ($this->User->groups[0] > 0)
						{
							$objGroup = $this->Database->prepare("SELECT pageimages_categories, pageimages_categoriesp FROM tl_user_group WHERE id=?")
													   ->limit(1)
													   ->execute($this->User->groups[0]);

							$arrCatp = deserialize($objGroup->pageimages_categoriesp);

							if (is_array($arrCatp) && in_array('create', $arrCatp))
							{
								$arrCats = deserialize($objGroup->pageimages_categories);
								$arrCats[] = $this->Input->get('id');

								$this->Database->prepare("UPDATE tl_user_group SET pageimages_categories=? WHERE id=?")
											   ->execute(serialize($arrCats), $this->User->groups[0]);
							}
						}

						// Add new element to the user object
						$root[] = $this->Input->get('id');
						$this->User->pageimages_categories = $root;
					}
				}
				// No break;

			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array($this->Input->get('id'), $root) || ($this->Input->get('act') == 'delete' && !$this->User->hasAccess('delete', 'pageimages_categoriesp')))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' pageimages category ID "'.$this->Input->get('id').'"', 'tl_pageimages checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $this->Session->getData();
				if ($this->Input->get('act') == 'deleteAll' && !$this->User->hasAccess('delete', 'pageimages_categoriesp'))
				{
					$session['CURRENT']['IDS'] = array();
				}
				else
				{
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$this->Session->setData($session);
				break;

			default:
				if (strlen($this->Input->get('act')))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' pageimages categories', 'tl_pageimages checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	/**
	 * Return the copy category button
	 */
	public function copyPageimages($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('create', 'pageimages_categoriesp')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}


	/**
	 * Return the delete category button
	 */
	public function deletePageimages($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('delete', 'pageimages_categoriesp')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}


    /**
     * Return the edit header button
     */
    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->canEditFieldsOf('tl_pageimages') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
    }
}
