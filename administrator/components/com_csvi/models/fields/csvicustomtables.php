<?php
/**
 * List the operations
 *
 * @author 		RolandD Cyber Produksi
 * @link 		https://rolandd.com
 * @copyright 	Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvioperations.php 2380 2013-03-15 14:34:04Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with operations
 */
class JFormFieldCsviCustomtables extends JFormFieldCsviForm
{
	/**
	 * The name of the form field
	 *
	 * @var    string
	 * @since  6.0
	 */
	protected $type = 'CsviCustomtables';

	/**
	 * Load the available tables.
	 *
	 * @return  array  A list of available tables.
	 *
	 * @since   4.0
	 */
	protected function getOptions()
	{
		$checkField = $this->element['jointable'];
		$templateId = $this->jinput->getInt('csvi_template_id', '', 0);

		$db     = JFactory::getDbo();
		$tables = $db->getTableList();
		$prefix = $db->getPrefix();

		// Remove the table prefix
		foreach ($tables as $tkey => $table)
		{
			unset($tables[$tkey]);

			if (stristr($table, $prefix))
			{
				$clean = str_replace($prefix, '', $table);
				$tables[$clean] = $clean;
			}
		}

		// If it is join table we only need selected tables
		if ($checkField)
		{
			$tables = [];

			if ($templateId)
			{
				$query = $this->db->getQuery(true)
					->select($this->db->quoteName('settings'))
					->from($this->db->quoteName('#__csvi_templates'))
					->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $templateId);
				$this->db->setQuery($query);
				$templateSettings  = $this->db->loadResult();
				$customTablesArray = json_decode($templateSettings, true);

				if (isset($customTablesArray['custom_table']))
				{
					$customTables = $customTablesArray['custom_table'];

					foreach ($customTables as $customTable)
					{
						$tables[$customTable['table']] = $customTable['table'];
					}
				}
			}
		}

		// Load the values from the XML definition
		return array_merge(parent::getOptions(), $tables);
	}
}
