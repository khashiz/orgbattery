<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaUsers
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace users\com_users\model\export;

defined('_JEXEC') or die;

/**
 * Export Joomla Users.
 *
 * @package     CSVI
 * @subpackage  JoomlaUsers
 * @since       6.0
 */
class User extends \CsviModelExports
{

	/**
	 * List of available custom fields
	 *
	 * @var    array
	 * @since  7.2.0
	 */
	private $customFields = array();

	/**
	 * Export the data.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			$this->loadCustomFields();

			// Load the dispatcher
			$dispatcher = new \RantaiPluginDispatcher;
			$dispatcher->importPlugins('csviext', $this->db);

			// Group by fields
			$groupFields   = json_decode($this->template->get('groupbyfields', '', 'string'), false);
			$groupBy       = [];
			$groupByFields = [];

			if (isset($groupFields->name))
			{
				$groupByFields = array_flip($groupFields->name);
			}

			// Sort selected fields
			$sortFields   = json_decode($this->template->get('sortfields', '{}', 'string'), false);
			$sortBy       = [];
			$sortByFields = [];

			if (isset($sortFields->name, $sortFields->sortby))
			{
				foreach ($sortFields->sortby as $key => $sortName)
				{
					$sortByFields[$sortFields->name[$key]] = $sortName;
				}
			}

			// Build something fancy to only get the fieldnames the user wants
			$userfields = [];
			$exportfields = $this->fields->getFields();
			$userfields[] = $this->db->quoteName('u.id');

			foreach ($exportfields as $field)
			{
				$sortDirection = ($sortByFields[$field->field_name]) ?? 'ASC';

				switch ($field->field_name)
				{
					case 'fullname':
						$userfields[] = $this->db->quoteName('u.name', 'fullname');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('u.name');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('u.name') . ' ' . $sortDirection;
						}
						break;
					case 'usergroup_name':
					case 'usergroup_path':
						$userfields[] = $this->db->quoteName('id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('id') . ' ' . $sortDirection;
						}
						break;
					case 'custom':
						break;
					default:
						if (!in_array($field->field_name, $this->customFields))
						{
							$userfields[] = $this->db->quoteName($field->field_name);

							if (array_key_exists($field->field_name, $groupByFields))
							{
								$groupBy[] = $this->db->quoteName($field->field_name);
							}

							if (array_key_exists($field->field_name, $sortByFields))
							{
								$sortBy[] = $this->db->quoteName($field->field_name) . ' ' . $sortDirection;
							}
						}

						break;
				}
			}

			// Build the query
			$userfields = array_unique($userfields);
			$query = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__users', 'u'));

			// Filter by published state
			$user_state = $this->template->get('user_state');

			if ($user_state != '*')
			{
				$query->where($this->db->quoteName('u.block') . ' = ' . (int) $user_state);
			}

			// Filter by active state
			$user_active = $this->template->get('user_active');

			if ($user_active == '0')
			{
				$query->where($this->db->quoteName('u.activation') . ' = ' . $this->db->quote(''));
			}
			elseif ($user_active == '1')
			{
				$query->where($this->db->quoteName('u.activation') . ' = ' . $this->db->quote('32'));
			}

			// Filter by user group
			$user_groups = $this->template->get('user_group');

			if ($user_groups && $user_groups[0] != '*')
			{
				$query->leftJoin(
						$this->db->quoteName('#__user_usergroup_map', 'map2')
						. ' ON ' . $this->db->quoteName('map2.user_id') . ' = ' . $this->db->quoteName('u.id')
				);

				if (isset($user_groups))
				{
					$query->where($this->db->quoteName('map2.group_id') . ' IN (' . implode(',', $user_groups) . ')');
				}
			}

			// Filter on range
			$user_range = $this->template->get('user_range', 'user');

			if ($user_range != '*')
			{
				jimport('joomla.utilities.date');

				// Get UTC for now.
				$dNow = new \JDate;
				$dStart = clone $dNow;

				switch ($user_range)
				{
					case 'past_week':
						$dStart->modify('-7 day');
						break;

					case 'past_1month':
						$dStart->modify('-1 month');
						break;

					case 'past_3month':
						$dStart->modify('-3 month');
						break;

					case 'past_6month':
						$dStart->modify('-6 month');
						break;

					case 'post_year':
					case 'past_year':
						$dStart->modify('-1 year');
						break;

					case 'today':
						// Ranges that need to align with local 'days' need special treatment.
						$app	= \JFactory::getApplication();
						$offset	= $app->get('offset');

						// Reset the start time to be the beginning of today, local time.
						$dStart	= new \JDate('now', $offset);
						$dStart->setTime(0, 0, 0);

						// Now change the timezone back to UTC.
						$tz = new \DateTimeZone('GMT');
						$dStart->setTimezone($tz);
						break;
				}

				if ($user_range == 'post_year')
				{
					$query->where($this->db->quoteName('u.registerDate') . ' < ' . $this->db->quote($dStart->format('Y-m-d H:i:s')));
				}
				else
				{
					$query->where(
						$this->db->quoteName('u.registerDate') . ' >= ' . $this->db->quote($dStart->format('Y-m-d H:i:s'))
						. ' AND u.registerDate <=' . $this->db->quote($dNow->format('Y-m-d H:i:s'))
					);
				}
			}

			// Group the fields
			$groupBy = array_unique($groupBy);

			if (!empty($groupBy))
			{
				$query->group($groupBy);
			}

			// Sort set fields
			$sortBy = array_unique($sortBy);

			if (!empty($sortBy))
			{
				$query->order($sortBy);
			}

			// Add a limit if user wants us to
			$limits = $this->getExportLimit();

			// Execute the query
			$this->db->setQuery($query, $limits['offset'], $limits['limit']);
			$records = $this->db->getIterator();
			$this->log->add('Export query' . $query->__toString(), false);

			// Check if there are any records
			$logcount = $this->db->getNumRows();

			if ($logcount > 0)
			{
				foreach ($records as $record)
				{
					$this->log->incrementLinenumber();

					foreach ($exportfields as $field)
					{
						$fieldname = $field->field_name;

						// Set the field value
						if (isset($record->$fieldname))
						{
							$fieldvalue = $record->$fieldname;
						}
						else
						{
							$fieldvalue = '';
						}

						// Process the field
						switch ($fieldname)
						{
							case 'registerDate':
							case 'lastvisitDate':
							case 'lastResetTime':
								$fieldvalue = $this->fields->getDateFormat($fieldname, $record->$fieldname, $field->column_header);
								break;
							case 'usergroup_name':
								$query = $this->db->getQuery(true);
								$query->select($this->db->quoteName('title'));
								$query->from($this->db->quoteName('#__usergroups'));
								$query->leftJoin(
									$this->db->quoteName('#__user_usergroup_map')
									. ' ON ' . $this->db->quoteName('#__user_usergroup_map.group_id') . ' = ' . $this->db->quoteName('#__usergroups.id')
								);
								$query->where($this->db->quoteName('user_id') . ' = ' . $record->id);
								$this->db->setQuery($query);
								$groups = $this->db->loadColumn();

								$fieldvalue = '';

								if (is_array($groups))
								{
									$fieldvalue = implode('|', $groups);
								}

								break;
							case 'usergroup_path':
								$query = $this->db->getQuery(true);

								$query->select($this->db->quoteName('group_id'))
									->from($this->db->quoteName('#__user_usergroup_map'))
									->where($this->db->quoteName('user_id') . ' = ' . (int) $record->id);
								$this->db->setQuery($query);
								$userGroupIds = $this->db->loadColumn();

								$groupPaths = $this->helper->getGroupPath($userGroupIds, true);

								if (is_array($groupPaths))
								{
									$fieldvalue = implode('|', $groupPaths);
								}

								break;
							default:
								if (in_array($fieldname, $this->customFields))
								{
									$result = $dispatcher->trigger(
										'exportCustomfields',
										array(
											'plugin'  => 'joomlacustomfields',
											'field'   => $fieldname,
											'value'   => $fieldvalue,
											'item_id' => $record->id,
											'log'     => $this->log
										)
									);

									if (is_array($result) && (0 !== count($result)))
									{
										$fieldvalue = $result[0];
									}

									if ($fieldvalue && $this->fields->checkCustomFieldType($fieldname, 'calendar'))
									{
										$fieldvalue = $this->fields->getDateFormat($fieldname, $fieldvalue, $field->column_header);
									}
								}

								break;
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
	}

	/**
	 * Get a list of custom fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   7.2.0
	 *
	 * @throws  \Exception
	 */
	private function loadCustomFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('name'))
			->from($this->db->quoteName('#__fields'))
			->where($this->db->quoteName('state') . ' = 1')
			->where($this->db->quoteName('context') . ' = ' . $this->db->quote('com_users.user'));
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach ($results as $result)
		{
			$this->customFields[] = $result->name;
		}

		$this->log->add('Load the Joomla custom fields for articles');
	}
}
