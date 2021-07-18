<?php
/**
 * @package     CSVI
 * @subpackage  Tasks
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Tasks model.
 *
 * @package     CSVI
 * @subpackage  Tasks
 * @since       6.0
 */
class CsviModelTask extends JModelAdmin
{
	/**
	 * The database class
	 *
	 * @var    JDatabaseDriver
	 * @since  6.0
	 */
	protected $db;

	/**
	 * Holds the input class
	 *
	 * @var    JInput
	 * @since  6.6.0
	 */
	protected $input;

	/**
	 * Public class constructor
	 *
	 * @param   array  $config  The configuration array
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		$this->db = JFactory::getDbo();
		$this->input = JFactory::getApplication()->input;
	}

	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   4.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_csvi.task', 'task', array('control' => 'jform', 'load_data' => $loadData));

		if ($form === null)
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form..
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.task.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Load the template types for a given selection.
	 *
	 * @param   string  $action     The import or export option.
	 * @param   string  $component  The component.
	 *
	 * @return  array  List of available tasks.
	 *
	 * @since   3.5
	 *
	 * @throws \RuntimeException
	 */
	public function loadTasks($action, $component)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('task_name'))
			->from($this->db->quoteName('#__csvi_tasks'))
			->where($this->db->quoteName('action') . ' = ' . $this->db->quote($action))
			->where($this->db->quoteName('component') . ' = ' . $this->db->quote($component))
			->where($this->db->quoteName('enabled') . ' = 1');
		$this->db->setQuery($query);
		$types = $this->db->loadColumn();

		// Get translations
		$trans = array();

		foreach ($types as $type)
		{
			$trans[$type] = JText::_('COM_CSVI_' . $component . '_' . $type);
		}

		// Sort by task name
		ksort($trans);

		return $trans;
	}

	/**
	 * Reset the tasks.
	 *
	 * @return  bool  True if no errors are found | False if an SQL error has been found.
	 *
	 * @since   5.4
	 *
	 * @throws  RuntimeException
	 */
	public function reload()
	{
		// Empty the tasks table
		$this->db->truncateTable('#__csvi_availabletables');
		$this->db->truncateTable('#__csvi_tasks');

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$files = JFolder::files(JPATH_PLUGINS . '/csviaddon/', 'tasks.sql', true, true);

		if ($files)
		{
			foreach ($files as $file)
			{
				$queries = JDatabaseDriver::splitSql(file_get_contents($file));

				foreach ($queries as $query)
				{
					$query = trim($query);

					if ('' !== $query)
					{
						$this->db->setQuery($query)->execute();
					}
				}
			}
		}

		// If there are any custom table export/import, add custom tables and their fields
		$csvihelper = new CsviHelperCsvi;
		$settings   = new CsviHelperSettings($this->db);
		$log        = new CsviHelperLog($settings, $this->db);
		require_once JPATH_PLUGINS . '/csviaddon/csvi/com_csvi/model/maintenance.php';
		$maintenanceModel = new Com_CsviMaintenance($this->db, $log, $csvihelper);
		$templatesModel   = JModelLegacy::getInstance('Templates', 'CsviModel', array('ignore_request' => true));
		$templatesModel->state->set('list.limit', 0);
		$templates        = $templatesModel->getItems();
		$processed        = array();

		foreach ($templates as $template)
		{
			$customTables     = array();
			$templateArray    = json_decode(json_encode($template), true);
			$templateSettings = json_decode(ArrayHelper::getValue($templateArray, 'settings'), true);
			$action           = $templateSettings['action'] ?? '';

			if (isset($templateSettings['component']) && ($templateSettings['component'] === 'com_csvi'))
			{
				if ($action === 'import')
				{
					if (!empty($templateSettings['custom_table']))
					{
						$customTables[$action][] = $templateSettings['custom_table'];
					}
				}
				else
				{
					for ($i = 0; $i < count($templateSettings['custom_table']); $i++)
					{
						if (isset($templateSettings['custom_table']['custom_table' . $i]))
						{
							$customTables[$action][] = $templateSettings['custom_table']['custom_table' . $i]['table'];

							if (isset($templateSettings['custom_table']['custom_table' . $i]['jointable'])
								&& $templateSettings['custom_table']['custom_table' . $i]['jointable']
							)
							{
								$customTables[$action][] = $templateSettings['custom_table']['custom_table' . $i]['jointable'];
							}
						}
					}
				}

				// Check if we have any tables to process
				if ($customTables)
				{
					// Check if we have the action
					if (!array_key_exists($action, $processed))
					{
						$processed[$action] = array();
					}

					// Check if we have already processed the table
					$index = array_diff($customTables[$action], $processed[$action]);

					if ($index)
					{
						$processed[$action] = array_merge($processed[$action], $index);
						$maintenanceModel->saveCustomTableAvailableFields($index, $action);
					}
				}
			}
		}

		return true;
	}
}
