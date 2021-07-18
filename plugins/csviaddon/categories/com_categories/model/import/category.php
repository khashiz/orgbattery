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

namespace categories\com_categories\model\import;

use CsviHelperTranslit;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Categories import.
 *
 * @package     CSVI
 * @subpackage  JoomlaCategory
 * @since       6.0
 */
class Category extends \RantaiImportEngine
{
	/**
	 * Category table
	 *
	 * @var    \CategoriesTableCategory
	 * @since  6.0
	 */
	private $category;

	/**
	 * The addon helper
	 *
	 * @var    \Com_CategoriesHelperCom_Categories
	 * @since  6.0
	 */
	protected $helper;

	/**
	 * Category separator
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $categorySeparator;

	/**
	 * List of available custom fields
	 *
	 * @var    array
	 * @since  7.2.0
	 */
	private $customFields = '';

	/**
	 * Check if import file can be loaded
	 *
	 * @var    bool
	 * @since  7.15.0
	 */
	protected $loaded = false;

	/**
	 * Run this before we start.
	 *
	 * @return  void.
	 *
	 * @since   7.2.0
	 */
	public function onBeforeStart()
	{
		// Load the tables that will contain the data
		$this->loadCustomFields();

		// Load the dispatcher
		$this->dispatcher = new \RantaiPluginDispatcher;
		$this->dispatcher->importPlugins('csviext', $this->db);
	}

	/**
	 * Start the product import process.
	 *
	 * @return  bool  True on success | false on failure.
	 *
	 * @since   6.0
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 * @throws  \UnexpectedValueException
	 */
	public function getStart()
	{
		// Process data
		foreach ($this->fields->getData() as $fields)
		{
			foreach ($fields as $name => $details)
			{
				$value = $details->value;

				switch ($name)
				{
					case 'category_path':
						$this->setState('path', $value);
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		// There must be an alias and catid or category_path
		if ($this->getState('extension', false) && ($this->getState('id', false) || $this->getState('path', false)))
		{
			$this->loaded = true;

			if (!$this->getState('id', false))
			{
				$this->setState('id', $this->helper->getCategoryId($this->createPath($this->getState('path')), $this->getState('extension')));
			}

			// Load the current category data
			if ($this->category->load($this->getState('id', 0)) &&!$this->template->get('overwrite_existing_data'))
			{
				$this->log->add(Text::sprintf('COM_CSVI_DATA_EXISTS_PRODUCT_SKU', $this->getState('path', '')));
				$this->loaded = false;
			}
		}
		else
		{
			// We must have the required fields otherwise category cannot be created
			$this->loaded = false;

			$this->log->addStats('skipped', Text::_('COM_CSVI_MISSING_REQUIRED_FIELDS'));
		}

		return true;
	}

	/**
	 * Process a record.
	 *
	 * @return  bool  Returns true if all is OK | Returns false if no product SKU or product ID can be found.
	 *
	 * @since   6.0
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	public function getProcessRecord()
	{
		if ($this->loaded)
		{
			if (!$this->getState('id', false) && $this->template->get('ignore_non_exist'))
			{
				// Do nothing for new categories when user chooses to ignore new categories
				$this->log->addStats('skipped', Text::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->getState('path', '')));
			}
			else
			{
				$this->log->add('Process category path:' . $this->getState('path'), false);

				// Set some special fields
				$this->setParams();
				$this->setMetadata();

				// Load the category separator
				if (null === $this->categorySeparator)
				{
					$this->categorySeparator = $this->template->get('category_separator', '/');
				}

				if (!$this->getState('id', false))
				{
					$paths       = explode($this->categorySeparator, $this->getState('path'));
					$path        = '';
					$parent_id   = false;
					$pathkeys    = array_keys($paths);
					$lastkey     = array_pop($pathkeys);
					$processTags = false;

					foreach ($paths as $key => $category)
					{
						if ($key === $lastkey)
						{
							$path .= $this->categorySeparator . $this->getState('alias', $category);
						}
						elseif ($key > 0)
						{
							$path .= $this->categorySeparator . $category;
						}
						else
						{
							$path = $category;
						}

						// Check if the path exists
						$path_id = $this->helper->getCategoryId($this->createPath($path), $this->getState('extension'));

						// Category doesn't exist
						if (!$path_id)
						{
							// Clean the table
							$this->category->reset();

							// Bind the data
							$data              = [];
							$data['alias']     = '';
							$data['published'] = (!$this->getState('published', false)) ? 0 : $this->getState('published');
							$data['access']    = (!$this->getState('access', false)) ? 1 : $this->getState('access');
							$data['params']    = $this->getState('params', '{}');
							$data['metadata']  = $this->getState('metadata', '{}');
							$data['language']  = (!$this->getState('language', false)) ? '*' : $this->getState('language');
							$data['parent_id'] = 1;
							$data['path']      = $path;
							$data['title']     = $category;
							$data['metadesc']  = $this->getState('metadesc', false);
							$data['metakey']   = $this->getState('metakey', false);

							if ($parent_id)
							{
								$data['parent_id'] = $parent_id;
							}

							if ($lastkey === $key)
							{
								$data['title']       = (!$this->getState('title', false)) ? $category : $this->getState('title');
								$data['note']        = $this->getState('note');
								$data['description'] = $this->getState('description');
								$data['alias']       = $this->getState('alias', $category);
								$processTags         = true;
							}

							$data['extension'] = $this->getState('extension');

							// Set the category location
							$this->category->setLocation($data['parent_id'], 'last-child');

							// Bind the data
							$this->category->bind($data);

							try
							{
								if ($this->category->checkCategory($this->date))
								{
									$this->category->storeCategory($this->date, $this->userId);

									// Saving failed, do not continue with this path
									if (!$this->category->id)
									{
										return false;
									}

									$this->category->rebuildPath($this->category->id);
									$this->category->rebuild($this->category->id, $this->category->lft, $this->category->level, $this->category->path);
									$parent_id = $this->category->id;
									$this->log->add('Added category');
									$this->log->addStats('added', 'COM_CSVI_ADD_CATEGORY');
									$this->processCustomFields($this->category->id);

									if ($processTags)
									{
										$this->processTags($this->category->id);
									}

									if ($this->getState('associations', false))
									{
										$this->processAssociations($this->category->id);
									}
								}
							}
							catch (\Exception $e)
							{
								$this->log->add('Cannot add Joomla category. Error: ' . $e->getMessage(), false);
								$this->log->addStats('incorrect', $e->getMessage());

								return false;
							}
						}
						else
						{
							$parent_id = $path_id;
						}
					}
				}
				else
				{
					// Check if we use a given category id
					if ($this->template->get('keepcatid'))
					{
						$categoryId = $this->category->checkId($this->getState('id'), $this->userId);
						$this->setState('id', $categoryId);
					}

					// Category already exist, just update it, first load the existing values
					$this->category->load($this->getState('id'));

					// Remove the alias, so it can be created again
					$this->category->alias = null;

					// Take the last category as default for the alias
					$categoryNames = explode($this->categorySeparator, $this->getState('path'));
					$categoryName  = end($categoryNames);

					// Prepare the data
					$data                = array();
					$data['alias']       = $this->getState('alias', $categoryName);
					$data['published']   = $this->getState('published');
					$data['access']      = $this->getState('access');
					$data['params']      = $this->getState('params');
					$data['metadata']    = $this->getState('metadata');
					$data['language']    = $this->getState('language');
					$data['path']        = $this->getState('path');
					$data['title']       = $this->getState('title');
					$data['extension']   = $this->getState('extension');
					$data['note']        = $this->getState('note');
					$data['description'] = $this->getState('description');
					$data['metadesc']    = $this->getState('metadesc');
					$data['metakey']     = $this->getState('metakey');


					// Bind the data
					$this->category->bind($data);

					try
					{
						if ($this->category->checkCategory($this->date))
						{
							$this->category->storeCategory($this->date, $this->userId);
							$this->log->add('Updated category');
							$this->log->addStats('updated', 'COM_CSVI_UPDATE_CATEGORY');
							$this->processCustomFields($this->category->id);
							$this->processTags($this->category->id);

							if ($this->getState('associations', false))
							{
								$this->processAssociations($this->category->id);
							}
						}
					}
					catch (\Exception $exception)
					{
						$this->log->add('Cannot add Joomla category. Error: ' . $exception->getMessage(), false);
						$this->log->addStats('incorrect', $exception->getMessage());

						return false;
					}
				}
			}

			$this->setState('id', $this->category->id);

			return true;
		}

		return false;
	}

	/**
	 * Load the necessary tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function loadTables()
	{
		\JTable::addIncludePath(JPATH_PLUGINS . '/csviaddon/categories/com_categories/table');
		$this->category = \JTable::getInstance('Category', 'CategoriesTable');

		// Inject the template into the table, needed for transliteration
		$this->category->setTemplate($this->template);
		$this->category->setLogger($this->log);
	}

	/**
	 * Clear the loaded tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function clearTables()
	{
		$this->category->reset();
	}

	/**
	 * Rebuild the category left and right values after the import is complete.
	 *
	 * @return  void
	 *
	 * @since   7.2.0
	 */
	public function onAfterStart()
	{
		$this->category->rebuild(1);
	}

	/**
	 * Set the attributes field.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function setParams()
	{
		// Check for attributes
		if (!$this->getState('params', false))
		{
			$paramFields = array
			(
				'category_layout',
				'image',
				'image_alt'
			);

			// Get Value from content plugin
			$dispatcher = new \RantaiPluginDispatcher;
			$dispatcher->importPlugins('csviext', $this->db);

			// Fire the plugin to get attributes to import
			$pluginFields = $dispatcher->trigger(
				'getAttributes',
				array(
					'extension' => 'joomla',
					'operation' => 'category',
					'log'       => $this->log
				)
			);

			if (!empty($pluginFields[0]))
			{
				$this->log->add('Attributes added for content swmap plugin', false);
				$paramFields = array_merge($paramFields, $pluginFields[0]);
			}

			// Load the current attributes
			$params = json_decode($this->category->get('params'));

			if (!is_object($params))
			{
				$params = new \stdClass;
			}

			foreach ($paramFields as $field)
			{
				if ($this->getState($field, false))
				{
					$params->$field = $this->getState($field, '');
				}
			}

			// Store the new attributes
			$this->setState('params', json_encode($params));
		}
	}

	/**
	 * Set the meta data.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function setMetadata()
	{
		if (!$this->getState('metadata', false))
		{
			$metadataFields = array
			(
				'meta_author',
				'meta_robots',
			);

			// Load the current attributes
			$metadata = json_decode($this->category->get('metadata'));

			if (!is_object($metadata))
			{
				$metadata = new \stdClass;
			}

			foreach ($metadataFields as $field)
			{
				$newField = str_ireplace('meta_', '', $field);
				$metadata->$newField = $this->getState($field, '');
			}

			// Store the new attributes
			$this->setState('metadata', json_encode($metadata));
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
		$this->customFields = $this->db->loadObjectList();

		$this->log->add('Load the Joomla custom fields for categories');
	}

	/**
	 * Update custom fields data.
	 *
	 * @param   int  $id  Id of the article
	 *
	 * @return  bool Returns true if all is OK | Returns false otherwise
	 *
	 * @since   7.2.0
	 */
	private function processCustomFields($id)
	{
		$countCustomFields = $this->customFields ? count($this->customFields) : 0;

		if ($countCustomFields === 0)
		{
			$this->log->add('No custom fields found', false);

			return false;
		}

		foreach ($this->customFields as $field)
		{
			$fieldName = $field->name;

			if ($this->getState($fieldName, '') !== '')
			{
				// Fire the plugin to enter custom field values
				$this->dispatcher->trigger(
					'importCustomfields',
					array(
						'plugin'  => 'joomlacustomfields',
						'field'   => $field->name,
						'value'   => $this->getState($fieldName, ''),
						'item_id' => $id,
						'log'     => $this->log
					)
				);
			}
		}
	}

	/**
	 * Update Tags data
	 *
	 * @param   int  $id  Id of the category
	 *
	 * @return  bool Returns true if all is OK | Returns false otherwise
	 *
	 * @since   7.7.0
	 */
	private function processTags($id)
	{
		$tags = $this->getState('tags', false);

		if (!$tags)
		{
			return false;
		}

		$tagsArray = explode('|', $tags);

		$typeAlias = 'com_content.category';
		$query     = $this->db->getQuery(true)
			->select($this->db->quoteName('type_id'))
			->from($this->db->quoteName('#__content_types'))
			->where($this->db->quoteName('type_alias') . '  = ' . $this->db->quote($typeAlias));
		$this->db->setQuery($query);
		$typeId  = $this->db->loadResult();
		$tagDate = $this->date->toSql();

		foreach ($tagsArray as $tag)
		{
			$query->clear()
				->select($this->db->quoteName('id'))
				->from($this->db->quoteName('#__tags'))
				->where($this->db->quoteName('path') . '  = ' . $this->db->quote($tag));
			$this->db->setQuery($query);
			$tagId = $this->db->loadResult();
			$this->log->add('Get the tag id ');

			if (!$tagId)
			{
				$this->log->add('No tag id found for the tag ' . $tag, false);
				continue;
			}

			// Delete the values and do a fresh import to avoid dulicate error
			$query->clear()
				->delete($this->db->quoteName('#__contentitem_tag_map'))
				->where($this->db->quoteName('content_item_id') . ' = ' . (int) $id)
				->where($this->db->quoteName('tag_id') . ' = ' . (int) $tagId);
			$this->db->setQuery($query)->execute();
			$this->log->add('Removed existing tag for category before inserting');

			$query->clear()
				->insert($this->db->quoteName('#__contentitem_tag_map'))
				->columns($this->db->quoteName(array('type_alias', 'content_item_id', 'tag_id', 'tag_date', 'type_id')))
				->values($this->db->quote($typeAlias) . ', ' . (int) $id . ', ' .
					(int) $tagId . ', ' . $this->db->quote($tagDate) . ', ' . (int) $typeId);
			$this->db->setQuery($query)->execute();
			$this->log->add('Insert the new tag for category');
		}

		return true;
	}

	/**
	 * Create a category path.
	 *
	 * @param   string  $categoryPath  The category path to create the path for
	 *
	 * @return  string  The created category path.
	 *
	 * @since   7.10.0
	 */
	public function createPath($categoryPath)
	{
		$categoryArray = array();
		$translit      = new CsviHelperTranslit($this->template);
		$paths         = explode($this->template->get('category_separator', '/'), $categoryPath);

		foreach ($paths as $categoryPath)
		{
			$categoryArray[] = $translit->stringURLSafe($categoryPath);
		}

		return implode($this->template->get('category_separator', '/'), $categoryArray);
	}

	/**
	 * Process associated categories
	 *
	 * @param   int $categoriesId Id of the Categories
	 *
	 * @return  bool True if all ok False otherwise.
	 *
	 * @since   7.15.0
	 */
	private function processAssociations($categoriesId)
	{
		if ($this->getState('language', false) === '*')
		{
			$this->log->add('Association cannot be added if language is set to all', false);
			$this->log->addStats('incorrect', 'COM_CSVI_LANGUAGE_SET_TO_ALL');

			return false;
		}

		$associations                                     = array();
		$associatedCategories                             = explode('|', $this->getState('associations', false));
		$associationsContext                              = 'com_categories.item';
		$associations[$this->getState('language', false)] = $categoriesId;

		foreach ($associatedCategories as $category)
		{
			$associatedDetails = explode('#', $category);

			if (strpos($associatedDetails[0], '-') === false)
			{
				$this->log->add('Not a valid language ' . $associatedDetails[0], false);
				$this->log->addStats('incorrect', Text::sprintf('COM_CSVI_LANGUAGE_NOT_VALID', $associatedDetails[0]));
				continue;
			}

			$languageTags         = explode('-', $associatedDetails[0]);
			$languageTags[1]      = strtoupper($languageTags[1]);
			$language             = implode('-', $languageTags);
			$categoryPath         = $associatedDetails[1];
			$associatedCategoryId = $this->helper->getCategoryId($categoryPath, $this->getState('extension', 'com_content'));

			if ($associatedCategoryId)
			{
				$associations[$language] = (int) $associatedCategoryId;
			}
			else
			{
				$this->log->add('No associated category id found with path  ' . $categoryPath, false);
				$this->log->addStats('incorrect', Text::sprintf('COM_CSVI_NO_CATEGORY_FOUND', $categoryPath));
			}
		}

		// Make sure there are no duplicates
		$associations = array_unique($associations);

		if ((count($associations)) > 1)
		{
			$key   = md5(json_encode($associations));
			$query = $this->db->getQuery(true)
				->insert($this->db->quoteName('#__associations'));

			foreach ($associations as $arrayVal => $id)
			{
				$this->deleteAssociation($id, $associationsContext);
				$query->values(((int) $id) . ',' . $this->db->quote($associationsContext) . ',' . $this->db->quote($key));
			}

			$this->db->setQuery($query)->execute();
			$this->log->add('Associations added for category');
		}

		return true;
	}

	/**
	 * Delete a association
	 *
	 * @param   int $id      Id of the category
	 * @param   int $context Context of com_categories
	 *
	 * @return  boolean True if deleted | False if id is missing.
	 *
	 * @since   7.15.0
	 */
	private function deleteAssociation($id, $context)
	{
		if (!$id)
		{
			$this->log->add('Association id not found to delete', false);

			return false;
		}

		$query = $this->db->getquery(true)
			->delete($this->db->quotename('#__associations'))
			->where($this->db->quotename('id') . ' = ' . (int) $id)
			->where($this->db->quotename('context') . ' = ' . $this->db->quote($context));
		$this->db->setquery($query)->execute();
		$this->log->add('Association category deleted');

		return true;
	}

	/**
	 * Set loaded true so to run import
	 *
	 * @since   7.15.0
	 */
	public function setLoaded()
	{
		$this->loaded = true;
	}
}
