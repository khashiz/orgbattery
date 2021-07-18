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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('groupedlist');

/**
 * Select list form field for parent categories.
 *
 * @package     CSVI
 * @subpackage  Joomla Categories
 * @since       7.15.0
 */
class CsviCategoriesFormFieldCategories extends JFormFieldGroupedList
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
		$this->type = 'Categories';

		parent::__construct($form);
	}

	/**
	 * Select categories extension.
	 *
	 * @return  array  An array of categories.
	 *
	 * @since   7.15.0
	 */
	protected function getGroups()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$title = $query->concatenate(
			[
				$db->quoteName('categories.title'),
				$db->quote(' ('),
				$db->quote(Text::_('COM_CSVI_JFORM_CATEGORY_ALIAS')),
				$db->quoteName('categories.alias'),
				$db->quote(')')
			]
		);
		$query->select($db->quoteName('id', 'value'))
			->select($title . ' AS ' . $db->quoteName('text'))
			->select($db->quoteName('extension'))
			->from($db->quoteName('#__categories', 'categories'))
			->where($db->quoteName('categories.level') . ' = 1 ')
			->where($db->quoteName('categories.path') . ' != ' . $db->quote('uncategorised'));

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		$groupedCategories = array();

		foreach ($categories as $category)
		{
			$groupedCategories[Text::_('COM_CSVI_EXTENSION_' . $category->extension)][$category->value] = $category->text;
		}

		return $groupedCategories;
	}
}
