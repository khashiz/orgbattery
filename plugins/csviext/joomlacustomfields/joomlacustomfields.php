<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Joomlacustomfields
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Joomla Custom fields.
 *
 * @package     CSVI
 * @subpackage  Plugin.Joomlacustomfields
 * @since       7.15.0
 */
class PlgCsviextJoomlacustomfields extends RantaiPluginDispatcher
{
	/**
	 * The unique ID of the plugin
	 *
	 * @var    string
	 * @since  7.15.0
	 */
	private $id = 'joomlacustomfields';

	/**
	 * JDatabase handler
	 *
	 * @var    JDatabase
	 * @since  7.15.0
	 */
	protected $db;

	/**
	 * Construct the class.
	 *
	 * @since   7.15.0
	 */
	public function __construct()
	{
		// Set the dependencies
		$this->db = JFactory::getDbo();
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string        $plugin    The ID of the plugin.
	 * @param   string        $fieldName The field name
	 * @param   string        $value     The value of field.
	 * @param   int           $itemId    The item ID.
	 * @param   CsviHelperLog $log       The CSVI logger.
	 *
	 * @return  mixed  Void if plugin needs to handle request | False if plugin does not handle request.
	 *
	 * @since   7.15.0
	 *
	 * @throws  RuntimeException
	 */
	public function importCustomfields($plugin, $fieldName, $value, $itemId, $log)
	{
		if ($plugin !== $this->id)
		{
			return false;
		}

		$fieldResult = $this->getFieldDetails($fieldName);
		$fieldId     = $fieldResult->id;
		$fieldType   = $fieldResult->type;
		$fieldParams = json_decode($fieldResult->fieldparams, true);

		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName('#__fields_values'))
			->where($this->db->quoteName('field_id') . ' = ' . (int) $fieldId)
			->where($this->db->quoteName('item_id') . ' = ' . (int) $itemId);
		$this->db->setQuery($query)->execute();
		$log->add('Removed existing custom field values');

		$importValues = array();

		switch (strtolower($fieldType))
		{
			case 'repeatable':
				// Value Name#name1|Age#34|Description#My first description~Name#name2|Age#23|Description#My second description
				$repeatableFieldNames = $fieldParams['fields'];

				foreach ($repeatableFieldNames as $repeatableFieldName)
				{
					$onlyFieldNames[] = $repeatableFieldName['fieldname'];
				}

				$importFieldValues = explode('~', $value);

				$fieldCounter = 0;

				foreach ($importFieldValues as $importFieldValue)
				{
					$seperatedFieldValues = explode('|', $importFieldValue);

					foreach ($seperatedFieldValues as $seperatedFieldValue)
					{
						$nextSeperatedFieldValues        = explode('#', $seperatedFieldValue);
						$seperatedFieldName              = $nextSeperatedFieldValues[0];
						$seperatedFieldValue             = $nextSeperatedFieldValues[1];
						$valueArray[$seperatedFieldName] = $seperatedFieldValue;
					}

					$finalArray[$fieldName . $fieldCounter] = $valueArray;
					$fieldCounter++;

				}

				$importValues[] = json_encode($finalArray);
				break;
			case 'acfurl':
			case 'acfsoundcloud':
			case 'acfpaypal':
			case 'acfosm':
				$fieldValues = explode('|', $value);

				foreach ($fieldValues as $fieldValue)
				{
					$eachFieldValues                 = explode('#', $fieldValue);
					$seperatedFieldName              = $eachFieldValues[0];
					$seperatedFieldValue             = $eachFieldValues[1];
					$valueArray[$seperatedFieldName] = $seperatedFieldValue;
				}

				$importValues[] = json_encode($valueArray);
				break;
			default:
				$multiple = false;

				if (isset($fieldParams['multiple']) && $fieldParams['multiple'])
				{
					$multiple = true;
				}

				// For multiple values value1#value2#value3
				if ((($multiple && strpos($value, '|') !== false)) || strpos($value, '|') !== false)
				{
					$importValues = explode('|', $value);
				}
				else
				{
					$importValues[] = $value;
				}

				break;
		}

		$query->clear()
			->insert($this->db->quoteName('#__fields_values'))
			->columns($this->db->quoteName(array('field_id', 'item_id', 'value')));

		foreach ($importValues as $fieldValues)
		{
			$query->values(
				(int) $fieldId . ',' .
				(int) $itemId . ',' .
				$this->db->quote($fieldValues)
			);
		}

		$this->db->setQuery($query)->execute();
		$log->add('Custom field values added');

		return true;
	}

	/**
	 * Export the values.
	 *
	 * @param   string        $plugin    The ID of the plugin.
	 * @param   string        $fieldName The field name
	 * @param   string        $value     The value of field.
	 * @param   int           $itemId    The item ID.
	 * @param   CsviHelperLog $log       The CSVI logger.
	 *
	 * @return  mixed  List of values to export if available else empty value.
	 *
	 * @since   7.15.0
	 */
	public function exportCustomfields($plugin, $fieldName, $value, $itemId, $log)
	{
		if ($plugin === $this->id)
		{
			$fieldResult = $this->getFieldDetails($fieldName);
			$fieldId     = $fieldResult->id;
			$fieldType   = $fieldResult->type;

			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('value'))
				->from($this->db->quoteName('#__fields_values'))
				->where($this->db->quoteName('field_id') . ' = ' . (int) $fieldId)
				->where($this->db->quoteName('item_id') . ' = ' . (int) $itemId);
			$this->db->setQuery($query);
			$fieldValue = $this->db->loadObjectList();

			if (!$fieldValue)
			{
				$log->add('No values found for the custom field ' . $fieldName);

				return '';
			}

			switch (strtolower($fieldType))
			{
				case 'repeatable':
					$storedValues = json_decode($fieldValue[0]->value, true);

					foreach ($storedValues as $storedValue)
					{
						$finalEachArray = array();

						foreach ($storedValue as $key => $customFieldValue)
						{
							$finalEachArray[] = $key . '#' . $customFieldValue;
						}

						$finalArray[] = implode('|', $finalEachArray);
					}

					$value = implode('~', $finalArray);

					break;
				case 'acfurl':
				case 'acfsoundcloud':
				case 'acfpaypal':
				case 'acfosm':
					$storedValues = json_decode($fieldValue[0]->value, true);

					foreach ($storedValues as $key => $customFieldValue)
					{
						$splitValues[] = $key . '#' . $customFieldValue;
					}

					$value = implode('|', $splitValues);

					break;
				default:
					$finalArray = array();

					foreach ($fieldValue as $fieldVal)
					{
						$finalArray[] = $fieldVal->value;
					}

					$value = implode('|', $finalArray);
					break;
			}

			return $value;

		}
	}

	/**
	 * Get custom field Details
	 *
	 * @param   string  $fieldName  The field name
	 *
	 * @return  array  List of field values
	 *
	 * @since   7.15.0
	 */
	private function getFieldDetails($fieldName)
	{
		$query = $this->db->getQuery(true)
			->select(
				$this->db->quoteName(
					[
						'id',
						'type',
						'fieldparams'
					]
				)
			)
			->from($this->db->quoteName('#__fields'))
			->where($this->db->quoteName('name') . '  = ' . $this->db->quote($fieldName));
		$this->db->setQuery($query);

		return $this->db->loadObject();
	}
}
