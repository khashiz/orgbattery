<?php
/**
 * @package     CSVI
 * @subpackage  Joomla Categories
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with Extensions.
 *
 * @package     CSVI
 * @subpackage  Joomla Categories
 * @since       7.15.0
 */
class CsviCategoriesFormFieldExtensions extends JFormFieldCsviForm
{
	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm  $form  The form to attach to the form field object.
	 *
	 * @since   7.15.0
	 *
	 * @throws  Exception
	 */
	public function __construct($form = null)
	{
		$this->type = 'Extensions';

		parent::__construct($form);
	}

	/**
	 * Select categories extension.
	 *
	 * @return  array  An array of extensions.
	 *
	 * @since   7.15.0
	 */
	protected function getOptions()
	{
		$db    = JFactory::getDbo();
		$query = $this->db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'extension',
						'extension'
					],
					[
						'value',
						'text'
					]
				)
			)
			->from($this->db->quoteName('#__categories'))
			->where($this->db->quoteName('asset_id') . ' > 0 ')
			->group($this->db->quoteName('value'));

		$this->db->setQuery($query);
		$extensions = $this->db->loadObjectList();

		if (empty($extensions))
		{
			$extensions = [];
		}
		else
		{
			foreach ($extensions as $extension)
			{
				$text            = Text::_('COM_CSVI_EXTENSION_' . $extension->text);
				$extension->text = $text;
			}
		}

		return array_merge(parent::getOptions(), $extensions);
	}
}
