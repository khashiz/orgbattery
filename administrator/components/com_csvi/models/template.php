<?php
/**
 * @package     CSVI
 * @subpackage  Template
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use phpseclib\Net\SFTP;

defined('_JEXEC') or die;

/**
 * Template model.
 *
 * @package     CSVI
 * @subpackage  Template
 * @since       6.0
 */
class CsviModelTemplate extends JModelAdmin
{
	/**
	 * Holds the database driver
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
	 * Holds the template settings
	 *
	 * @var    array
	 * @since  6.0
	 */
	protected $options;

	/**
	 * Construct the class.
	 *
	 * @since   6.0
	 *
	 * @throws  Exception
	 */
	public function __construct()
	{
		parent::__construct();

		// Load the basics
		$this->db = $this->getDbo();
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
		// Add our own form path
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_csvi/views/template/tmpl/');

		// Get the form.
		$form = $this->loadForm('com_csvi.template', 'operations', array('control' => 'jform', 'load_data' => $loadData));

		if ($form === null)
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The filtered form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   3.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  UnexpectedValueException
	 */
	public function save($data)
	{
		// Get the complete posted data
		$fullData = $this->input->post->getArray(
			array(
				'template_name'    => 'string',
				'template_alias'   => 'string',
				'enabled'          => 'int',
				'ordering'         => 'int',
				'log'              => 'int',
				'frontend'         => 'int',
				'csvi_template_id' => 'int',
				'secret'           => 'raw',
				'advanced'         => 'int',
				'notes'            => 'string',
				'tag'              => 'string',
			)
		);

		// Set the filtered data
		$fullData['jform'] = $data;

		// Load the table
		$table = $this->getTable('Template');
		$table->load($fullData['csvi_template_id']);

		$templateId = $table->get( 'csvi_template_id' );
		$query      = $this->db->getQuery( true );

		// Prepare the settings
		if (array_key_exists('jform', $fullData))
		{
			// Check if we are in the wizard, if so, we must preload the already stored settings
			if ($this->input->getInt('step', 0))
			{
				$query->clear()
					->select(
						$this->db->quoteName(
							array(
								'settings',
								'action',
							)
						)
					)
					->from($this->db->quoteName('#__csvi_templates'))
					->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $table->get('csvi_template_id'));
				$this->db->setQuery($query);
				$templateSettings = $this->db->loadObject();

				if ($templateSettings)
				{
					$fullData['jform']  = array_merge((array) json_decode($templateSettings->settings), $fullData['jform']);
					$fullData['action'] = $templateSettings->action;
				}
			}

			// Clear the FTP details if it is not set as location
			if ((array_key_exists('source', $fullData['jform']) && $fullData['jform']['source'] !== 'fromftp')
				|| (array_key_exists('exportto', $fullData['jform']) && !in_array('toftp', $fullData['jform']['exportto'], true)))
			{
				$fullData['jform']['ftpusername'] = '';
				$fullData['jform']['ftppass']     = '';
			}

			// Clear the URL details if it is not set as location
			if (array_key_exists('source', $fullData['jform']) && $fullData['jform']['source'] !== 'fromurl')
			{
				$fullData['jform']['urlusername'] = '';
				$fullData['jform']['urlpass']     = '';
			}

			// If exporting to an import template, save the mapped template fields automatically
			if (array_key_exists('exportto', $fullData['jform']))
			{
				$sourceArray = $fullData['jform']['exportto'];
				$component   = $fullData['jform']['component'];

				foreach ($sourceArray as $source)
				{
					if (is_numeric($source))
					{
						$exportField = '';

						if ($component)
						{
							$exportExtension = str_replace('com_', '', $component);
							$exportField     = 'field_' . $exportExtension;
						}

						$this->createBridgeTemplateTable($source, $fullData['csvi_template_id'], $exportField);
					}
				}
			}

			// Remove any trailing slash in export to server path
			if (array_key_exists('localpath', $fullData['jform']))
			{
				$localPath = $fullData['jform']['localpath'];

				if (substr($localPath, -1) === '/')
				{
					$localPath = substr($fullData['jform']['localpath'], 0, -1);
				}

				$fullData['jform']['localpath'] = $localPath;
			}

			$fullData['settings'] = json_encode($fullData['jform']);
			$fullData['action']   = $fullData['jform']['action'];
		}

		// Store the table to the custom available fields if needed
		if (array_key_exists('custom_table', $fullData['jform']) && $fullData['jform']['custom_table']['custom_table0'])
		{
			$customTables = array();

			if (isset($fullData['jform']['import_based_on']) && $fullData['jform']['import_based_on'])
			{
				$field_name = $fullData['jform']['import_based_on'];
				$importBasedkeys = explode(',', $field_name);
				$customTable = $this->db->getPrefix() . $fullData['jform']['custom_table'];
				$columns = $this->db->getTableColumns($customTable);

				try
				{
					foreach ($importBasedkeys as $importField)
					{
						$importField = trim($importField);

						if (!isset($columns[$importField]))
						{
							$this->setError(JText::sprintf('COM_CSVI_NO_IMPORT_FIELD_FOUND', $importField));

							return false;
						}
					}
				}
				catch (Exception $e)
				{
					throw new CsviException($e->getMessage(), $e->getCode());
				}
			}


			// Load the helpers
			$csvihelper = new CsviHelperCsvi;
			$settings   = new CsviHelperSettings($this->db);
			$log        = new CsviHelperLog($settings, $this->db);

			// For export multiple tables is possible so modify array in readable format
			if ($fullData['action'] === 'export')
			{
				for ($i = 0; $i < count($fullData['jform']['custom_table']) + 1; $i++)
				{
					$tableId        = 'custom_table' . $i;
					$joinTableName  = '';
					$joinTableAlias = '';

					if ($templateId && isset($fullData['jform']['custom_table'][$tableId]))
					{
						$tableName      = $fullData['jform']['custom_table'][$tableId]['table'];
						$tableAlias     = $fullData['jform']['custom_table'][$tableId]['tablealias'];

						if (isset($fullData['jform']['custom_table'][$tableId]['jointable']))
						{
							$joinTableName = $fullData['jform']['custom_table'][$tableId]['jointable'];
						}

						if (isset($fullData['jform']['custom_table'][$tableId]['jointablealias']))
						{
							$joinTableAlias = $fullData['jform']['custom_table'][$tableId]['jointablealias'];
						}

						if ($tableAlias || $tableName)
						{
							$query->clear()
								->update($this->db->quoteName('#__csvi_templatefields'))
								->set($this->db->quoteName('table_alias') . ' = ' . $this->db->quote($tableAlias))
								->where($this->db->quoteName('table_name') . ' = ' . $this->db->quote($tableName))
								->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $table->get('csvi_template_id'));
							$this->db->setQuery($query)->execute();
						}

						if ($joinTableAlias || $joinTableName)
						{
							$query->clear()
								->update($this->db->quoteName('#__csvi_templatefields'))
								->set($this->db->quoteName('table_alias') . ' = ' . $this->db->quote($joinTableAlias))
								->where($this->db->quoteName('table_name') . ' = ' . $this->db->quote($joinTableName))
								->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $table->get('csvi_template_id'));
							$this->db->setQuery($query)->execute();
						}

						$customTables[] = $tableName;

						if ($joinTableName)
						{
							$customTables[] = $joinTableName;
						}
					}
				}

				array_unique($customTables);
				$fullData['jform']['custom_table']['custom_table0'] = array_merge(
					$fullData['jform']['custom_table']['custom_table0'],
					array('jointable' => '', 'joinfield' => '', 'field' => '', 'jointype' => '', 'jointablealias' => '')
				);

				$fullData['settings'] = json_encode($fullData['jform']);
			}
			else
			{
				$customTables[] = $fullData['jform']['custom_table'];

				// If user changes export template to import or vice versa then take the default first table
				if (isset($fullData['jform']['custom_table']['custom_table0']['table']))
				{
					$customTables = array();
					$customTables[] = $fullData['jform']['custom_table']['custom_table0']['table'];
				}
			}

			// Index the custom table and add available fields table
			require_once JPATH_PLUGINS . '/csviaddon/csvi/com_csvi/model/maintenance.php';
			$maintenanceModel = new Com_CsviMaintenance($this->db, $log, $csvihelper);
			$maintenanceModel->saveCustomTableAvailableFields($customTables, $fullData['action']);
		}

		// Check if the chosen table is the same as the one already stored, if not, we need to remove the template fields
		$settings          = json_decode($table->get('settings'));

		if (isset($settings->custom_table))
		{
			$customTablesArray          = json_decode(json_encode($settings->custom_table), true);
			$currentSavedTables         = $fullData['jform']['custom_table'];
			$includeTableCheck          = true;
			$differenceCustomTableArray = $this->checkCustomTableArrays($customTablesArray, $currentSavedTables);

			// Clean left over template fields for multiple table joins
			if (count($currentSavedTables) > 1)
			{
				$this->cleanTemplateFields($currentSavedTables, $table->get('csvi_template_id'));
			}

			// If it is a single table, check and delete the template fields if tables are different
			if (empty($differenceCustomTableArray) && count($customTablesArray) === 1 && count($currentSavedTables) === 1)
			{
				if ($customTablesArray['custom_table0']['table'] !== $currentSavedTables['custom_table0']['table'])
				{
					$differenceCustomTableArray = $customTablesArray;
					$includeTableCheck          = false;
				}
			}

			// If there is a difference in array, delete associated template fields
			if ($differenceCustomTableArray)
			{
				foreach ($differenceCustomTableArray as $key => $differenceCustomTable)
				{
					$tableName      = $differenceCustomTable['table'];
					$tableAliasName = $differenceCustomTable['tablealias'];
					$query->clear()
						->delete($this->db->quoteName('#__csvi_templatefields'))
						->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $table->get('csvi_template_id'));

					if ($tableName && $includeTableCheck)
					{
						$query->where($this->db->quoteName('table_name') . ' = ' . $this->db->quote($tableName));
					}

					if ($tableAliasName && !$tableName && $includeTableCheck)
					{
						$query->where($this->db->quoteName('table_alias') . ' = ' . $this->db->quote($tableAliasName));
					}

					$this->db->setQuery($query)->execute();

					// Clean Group and Sort by fields
					$groupByArray  = json_decode($settings->groupbyfields);
					$sortByArray   = json_decode($settings->sortfields);
					$newGroupArray = new stdClass();
					$newSortArray  = new stdClass();

					if ($groupByArray)
					{
						foreach ($groupByArray->groupby_table_name as $groupKey => $groupTableName)
						{
							if ($tableName === $groupTableName)
							{
								unset($groupByArray->groupby_table_name[$groupKey]);
								unset($groupByArray->groupby_field_name[$groupKey]);
							}
							else
							{
								$newGroupArray->groupby_table_name[] = $groupTableName;
								$newGroupArray->groupby_field_name[] = $groupByArray->groupby_field_name[$groupKey];
							}
						}

						$fullData['jform']['groupbyfields'] = [];

						if ((array) $newGroupArray)
						{
							$fullData['jform']['groupbyfields'] = json_encode($newGroupArray);
						}
					}

					if($sortByArray)
					{
						foreach ($sortByArray->sortby_table_name as $sortKey => $sortTableName)
						{
							if ($tableName === $sortTableName)
							{
								unset($sortByArray->sortby_table_name[$sortKey]);
								unset($sortByArray->sortby_field_name[$sortKey]);
								unset($sortByArray->sortby_field_name_order[$sortKey]);
							}
							else
							{
								$newSortArray->sortby_table_name[]       = $sortTableName;
								$newSortArray->sortby_field_name[]       = $sortByArray->sortby_field_name[$sortKey];
								$newSortArray->sortby_field_name_order[] = $sortByArray->sortby_field_name_order[$sortKey];
							}
						}

						$fullData['jform']['sortfields'] = [];

						if ((array) $newSortArray)
						{
							$fullData['jform']['sortfields'] = json_encode($newSortArray);
						}
					}
				}

				$fullData['settings'] = json_encode($fullData['jform']);
			}
		}

		return parent::save($fullData);
	}

	/**
	 * Method to check difference in custom table array saved
	 *
	 * @param   array   $existingArray Existing array in database
	 * @param   array   $savedArray    Current saved array
	 *
	 * @return  array  Return difference of array
	 *
	 * @since   7.17.0
	 */
	private function checkCustomTableArrays($existingArray, $savedArray)
	{
		$deletedRow = [];

		foreach ($existingArray as $key => $val)
		{
			if (!array_key_exists($key, $savedArray))
			{
				$deletedRow[$key] = $val;
			}
		}

		return $deletedRow;
	}

	/**
	 * Method to clean leftover template fields
	 *
	 * @param   array $savedCustomDetails Custom field table array
	 * @param   int   $templateId         Template id
	 *
	 * @return  mixed  Return false if the custom fields array is empty
	 *
	 * @since   7.15.1
	 */
	private function cleanTemplateFields($savedCustomDetails, $templateId)
	{
		if (!$savedCustomDetails)
		{
			return false;
		}

		$savedTables = [];

		foreach ($savedCustomDetails as $customKey => $customDetails)
		{
			if (isset($customDetails['table']) && $customDetails['table'])
			{
				$savedTables[] = "'" . $customDetails['table'] . "'";

				if ($customDetails['jointable'])
				{
					$savedTables[] = "'" . $customDetails['jointable'] . "'";
				}
			}
		}

		$tablesList = array_unique($savedTables);

		if ($templateId && $tablesList)
		{
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__csvi_templatefields'))
				->where($this->db->quoteName('csvi_template_id') . ' = ' . $this->db->quote($templateId))
				->where($this->db->quoteName('table_name') . ' NOT IN ( ' . implode(',', $tablesList) . ')');
			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   12.2
	 */
	public function validate($form, $data, $group = null)
	{
		return $data;
	}

	/**
	 * Delete a template
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  Return false to raise an error, true otherwise
	 *
	 * @throws  RuntimeException
	 *
	 * @since   3.0
	 */
	public function delete(&$pks)
	{
		foreach ($pks as $pk)
		{
			// Delete the temporary table if any
			$template         = $this->getItem( $pk );
			$templateSettings = new Registry( $template->settings );
			$tableName        = $templateSettings->get( 'localtablelist', '' );

			if ($tableName !== ''
				&& $templateSettings->get('source') === 'fromdatabase'
			)
			{
				$query = 'DROP TABLE IF EXISTS ' . $this->db->quoteName('#__' . $tableName);
				$this->db->setQuery( $query )->execute();
			}

			// Delete the template field rules
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('csvi_templatefield_id'))
				->from($this->db->quoteName('#__csvi_templatefields'))
				->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $pk);
			$this->db->setQuery($query);
			$fieldIds = $this->db->loadColumn();

			if ($fieldIds)
			{
				// Delete the rules
				$query->clear()
					->delete($this->db->quoteName('#__csvi_templatefields_rules'))
					->where($this->db->quoteName('csvi_templatefield_id') . ' IN (' . implode(',', $fieldIds) . ')');
				$this->db->setQuery($query)->execute();
			}

			// Delete the template fields
			$query->clear()
				->delete($this->db->quoteName('#__csvi_templatefields'))
				->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $pk);
			$this->db->setQuery($query)->execute();
		}

		return parent::delete($pks);
	}

	/**
	 * Test the FTP details.
	 *
	 * @return  bool  True if connection works | Fails if connection fails.
	 *
	 * @since   4.3.2
	 *
	 * @throws  CsviException
	 * @throws  InvalidArgumentException
	 */
	public function testFtp()
	{
		$ftphost     = $this->input->get('ftphost', '', 'string');
		$ftpport     = $this->input->get('ftpport');
		$ftpusername = $this->input->get('ftpusername', '', 'string');
		$ftppass     = $this->input->get('ftppass', '', 'string');
		$ftproot     = $this->input->get('ftproot', '', 'string');
		$ftpfile     = $this->input->get('ftpfile', '', 'string');
		$action      = $this->input->get('action');
		$sftp		 = $this->input->getInt('sftp', 0);

		if ($sftp)
		{
			return $this->testSftp();
		}

		// Set up the ftp connection
		jimport('joomla.client.ftp');
		$ftp = JClientFtp::getInstance($ftphost, $ftpport, array(), $ftpusername, $ftppass);

		try
		{
			// Try to login again because Joomla! doesn't let us know when the username and/or password is wrong
			if ($ftp->isConnected())
			{
				// See if we can change folder
				if ($ftp->chdir($ftproot))
				{
					$result = true;

					if ($action === 'import' && $ftpfile)
					{
						// Check if the file exists
						$files = $ftp->listNames();

						if (!is_array($files))
						{
							throw new CsviException(JText::sprintf('COM_CSVI_FTP_NO_FILES_FOUND', $ftp->pwd()));
						}

						if (!in_array($ftpfile, $files, true))
						{
							throw new CsviException(JText::sprintf('COM_CSVI_FTP_FILE_NOT_FOUND', $ftpfile, $ftp->pwd()));
						}
					}
				}
				else
				{
					throw new CsviException(JText::sprintf('COM_CSVI_FTP_FOLDER_NOT_FOUND', $ftproot));
				}
			}
			else
			{
				throw new InvalidArgumentException(JText::_('COM_CSVI_FTP_CREDENTIALS_INVALID'));
			}

			// Close up
			$ftp->quit();
		}
		catch (Exception $e)
		{
			// Close up
			$ftp->quit();

			throw new CsviException($e->getMessage(), $e->getCode());
		}

		return $result;
	}

	/**
	 * Test the FTP details.
	 *
	 * @return  bool  True if connection works | Fails if connection fails.
	 *
	 * @since   4.3.2
	 *
	 * @throws  CsviException
	 * @throws  InvalidArgumentException
	 */
	public function testSftp()
	{
		$ftphost     = $this->input->get('ftphost', '', 'string');
		$ftpport     = $this->input->get('ftpport');
		$ftpusername = $this->input->get('ftpusername', '', 'string');
		$ftppass     = $this->input->get('ftppass', '', 'string');
		$ftproot     = $this->input->get('ftproot', '', 'string');
		$ftpfile     = $this->input->get('ftpfile', '', 'string');
		$action      = $this->input->get('action');

		try
		{
			$sftp = new SFTP($ftphost, $ftpport);

			if (!$sftp->login($ftpusername, $ftppass))
			{
				throw new InvalidArgumentException(JText::_('COM_CSVI_FTP_CREDENTIALS_INVALID'));
			}

			// See if we can change folder
			if ($sftp->chdir($ftproot))
			{
				$result = true;

				if ($action === 'import' && $ftpfile)
				{
					// Check if the file exists
					$files = $sftp->nlist();

					if (!is_array($files))
					{
						throw new CsviException(JText::sprintf('COM_CSVI_FTP_NO_FILES_FOUND', $sftp->pwd()));
					}

					if (!in_array($ftpfile, $files, true))
					{
						throw new CsviException(JText::sprintf('COM_CSVI_FTP_FILE_NOT_FOUND', $ftpfile, $sftp->pwd()));
					}
				}
			}
			else
			{
				throw new CsviException(JText::sprintf('COM_CSVI_FTP_FOLDER_NOT_FOUND', $ftproot));
			}
		}
		catch (Exception $e)
		{
			throw new CsviException($e->getMessage(), $e->getCode());
		}

		return $result;
	}

	/**
	 * Test if the URL exists.
	 *
	 * @return  bool  True if URL exists | Fails otherwise.
	 *
	 * @since   6.5.0
	 *
	 * @throws  CsviException
	 */
	public function testURL()
	{
		$testurl            = base64_decode($this->input->get('testurl', '', 'base64'));
		$testuser           = $this->input->get('testuser', '', 'string');
		$testuserfield      = $this->input->get('testuserfield', '', 'string');
		$testpass           = $this->input->get('testpass', '', 'string');
		$testpassfield      = $this->input->get('testpassfield', '', 'string');
		$testmethod         = $this->input->get('testmethod', 'GET', 'string');
		$testcredentialtype = $this->input->get('testcredentialtype', 'htaccess', 'string');
		$encodeURL          = $this->input->get('encodeurl', '1', 'int');
		$csvihelper         = new CsviHelperCsvi;

		if (!$csvihelper->fileExistsRemote($testurl, $testuser, $testpass, $testmethod, $testuserfield, $testpassfield, $testcredentialtype, $encodeURL))
		{
			throw new CsviException(JText::_('COM_CSVI_URL_TEST_NO_SUCCESS'));
		}

		return true;
	}

	/**
	 * Test if the server path is valid.
	 *
	 * @return  bool  True if URL exists | Fails otherwise.
	 *
	 * @since   6.5.0
	 *
	 * @throws  CsviException
	 * @throws  UnexpectedValueException
	 */
	public function testPath()
	{
		jimport('joomla.filesystem.folder');

		$testPath = $this->input->get('testpath', '', 'string');

		$file = JPath::clean($testPath, '/');

		// If the given path is a folder or file, check if its valid
		if (!JFolder::exists($file) && (!JFile::exists($file)))
		{
			throw new CsviException(JText::_('COM_CSVI_PATH_TEST_NO_SUCCESS'));
		}

		return true;
	}

	/**
	 * Copy one ore more templates to a new one.
	 *
	 * @param   array  $templateIds  The IDs of the template(s) to copy.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @throws  CsviException
	 * @throws  RuntimeException
	 *
	 * @since   6.0
	 */
	public function createCopy($templateIds)
	{
		if (!is_array($templateIds))
		{
			$templateIds = (array) $templateIds;
		}

		$table = $this->getTable();

		foreach ($templateIds as $templateId)
		{
			$table->load($templateId);
			$templateAlias = JApplicationHelper::stringURLSafe($table->get('template_name'));
			$templateAlias = $this->generateDuplicateAlias($templateAlias);
			$table->set('csvi_template_id', 0);
			$table->set('lastrun', $this->db->getNullDate());
			$table->set('template_name', $table->get('template_name') . ' copy');
			$table->set('template_alias', $templateAlias);

			if ($table->store())
			{
				// Copy also the template fields
				$query = $this->db->getQuery(true)
					->select($this->db->quoteName('csvi_templatefield_id'))
					->from($this->db->quoteName('#__csvi_templatefields'))
					->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $templateId);
				$this->db->setQuery($query);
				$fieldIds = $this->db->loadColumn();

				$ftable = $this->getTable('Templatefield');

				foreach ($fieldIds as $fieldId)
				{
					$ftable->load($fieldId);
					$ftable->set('csvi_templatefield_id', 0);
					$ftable->set('csvi_template_id', $table->get('csvi_template_id'));
					$ftable->store();

					// Copy the template field rules
					$query->clear()
						->select($ftable->get('csvi_templatefield_id'))
						->select($this->db->quoteName('csvi_rule_id'))
						->from($this->db->quoteName('#__csvi_templatefields_rules'))
						->where($this->db->quoteName('csvi_templatefield_id') . ' = ' . (int) $fieldId);
					$this->db->setQuery($query);
					$templatefieldruleIds = $this->db->loadAssocList();

					if (count($templatefieldruleIds) > 0)
					{
						$query->clear()
							->insert($this->db->quoteName('#__csvi_templatefields_rules'))
							->columns(
								$this->db->quoteName(
									array(
										'csvi_templatefield_id',
										'csvi_rule_id'
									)
								)
							);

						foreach ($templatefieldruleIds as $rule)
						{
							$query->values(implode(',', $rule));
						}

						$this->db->setQuery($query)->execute();
					}
				}
			}
			else
			{
				throw new CsviException(JText::sprintf('COM_CSVI_CANNOT_COPY_TEMPLATE', $table->getError()));
			}
		}

		return true;
	}

	/**
	 * Test the database connection details.
	 *
	 * @return  bool  True if connection works | Fails if connection fails.
	 *
	 * @since   6.7.0
	 *
	 * @throws  CsviException
	 * @throws  InvalidArgumentException
	 */
	public function testDbConnection()
	{
		$details              = array();
		$details['user']      = $this->input->get('dbusername', '', 'string');
		$details['password']  = $this->input->get('dbpassword', '', 'string');
		$details['database']  = $this->input->get('dbname', '', 'string');
		$portNo               = $this->input->get('dbportno');
		$hostName             = $this->input->get('dburl', '', 'string');
		$tableName            = $this->input->get('dbtable', '', 'string');
		$action               = $this->input->get('action', '', 'string');
		$createDatabase       = false;
		$createTable          = false;

		if ($action != 'import')
		{
			$createTable = true;
		}

		if ((strpos($hostName, ':') === false) && $portNo)
		{
			$hostName = $hostName . ':' . $portNo;
		}

		$details['host'] = $hostName;
		$database        = JDatabaseDriver::getInstance($details);

		try
		{
			$database->connect();
			$database->connected();

			if ($createTable)
			{
				// Create table if not exists if connected to database
				$query = "CREATE TABLE IF NOT EXISTS" . $database->quoteName($tableName) . "  (
			" . $database->quoteName('id') . " int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (" . $database->quoteName('id') . ")) CHARSET=utf8";
				$database->setQuery($query)->execute();
			}
		}
		catch (Exception $e)
		{
			if ($e->getMessage() === 'Could not connect to database.' && $createTable)
			{
				$createDatabase = true;
			}
			else
			{
				throw new CsviException(JText::sprintf('COM_CSVI_DBCONNECTION_TEST_NO_SUCCESS', $e->getMessage()));
			}
		}

		// If there is no database, create one
		if ($createDatabase)
		{
			// Create database
			$options          = new stdClass;
			$options->db_name = $details['database'];
			$options->db_user = $details['user'];
			$database->createDatabase($options);

			if ($database->select($details['database']))
			{
				// Create table if not exists
				$query = "CREATE TABLE IF NOT EXISTS" . $database->quoteName($tableName) . "  (
				" . $database->quoteName('id') . " int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (" . $database->quoteName('id') . ")) CHARSET=utf8";

				try
				{
					$database->setQuery($query);
					$database->execute();
				}
				catch (Exception $e)
				{
					throw new CsviException(JText::sprintf('COM_CSVI_TABLE_CREATE_NOT_SUCCESS', $e->getMessage()));
				}
			}
		}

		$database->disconnect();

		return true;
	}

	/**
	 * Get custom table fields columns
	 *
	 * @param   string  $table  Table name
	 *
	 * @return  array $columns The fields names.
	 *
	 * @since   7.2.0
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function getTableColumns($table)
	{
		$columns = array();
		$tableName = $this->db->getPrefix() . $table;
		$tableColumns = $this->db->getTableColumns($tableName);

		foreach ($tableColumns as $keyField => $field)
		{
			if ($field === 'int' || $field === 'int unsigned')
			{
				$columns[$keyField] = $keyField;
			}
		}

		return array('columns' => $columns);
	}

	/**
	 * Get available fields
	 *
	 * @param   int  $templateId  Template Id
	 *
	 * @return  array $fields The fields names.
	 *
	 * @since   7.8.0
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function getAvailableFields($templateId)
	{
		$item = $this->getItem($templateId);
		$settings = json_decode($item->settings);
		$component = $settings->component;
		$operation = $settings->operation;
		$action = $settings->action;

		// Call the Availablefields model
		$model = JModelLegacy::getInstance('Availablefields', 'CsviModel', array('ignore_request' => true));

		// Set a default filters
		$model->setState('filter_order', 'csvi_name');
		$model->setState('filter_order_Dir', 'DESC');
		$model->setState('filter.component', $component);
		$model->setState('filter.operation', $operation);
		$model->setState('filter.action', $action);
		$fields = $model->getItems();

		$avFields = array();

		foreach ($fields as $field)
		{
			$avFields[] = $field->csvi_name;
		}

		return array_unique($avFields);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Get the template information
		$item->options    = new Registry(json_decode($item->settings, true));
		$templateArray    = json_decode(json_encode($item), true);
		$templateSettings = json_decode(ArrayHelper::getValue($templateArray, 'settings'), true);

		if ($templateSettings && array_key_exists('custom_table', $templateSettings))
		{
			$item->count_tables = is_array($templateSettings['custom_table']) ? count($templateSettings['custom_table']) : 1;
		}

		return $item;
	}

	/**
	 * Save the mapped template fields from the wizard
	 *
	 * @param   int   $templateId Template Id
	 * @param  string $fields     Mapped fields
	 *
	 * @since   7.8.0
	 *
	 * @throws  Exception
	 */
	public function mapTemplateFields($templateId, $fields)
	{
		$fieldNames     = explode(',', $fields);
		$templateFields = JTable::getInstance('Templatefield', 'Table');

		foreach ($fieldNames as $order => $field)
		{
			if ($field)
			{
				$saveField                        = new stdClass;
				$saveField->csvi_templatefield_id = 0;
				$saveField->csvi_template_id      = $templateId;
				$saveField->field_name            = $field;
				$saveField->enabled               = 1;
				$saveField->ordering              = $order + 1;

				$templateFields->save($saveField);
				$templateFields->reset();
			}
		}
	}

	/**
	 * Get a list of templates.
	 *
	 * @param  array  $templateIds  Template ids to backup
	 *
	 * @return  string  File location
	 *
	 * @since   7.12.0
	 *
	 */
	public function backupTemplates($templateIds)
	{
		$settings         = new CsviHelperSettings($this->db);
		$log              = new CsviHelperLog($settings, $this->db);
		$helper           = new CsviHelperCsvi();
		$maintenanceModel = new Com_CsviMaintenance($this->db, $log, $helper, false);
		$input            = JFactory::getApplication()->input;
		$input->set('templates', $templateIds);
		$maintenanceModel->backupTemplates($input);
		$domain   = JUri::getInstance()->toString(array('host'));
		$filename = 'csvi_templates_' . $domain . '_' . date('Ymd', time()) . '.xml';
		$file     = JPath::clean(CSVIPATH_TMP . '/' . $filename, '/');

		return $file;
	}

	/**
	 * Get custom table fields columns
	 *
	 * @return  array $tables The list of tables.
	 *
	 * @since   7.14.0
	 *
	 */
	public function getTableList()
	{
		$tables = $this->db->getTableList();

		return $tables;
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
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.template.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to create temporary table for bridge template
	 *
	 * @param  int      $importTemplateId  Import template ID.
	 * @param  int      $exportTemplateId  Export template ID.
	 * @param  string   $exportField       Export field name
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   7.15.0
	 *
	 */
	public function createBridgeTemplateTable($importTemplateId, $exportTemplateId, $exportField)
	{
		$importTemplateSettings      = $this->getItem($importTemplateId);
		$importTemplateArray         = json_decode(json_encode($importTemplateSettings), true);
		$importTemplateSettingsArray = json_decode(ArrayHelper::getValue($importTemplateArray, 'settings'), true);
		$importExtension             = str_replace('com_', '', $importTemplateSettingsArray['component']);
		$importField                 = 'field_' . $importExtension;

		// Create temporary table to use for import
		$tableName = '#__csvi_importto_' . $importExtension . '_' . $importTemplateId;
		$query     = 'CREATE TABLE IF NOT EXISTS ' . $this->db->quoteName($tableName) . ' (' .
			$this->db->quoteName('id') . ' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,' .
			$this->db->quoteName('template_id') . ' INT(10) NOT NULL, PRIMARY KEY (' . $this->db->quoteName('id') . '))';
		$this->db->setQuery($query)->execute();

		// Get template fields of export template
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('field_name', 'csvi_templatefield_id')))
			->from($this->db->quoteName('#__csvi_templatefields'))
			->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $exportTemplateId);
		$this->db->setQuery($query);
		$fieldNames = $this->db->loadObjectList();

		if ($fieldNames)
		{
			$query->clear()
				->select($this->db->quoteName($importField))
				->from($this->db->quoteName('#__csvi_mappedfields'));

			foreach ($fieldNames as $fieldName)
			{
				$query->clear('where')
					->where($this->db->quoteName($exportField) . ' = ' . $this->db->quote($fieldName->field_name));
				$this->db->setQuery($query);
				$mappedFieldName = $this->db->loadResult();
				$templateFieldId = $fieldName->csvi_templatefield_id;

				$updateQuery = $this->db->getQuery(true)
					->update($this->db->quoteName('#__csvi_templatefields'))
					->set($this->db->quoteName('column_header') . ' = ' . $this->db->quote($mappedFieldName))
					->where($this->db->quoteName('csvi_templatefield_id') . ' = ' . (int) $templateFieldId);
				$this->db->setQuery($updateQuery)->execute();

				if (!$mappedFieldName)
				{
					$mappedFieldName = $fieldName->field_name;
				}

				$existingFields = $this->db->getTableColumns($tableName);
				$fieldExists    = array_key_exists($mappedFieldName, $existingFields);

				// Alter the temporary table to add field names
				if (!$fieldExists)
				{
					$alterQuery = 'ALTER TABLE ' . $this->db->quoteName($tableName) . ' ADD ' . $this->db->quoteName($mappedFieldName) . ' TEXT NOT NULL';
					$this->db->setQuery($alterQuery)->execute();
				}
			}
		}
	}

	/**
	 * Method to generate alias using template name
	 *
	 * @param  string  $templateName   Template name
	 *
	 * @return  string Template alias
	 *
	 * @throws  Exception
	 *
	 * @since   7.15.0
	 *
	 */
	public function generateDuplicateAlias($templateAlias)
	{
		if (!$templateAlias)
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('csvi_template_id'))
			->select($this->db->quoteName('template_alias'))
			->from($this->db->quoteName('#__csvi_templates'))
			->where($this->db->quoteName('template_alias') . ' LIKE  ' . $this->db->quote('%' . $templateAlias . '%'));
		$this->db->setQuery($query);
		$templateResults = $this->db->loadObjectList();

		if ($templateResults)
		{
			$totalExisting = count($templateResults);
			$existingAlias = $templateResults[$totalExisting - 1]->template_alias;
			$lastCount     = substr($existingAlias, -1);

			if (is_numeric($lastCount))
			{
				$templateAlias = $templateAlias . '-' . ++$lastCount;
			}
			else
			{
				$templateAlias = $templateAlias . '-1';
			}
		}

		return $templateAlias;
	}
}
