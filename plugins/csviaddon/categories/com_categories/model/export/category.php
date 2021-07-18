<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaCategory
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace categories\com_categories\model\export;

defined('_JEXEC') or die;

// Load needed class for associations export
\JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');

use Joomla\CMS\Factory;

/**
 * Export Joomla Categories.
 *
 * @package     CSVI
 * @subpackage  JoomlaCategory
 * @since       6.0
 */
class Category extends \CsviModelExports
{
	/**
	 * The custom fields that from other extensions.
	 *
	 * @var    array
	 * @since  6.5.0
	 */
	private $pluginfieldsExport = array();

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
			// Get some basic data
			$this->loadPluginFields();
			$this->loadCustomFields();

			// Load the dispatcher
			$dispatcher = new \RantaiPluginDispatcher;
			$dispatcher->importPlugins('csviext', $this->db);

			// Build something fancy to only get the fieldnames the user wants
			$userfields    = [];
			$exportfields  = $this->fields->getFields();
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

			$userfields[] = $this->db->quoteName('c.id');

			foreach ($exportfields as $field)
			{
				$sortDirection = ($sortByFields[$field->field_name]) ?? 'ASC';

				switch ($field->field_name)
				{
					case 'category_path':
						$userfields[] = $this->db->quoteName('c.path');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.path');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.path') . ' ' . $sortDirection;
						}

						break;
					case 'meta_author':
					case 'meta_robots':
						$userfields[] = $this->db->quoteName('c.metadata');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.metadata');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.metadata') . ' ' . $sortDirection;
						}

						break;
					case 'category_layout':
					case 'image':
					case 'image_alt':
						$userfields[] = $this->db->quoteName('c.params');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.params');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.params') . ' ' . $sortDirection;
						}

						break;
					case 'tags':
						$userfields[] = $this->db->quoteName('c.id');
						break;
					case 'associations':
						$userfields[] = $this->db->quoteName('c.language');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.language');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.language') . ' ' . $sortDirection;
						}
						break;
					case 'custom':
						break;
					default:
						// Do not include custom fields into the query
						if (!in_array($field->field_name, $this->pluginfieldsExport)
							&& !in_array($field->field_name, $this->customFields))
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
			$query->from($this->db->quoteName('#__categories', 'c'));

			// Make sure the ID is always greater than 0 as we don't want to export the root
			$query->where('asset_id > 0');

			// Filter by published state
			$publish_state = $this->template->get('publish_state');

			if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0))
			{
				$query->where($this->db->quoteName('c.published') . ' = ' . (int) $publish_state);
			}

			// Filter by extension
			$extensions = $this->template->get('extension', array(), 'array');

			if ($extensions)
			{
				// Remove all if user has it in options
				if (($key = array_search('all', $extensions)) !== false)
				{
					unset($extensions[$key]);
				}

				if (count($extensions) > 0)
				{
					$query->where($this->db->quoteName('c.extension') . " IN ('" . implode("','", $extensions) . "')");
				}
			}

			// Filter by parent category
			$parentCategories = $this->template->get('parentcategories', array(), 'array');

			if ($parentCategories)
			{
				if (count($parentCategories) > 0)
				{
					$totalCategories = array();

					foreach ($parentCategories as $parentCategory)
					{
						$subCategoryIds  = $this->getSubCategoryIds($parentCategory);
						$totalCategories = array_merge($parentCategories, $totalCategories, $subCategoryIds);
					}

					$query->where($this->db->quoteName('c.id') . " IN ('" . implode("','", array_unique($totalCategories)) . "')");
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
							case 'category_path':
								$fieldvalue = $record->path;
								break;
							case 'meta_author':
							case 'meta_robots':
								$metadata = json_decode($record->metadata);

								if (isset($metadata->$fieldname))
								{
									$fieldvalue = $metadata->$fieldname;
								}
								break;
							case 'category_layout':
							case 'image':
							case 'image_alt':
								$params = json_decode($record->params);

								if (isset($params->$fieldname))
								{
									$fieldvalue = $params->$fieldname;
								}
								break;
							case 'tags':
								$query->clear()
									->select($this->db->quoteName('tag_id'))
									->from($this->db->quoteName('#__contentitem_tag_map'))
									->where($this->db->quoteName('content_item_id') . ' = ' . (int) $record->id)
									->where($this->db->quoteName('type_alias') . ' = ' . $this->db->quote('com_content.category'));
								$this->db->setQuery($query);
								$tagIds = $this->db->loadObjectList();

								$tags = array();

								if ($tagIds)
								{
									foreach ($tagIds as $tagId)
									{
										$query->clear()
											->select($this->db->quoteName('path'))
											->from($this->db->quoteName('#__tags'))
											->where($this->db->quoteName('id') . ' = ' . (int) $tagId->tag_id);
										$this->db->setQuery($query);
										$tags[] = $this->db->loadResult();
									}
								}

								$fieldvalue = implode('|', $tags);
								break;
							case 'associations':
								$savedLanguage     = $record->language;
								$associationsArray = [];

								if ($savedLanguage !== '*' && $savedLanguage === Factory::getLanguage()->getTag())
								{
									$associations = \CategoryHelperAssociation::getCategoryAssociations($record->id, 'com_content');

									foreach ($associations as $tag => $item)
									{
										if ($tag !== Factory::getLanguage()->getTag())
										{
											$id                  = explode(':', $item);
											$alias               = $id[1];
											$language            = $tag;
											$associationsArray[] = $language . '#' . $alias;
										}
									}

									$fieldvalue = implode('|', $associationsArray);
								}
								else
								{
									$this->log->add('Association cannot be exported as language is set to all', false);
								}
								break;
							default:
								if (in_array($fieldname, $this->pluginfieldsExport))
								{
									$fieldvalue = '';

									// Get value from content plugin
									$result = $dispatcher->trigger(
										'onExportContent',
										array(
											'extension' => 'joomla',
											'operation' => 'category',
											'id' => $record->id,
											'fieldname' => $fieldname,
											'log' => $this->log
										)
									);

									if (isset($result[0]))
									{
										$fieldvalue = $result[0];
									}
								}

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
				$this->addExportContent('COM_CSVI_NO_DATA_FOUND');

				// Output the contents
				$this->writeOutput();
			}
		}
	}

	/**
	 * Get a list of plugin fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	private function loadPluginFields()
	{
		$dispatcher = new \RantaiPluginDispatcher;
		$dispatcher->importPlugins('csviext', $this->db);
		$result = $dispatcher->trigger(
			'getAttributes',
			array(
				'extension' => 'joomla',
				'operation' => 'category',
				'log' => $this->log
			)
		);

		if (is_array($result) && !empty($result))
		{
			$this->pluginfieldsExport = array_merge($this->pluginfieldsExport, $result[0]);
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
			->where($this->db->quoteName('context') . ' = ' . $this->db->quote('com_contact.categories') . ' OR ' .
				$this->db->quoteName('context') . ' = ' . $this->db->quote('com_content.categories'));
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach ($results as $result)
		{
			$this->customFields[] = $result->name;
		}

		$this->log->add('Load the Joomla custom fields for categories');
	}

	/**
	 * Get the children categories from parent category id
	 *
	 * @param   integer  $categoryId  The id of the parent category
	 *
	 * @return  array  Array of category ids
	 *
	 * @since   7.15.0
	 */
	private function getSubCategoryIds($categoryId)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__categories'))
			->where($this->db->quoteName('parent_id') . ' = ' . (int) $categoryId);
		$this->db->setQuery($query);
		$subCatIds = $this->db->loadColumn();

		if ($subCatIds)
		{
			foreach ((array) $subCatIds as $subCatId)
			{
				$newCatIds = $this->getSubCategoryIds($subCatId);

				$subCatIds = array_merge((array) $subCatIds, $newCatIds);
			}
		}

		return $subCatIds;
	}
}
