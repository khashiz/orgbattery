<?php
/**
 * @package     CSVI
 * @subpackage  CSVI
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace csvi\com_csvi\model\export;

defined('_JEXEC') or die;

/**
 * Export custom tables.
 *
 * @package     CSVI
 * @subpackage  CSVI
 * @since       6.0
 */
class Custom extends \CsviModelExports
{
	/**
	 * Export the data.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 *
	 * @throws  \CsviException
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Build something fancy to only get the fieldnames the user wants
			$userfields   = array();
			$exportfields = $this->fields->getFields();
			$customTables = $this->template->get('custom_table');

			if (!isset($customTables->custom_table0->table))
			{
				// There is no from table, we can't continue
				throw new \CsviException(\JText::_('COM_CSVI_CUSTOM_EXPORT_NO_TABLENAME_SET'));
			}

			// Get the primary table
			$primaryTable      = $customTables->custom_table0->table;
			$primaryTableAlias = $customTables->custom_table0->tablealias;

			// Group By and Sort selected fields
			$groupbyfields = json_decode( $this->template->get( 'groupbyfields', '[]', 'string' ) );
			$sortfields    = json_decode( $this->template->get( 'sortfields', '[]', 'string' ) );
			$groupby       = [];
			$sortby        = [];

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					// Man made fields, do not export them
					case 'custom':
						break;
					default:
						$tableName    = $field->table_name;
						$tableAlias   = $field->table_alias ?? '#__' . $tableName;
						$columnHeader = $field->column_header ?: $field->field_name;

						$userfields[] = $this->db->quoteName($tableAlias . '.' . $field->field_name, $columnHeader);

						$groupTable = false;
						$groupKey   = false;

						// Determine if the field must be grouped by
						if (isset($groupbyfields->groupby_table_name))
						{
							$groupTable = array_search($tableName, $groupbyfields->groupby_table_name, true);
						}

						if (isset($groupbyfields->groupby_field_name))
						{
							$groupKey = array_search($field->field_name, $groupbyfields->groupby_field_name, true);
						}

						if ($groupTable !== false && $groupKey !== false && $groupTable === $groupKey)
						{
							$groupby[] = $this->db->quoteName($tableAlias . '.' . $field->field_name);
						}

						$sortTable = false;
						$sortKey   = false;

						// Determine if the field must be sorted by
						if (isset($sortfields->sortby_table_name))
						{
							$sortTable = array_search($tableName, $sortfields->sortby_table_name, true);
						}

						if (isset($sortfields->sortby_field_name))
						{
							$sortKey = array_search($field->field_name, $sortfields->sortby_field_name, true);
						}

						if ($sortTable !== false && $sortKey !== false && $sortTable === $sortKey)
						{
							$sortby[] = $this->db->quoteName($tableAlias . '.' . $field->field_name) . ' ' . $sortfields->sortby_field_name_order[$sortKey];
						}
						break;
				}
			}

			$userfields = array_unique($userfields);
			$query      = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));

			if ($primaryTableAlias)
			{
				$query->from($this->db->quoteName("#__" . $primaryTable, $primaryTableAlias));
			}
			else
			{
				$query->from($this->db->quoteName("#__" . $primaryTable));
			}

			for ($i = 1, $iMax = count( (array) $customTables ); $i <= $iMax; $i++)
			{
				$keyName = 'custom_table' . $i;

				if (isset($customTables->$keyName->table))
				{
					$table          = '#__' . $customTables->$keyName->table;
					$tableAlias     = $customTables->$keyName->tablealias ?? '';
					$tableField     = $customTables->$keyName->field;
					$joinTable      = '#__' . $customTables->$keyName->jointable;
					$joinTableAlias = $customTables->$keyName->jointablealias ?? '';
					$joinTableField = $customTables->$keyName->joinfield;

					$mainTable          = ($tableAlias)
						? $this->db->quoteName($table, $tableAlias)
						: $this->db->quoteName($table);

					$joinMainTableField = ($tableAlias)
						? $this->db->quoteName($tableAlias . '.' . $tableField)
						: $this->db->quoteName($table . '.' . $tableField);

					$joinTableField     = ($joinTableAlias)
						? $this->db->quoteName($joinTableAlias . '.' . $joinTableField)
						: $this->db->quoteName($joinTable . '.' . $joinTableField);

					$joinType = $customTables->$keyName->jointype;
					$query->join(
						$joinType,
						$mainTable
						. ' ON ' . $joinMainTableField .
						' = ' . $joinTableField
					);
				}
			}

			// Group the fields
			$groupby = array_unique($groupby);

			if (!empty($groupby))
			{
				$query->group($groupby);
			}

			// Sort set fields
			$sortby = array_unique($sortby);

			if (!empty($sortby))
			{
				$query->order($sortby);
			}

			// Add export limits
			$limits = $this->getExportLimit();

			try
			{
				// Execute the query
				$this->db->setQuery($query, $limits['offset'], $limits['limit']);
				$this->log->add('Export query' . $query->__toString(), false);
				$records = $this->db->getIterator();

				// Check if there are any records
				$logcount = $this->db->getNumRows();

				if ($logcount > 0)
				{
					foreach ($records as $record)
					{
						$this->log->incrementLinenumber();

						foreach ($exportfields as $field)
						{
							$fieldname    = $field->field_name;
							$columnHeader = $field->column_header;
							$fieldvalue   = '';

							// Set the field value
							if (isset($record->$fieldname))
							{
								$fieldvalue = $record->$fieldname;
							}

							// Column header is the field alias, use that for retrieve field value if set
							// This is useful in case of exporting multiple tables with same field name
							if ($field->column_header)
							{
								$fieldvalue = $record->$columnHeader;
							}

							// Store the field value
							$this->fields->set($field->csvi_templatefield_id, $fieldvalue);
						}

						// Output the data
						$this->addExportFields();

						// Output the contents
						$this->writeOutput();
					}
				}
				else
				{
					$this->addExportContent(\JText::_('COM_CSVI_NO_DATA_FOUND'));

					// Output the contents
					$this->writeOutput();
				}
			}
			catch (\Exception $exception)
			{
				$this->log->add('Error: ' . $exception->getMessage(), false);
				$this->log->addStats('incorrect', $exception->getMessage());
			}
		}
	}
}
