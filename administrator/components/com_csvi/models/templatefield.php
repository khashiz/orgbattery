<?php
/**
 * @package     CSVI
 * @subpackage  Templatefields
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * The template fields model.
 *
 * @package     CSVI
 * @subpackage  Templatefields
 * @since       6.0
 */
class CsviModelTemplatefield extends JModelAdmin
{
	/**
	 * Holds the database driver
	 *
	 * @var    JDatabaseDriver
	 * @since  6.0
	 */
	private $db;

	/**
	 * Holds the input object
	 *
	 * @var    JInput
	 * @since  6.6.0
	 */
	private $input;

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
		$this->db    = $this->getDbo();
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
	 *
	 * @throws  Exception
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Default action
		$action             = 'import';
		$exportXml          = false;
		$exportCustomTable  = false;

		// Get the template
		$template = $this->getState('template');
		$csvi_template_id = $this->input->post->getInt('csvi_template_id', 0);

		if (!$template && $csvi_template_id)
		{
			$template = new CsviHelperTemplate($csvi_template_id);
		}

		if ($template)
		{
			$action            = $template->get('action');
			$exportXml         = $template->get('export_file') === 'xml' ? true : false;
			$exportCustomTable = $template->get('custom_table') === '' ? false : true;
		}

		// Get the form.
		$form = $this->loadForm('com_csvi.templatefield', 'templatefield_' . $action, array('control' => 'jform', 'load_data' => $loadData));

		if (is_null($form))
		{
			return false;
		}

		// Remove the XML field if not used
		if ($exportXml === false)
		{
			$form->removeField('cdata');
		}

		// Get the number of custom tables added in template
		$customTables = $template->get('custom_table');

		// Remove the export custom table field if not needed
		if ($exportCustomTable === false || count((array) $customTables) === 1)
		{
			$form->removeField('table_name');
			$form->removeField('table_alias');
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
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.templatefield.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
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
	 * @throws  RuntimeException
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Set the template ID
		$item->csvi_template_id = $this->getState('filter.csvi_template_id', $this->input->getInt('csvi_template_id', $item->get('csvi_template_id')));

		// Load the rule IDs
		$item->rules = $this->loadRules($item->csvi_templatefield_id);

		if (!$item->rules)
		{
			$item->rules = '';
		}

		// Check the source from to import
		$templateModel      = JModelLegacy::getInstance('Template', 'CsviModel', array('ignore_request' => true));
		$templates          = $templateModel->getItem($item->csvi_template_id);
		$templateArray      = json_decode(json_encode($templates), true);
		$templateSettings   = json_decode(ArrayHelper::getValue($templateArray, 'settings'), true);
		$item->fromdatabase = 0;

		if (isset($templateSettings['source']) && $templateSettings['source'] === 'fromdatabase')
		{
			$item->fromdatabase = 1;
		}

		return $item;
	}

	/**
	 * Load the rules for a given field.
	 *
	 * @param   int  $csvi_templatefield_id  The ID of the field to get the rules for.
	 *
	 * @return  array  List of rules.
	 *
	 * @since   6.2.0
	 *
	 * @throws  RuntimeException
	 */
	private function loadRules($csvi_templatefield_id)
	{
		// Load the rule IDs
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('csvi_rule_id'))
			->from($this->db->quoteName('#__csvi_templatefields_rules'))
			->where($this->db->quoteName('csvi_templatefield_id') . ' = ' . (int) $csvi_templatefield_id);
		$this->db->setQuery($query);

		return $this->db->loadColumn();
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   6.6.0
	 *
	 * @throws  RuntimeException
	 */
	public function delete(&$pks)
	{
		foreach ($pks as $pk)
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName(array('csvi_template_id', 'field_name')))
				->from($this->db->quoteName('#__csvi_templatefields'))
				->where($this->db->quoteName('csvi_templatefield_id') . ' = ' . (int) $pk);
			$this->db->setQuery($query);
			$fieldDetails = $this->db->loadObject();
			$this->getMappedFieldColumnHeader($fieldDetails->csvi_template_id, $fieldDetails->field_name, 'delete');

			$query->clear()
				->delete($this->db->quoteName('#__csvi_templatefields_rules'))
				->where($this->db->quoteName('csvi_templatefield_id') . ' = ' . (int) $pk);
			$this->db->setQuery($query)->execute();
		}

		return parent::delete($pks);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   6.6.0
	 *
	 * @throws  RuntimeException
	 */
	public function save($data)
	{
		// Check if we need to map fields for importing to a template
		$fieldName = $this->getMappedFieldColumnHeader($data['csvi_template_id'], $data['field_name'], 'add');

		if ($fieldName)
		{
			$data['column_header'] = $fieldName;
		}

		// Auto increment ordering if not set by user
		if (0 === $data['ordering'])
		{
			// Get the highest ordering number from db
			$query = $this->db->getQuery(true)
				->select('MAX(' . $this->db->quoteName('ordering') . ')')
				->from($this->db->quoteName('#__csvi_templatefields'))
				->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $data['csvi_template_id']);
			$this->db->setQuery($query);
			$ordering = $this->db->loadResult();

			if ($ordering > 0)
			{
				$data['ordering'] = ++$ordering;
			}
		}

		if (parent::save($data))
		{
			if (array_key_exists('rules', $data))
			{
				// Remove all rule IDs
				$query = $this->db->getQuery(true)
					->delete($this->db->quoteName('#__csvi_templatefields_rules'))
					->where($this->db->quoteName('csvi_templatefield_id') . ' = ' . (int) $data['csvi_templatefield_id']);
				$this->db->setQuery($query)->execute();

				// Store rule IDs
				$rule_table = JTable::getInstance('Templatefields_rules', 'Table');

				foreach ($data['rules'] as $rule_id)
				{
					if (!empty($rule_id))
					{
						$rule_table->save(array('csvi_templatefield_id' => $data['csvi_templatefield_id'], 'csvi_rule_id' => $rule_id));
						$rule_table->set('csvi_templatefields_rule_id', null);
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Store a template field.
	 *
	 * @return  bool  Always returns true
	 *
	 * @since   4.3
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function storeTemplateField()
	{
		// Collect the data
		$data        = array();
		$fieldNames  = explode('~', $this->input->get('field_name', '', 'string'));
		$template_id = $this->input->getInt('template_id', 0);

		// Get the highest field number
		$query = $this->db->getQuery(true)
			->select('MAX(' . $this->db->quoteName('ordering') . ')')
			->from($this->db->quoteName('#__csvi_templatefields'))
			->where($this->db->quoteName('csvi_template_id') . ' = ' . (int) $template_id);
		$this->db->setQuery($query);
		$ordering = $this->db->loadResult();

		foreach ($fieldNames as $fieldname)
		{
			if ($fieldname)
			{
				// Check if we need to map fields for importing to a template
				$columnHeaderName         = $this->getMappedFieldColumnHeader($template_id, $fieldname, 'add');
				$table                    = $this->getTable('Templatefield');
				$data['csvi_template_id'] = $template_id;
				$data['ordering']         = ++$ordering;
				$data['field_name']       = $fieldname;
				$data['file_field_name']  = $this->input->get('file_field_name', '', 'string');
				$data['column_header']    = $this->input->get('column_header', '', 'string');

				if ($columnHeaderName)
				{
					$data['column_header'] = $columnHeaderName;
				}

				$data['default_value'] = $this->input->get('default_value', '', 'string');
				$data['enabled']       = $this->input->get('enabled', 1, 'int');
				$data['sort']          = $this->input->get('sort', 0, 'int');
				$table->bind($data);

				if (!$table->store())
				{
					throw new CsviException(JText::_('COM_CSVI_STORE_TEMPLATE_FIELD_FAILED'), 500);
				}
			}
		}

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'csvi_template_id = ' . (int) $table->csvi_template_id;

		return $condition;
	}

	/**
	 * Get custom table fields
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
			$columns[$keyField] = $keyField;
		}

		return array('columns' => $columns);
	}

	/**
	 * Get template settings
	 *
	 * @param   int  $templateId  Template id
	 *
	 * @return  mixed $settings The template settings| false if there is no template id set.
	 *
	 * @since   7.13.0
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	private function getTemplateSettings($templateId)
	{
		if(!$templateId)
		{
			return false;
		}

		$templateModel      = JModelLegacy::getInstance('Template', 'CsviModel');
		$templates          = $templateModel->getItem($templateId);
		$templateArray      = json_decode(json_encode($templates), true);
		$templateSettings   = json_decode(ArrayHelper::getValue($templateArray, 'settings'), true);

		return $templateSettings;
	}

	/**
	 * Get mapped field column header
	 *
	 * @param   int     $templateId Template id
	 * @param   string  $fieldName  Field name
	 * @param   string  $action     To add or delete field name
	 *
	 * @return  mixed $columnHeaderName The column header name| false if there is no fieldname.
	 *
	 * @since   7.13.0
	 *
	 */
	private function getMappedFieldColumnHeader($templateId, $fieldName, $action)
	{
		$exportTemplateSettings = $this->getTemplateSettings($templateId);
		$exportExtension        = str_replace('com_', '', $exportTemplateSettings['component']);
		$sourceSetting          = $exportTemplateSettings['exportto'] ?? [];
		$exportField            = 'field_' . $exportExtension;
		$importField            = '';
		$tableName              = '';

		foreach ($sourceSetting as $source)
		{
			if (is_numeric($source))
			{
				$importTemplateSettings = $this->getTemplateSettings($source);
				$importExtension        = str_replace('com_', '', $importTemplateSettings['component']);
				$importField            = 'field_' . $importExtension;

				// Create temporary table to use for import
				$tableName = '#__csvi_importto_' . $importExtension . '_' . $source;

				if ($action === 'add')
				{
					$query = 'CREATE TABLE IF NOT EXISTS ' . $this->db->quoteName($tableName) . ' (' .
						$this->db->quoteName('id') . ' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,' .
						$this->db->quoteName('template_id') . ' INT(10) NOT NULL, PRIMARY KEY (' . $this->db->quoteName('id') . '))';
					$this->db->setQuery($query)->execute();
				}
			}
		}

		if ($importField)
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName($importField))
				->from($this->db->quoteName('#__csvi_mappedfields'))
				->where($this->db->quoteName($exportField) . ' = ' . $this->db->quote($fieldName));
			$this->db->setQuery($query);
			$columnHeaderName = $this->db->loadResult();

			if (!$columnHeaderName)
			{
				$columnHeaderName = $fieldName;
			}

			$existingFields = $this->db->getTableColumns($tableName);
			$fieldExists   = array_key_exists($columnHeaderName, $existingFields);

			// Alter the temporary table to add field names
			if ($tableName && $action === 'add')
			{
				if (!$fieldExists)
				{
					$alterQuery = 'ALTER TABLE' . $this->db->quoteName($tableName) . ' ADD ' .
						$this->db->quoteName($columnHeaderName) . ' TEXT NOT NULL';
					$this->db->setQuery($alterQuery)->execute();
				}
			}

			// Alter the temporary table to remove field names
			if ($tableName && $action === 'delete' && $fieldExists)
			{
				$deleteQuery = 'ALTER TABLE' . $this->db->quoteName($tableName) . ' DROP ' . $this->db->quoteName($columnHeaderName);
				$this->db->setQuery($deleteQuery)->execute();
			}

			return $columnHeaderName;
		}
	}
}
