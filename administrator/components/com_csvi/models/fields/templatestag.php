<?php
/**
 * @package     CSVI
 * @subpackage  Template tag
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Loads a list of template tags added
 *
 * @package     CSVI
 * @subpackage  Template Tag
 * @since       7.12.0
 */
class CsviFormFieldTemplatestag extends JFormFieldList
{
	/**
	 * The type of field
	 *
	 * @var    string
	 * @since  7.12.0
	 */
	protected $type = 'templatestag';

	/**
	 * Get the list of tags added to templates
	 *
	 * @return  array  An array of tags.
	 *
	 * @since   7.12.0
	 *
	 * @throws  RuntimeException
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('tag', 'text'))
			->select($db->quoteName('tag', 'value'))
			->from($db->quoteName('#__csvi_templates'))
			->where($db->quoteName('tag') . ' <> ' . $db->quote(''))
			->order($db->quoteName('tag'))
			->group($db->quoteName('tag'));
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return array_merge(parent::getOptions(), $options);
	}
}
