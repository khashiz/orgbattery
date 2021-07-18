<?php
/**
 * @package     CSVI
 * @subpackage  Fields
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * elect list form field with templates.
 *
 * @package     CSVI
 * @subpackage  Fields
 * @since       4.0
 */
class JFormFieldCsviTemplates extends JFormFieldCsviForm
{
	/**
	 * Name of the field
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'CsviTemplates';

	/**
	 * Get the export templates set for front-end export.
	 *
	 * @return  array  List of templates.
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 */
	protected function getOptions()
	{
		$action = isset($this->element['action']) ? $this->element['action'] : 'export';
		$frontend = isset($this->element['frontend']) ? (int) $this->element['frontend'] : 1;
		$parent = isset($this->element['parent']) ? $this->element['parent'] : 0;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('csvi_template_id', 'value') . ',' . $db->quoteName('template_name', 'text'))
			->from($db->quoteName('#__csvi_templates'))
			->where($db->quoteName('action') . ' = ' . $db->quote($action));

		if ($frontend)
		{
			$query->where($db->quoteName('frontend') . ' = ' . $frontend);
		}

		$query->order($db->quoteName('template_name'));
		$db->setQuery($query);
		$templates = $db->loadObjectList();

		if ($parent)
		{
			return array_merge(parent::getOptions(), $templates);
		}

		return $templates;
	}
}
