<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaContent
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace content\com_content\model\export;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;

/**
 * Export Joomla articles.
 *
 * @package     CSVI
 * @subpackage  JoomlaContent
 * @since       6.0
 */
class Content extends \CsviModelExports
{
	/**
	 * The Joomla content helper
	 *
	 * @var    \Com_ContentHelperCom_Content
	 * @since  6.0
	 */
	protected $helper;

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
	 * @throws  \Exception
	 *
	 * @since   6.0
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Get some basic data
			require_once JPATH_SITE . '/components/com_content/helpers/route.php';
			$this->loadPluginFields();
			$this->loadCustomFields();

			// Load the dispatcher
			$dispatcher = new \RantaiPluginDispatcher;
			$dispatcher->importPlugins('csviext', $this->db);

			// Build something fancy to only get the fieldnames the user wants
			$userfields = [];
			$exportfields = $this->fields->getFields();

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

			$userfields[] = $this->db->quoteName('c.id');

			foreach ($exportfields as $field)
			{
				$sortDirection = ($sortByFields[$field->field_name]) ?? 'ASC';

				switch ($field->field_name)
				{
					case 'category_path':
						$userfields[] = $this->db->quoteName('c.catid');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.catid');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.catid') . ' ' . $sortDirection;
						}
						break;
					case 'article_url':
						$userfields[] = $this->db->quoteName('c.id');
						$userfields[] = $this->db->quoteName('cat.id', 'catid');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.id');
							$groupBy[] = $this->db->quoteName('cat.id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.id') . ' ' . $sortDirection;
							$sortBy[] = $this->db->quoteName('cat.id') . ' ' . $sortDirection;
						}
						break;
					case 'access':
					case 'alias':
					case 'asset_id':
					case 'checked_out':
					case 'checked_out_time':
					case 'created_by':
					case 'created_by_alias':
					case 'hits':
					case 'id':
					case 'language':
					case 'metadata':
					case 'metadesc':
					case 'metakey':
					case 'title':
					case 'version':
						$userfields[] = $this->db->quoteName('c.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.' . $field->field_name) . ' ' . $sortDirection;
						}
					break;
					case 'show_title':
					case 'link_titles':
					case 'show_intro':
					case 'show_category':
					case 'link_category':
					case 'show_parent_category':
					case 'link_parent_category':
					case 'show_author':
					case 'link_author':
					case 'show_create_date':
					case 'show_modify_date':
					case 'show_publish_date':
					case 'show_item_navigation':
					case 'show_icons':
					case 'show_print_icon':
					case 'show_email_icon':
					case 'show_vote':
					case 'show_hits':
					case 'show_noauth':
					case 'urls_position':
					case 'alternative_readmore':
					case 'article_layout':
					case 'show_publishing_options':
					case 'show_article_options':
					case 'show_urls_images_backend':
					case 'show_urls_images_frontend':
					case 'article_page_title':
					case 'show_tags':
					case 'info_block_position':
					case 'info_block_show_title':
					case 'show_associations':
						$userfields[] = $this->db->quoteName('c.id');
						$userfields[] = $this->db->quoteName('c.attribs');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.attribs');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.attribs') . ' ' . $sortDirection;
						}
					break;
					case 'image_intro':
					case 'float_intro':
					case 'image_intro_alt':
					case 'image_intro_caption':
					case 'image_fulltext':
					case 'float_fulltext':
					case 'image_fulltext_alt':
					case 'image_fulltext_caption':
						$userfields[] = $this->db->quoteName('c.images');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.images');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.images') . ' ' . $sortDirection;
						}
					break;
					case 'urla':
					case 'urlatext':
					case 'targeta':
					case 'urlb':
					case 'urlbtext':
					case 'targetb':
					case 'urlc':
					case 'urlctext':
					case 'targetc':
						$userfields[] = $this->db->quoteName('c.urls');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.urls');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.urls') . ' ' . $sortDirection;
						}
						break;
					case 'tags':
						$userfields[] = $this->db->quoteName('c.id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.id') . ' ' . $sortDirection;
						}
						break;
					case 'note':
						$userfields[] = $this->db->quoteName('c.note');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('c.note');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('c.note') . ' ' . $sortDirection;
						}
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
			$query->from($this->db->quoteName('#__content', 'c'));
			$query->leftJoin($this->db->quoteName('#__categories', 'cat') . ' ON ' . $this->db->quoteName('cat.id') . ' = ' . $this->db->quoteName('c.catid'));

			// Filter by published state
			$publish_state = $this->template->get('publish_state');

			if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0))
			{
				$query->where($this->db->quoteName('c.state') . ' = ' . (int) $publish_state);
			}

			// Filter by language
			$language = $this->template->get('content_language');

			if ($language != '*')
			{
				$query->where($this->db->quoteName('c.language') . ' = ' . $this->db->quote($language));
			}

			// Filter by category
			$categories = $this->template->get('content_categories');

			if ($categories && $categories[0] != '*')
			{
				if ($this->template->get('incl_subcategory', false))
				{
					$subCategories = array();

					foreach ($categories as $categoryId)
					{
						$subCategories = $this->helper->getSubCategoryIds($categoryId);
					}

					if ($subCategories)
					{
						$categories = array_merge($subCategories, $categories);
					}
				}

				$query->where($this->db->quoteName('catid') . " IN ('" . implode("','", $categories) . "')");
			}

			$daterange      = $this->template->get('contentdaterange', '');
			$checkDatefield = $this->template->get('filterdatefield', 'created');

			if ($daterange)
			{
				$jdate       = \JFactory::getDate('now', 'UTC');
				$currentDate = $this->db->quote($jdate->format('Y-m-d'));

				switch ($daterange)
				{
					case 'lastrun':
						if (substr($this->template->getLastrun(), 0, 4) != '0000')
						{
							$query->where($this->db->quoteName('c.' . $checkDatefield) . ' > ' . $this->db->quote($this->template->getLastrun()));
						}
						break;
					case 'yesterday':
						$query->where(
							'DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') = DATE_SUB(' . $currentDate . ', INTERVAL 1 DAY)');
						break;
					case 'thisweek':
						// Get the current day of the week
						$dayofweek = $jdate->__get('dayofweek');
						$offset    = $dayofweek - 1;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= ' . $currentDate);
						break;
					case 'lastweek':
						// Get the current day of the week
						$dayofweek = $jdate->__get('dayofweek');
						$offset    = $dayofweek + 6;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofweek . ' DAY)');
						break;
					case 'thismonth':
						// Get the current day of the week
						$dayofmonth = $jdate->__get('day');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofmonth . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= ' . $currentDate);
						break;
					case 'lastmonth':
						// Get the current day of the week
						$dayofmonth = $jdate->__get('day');
						$month      = date('n');
						$year       = date('y');

						if ($month > 1)
						{
							$month--;
						}
						else
						{
							$month = 12;
							$year--;
						}

						$daysinmonth = date('t', mktime(0, 0, 0, $month, 25, $year));
						$offset      = ($daysinmonth + $dayofmonth) - 1;

						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofmonth . ' DAY)');
						break;
					case 'thisquarter':
						// Find out which quarter we are in
						$month   = $jdate->__get('month');
						$year    = date('Y');
						$quarter = ceil($month / 3);

						switch ($quarter)
						{
							case '1':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-04-01'));
								break;
							case '2':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-04-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-07-01'));
								break;
							case '3':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-07-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-10-01'));
								break;
							case '4':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-10-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year++ . '-01-01'));
								break;
						}
						break;
					case 'lastquarter':
						// Find out which quarter we are in
						$month   = $jdate->__get('month');
						$year    = date('Y');
						$quarter = ceil($month / 3);

						if ($quarter == 1)
						{
							$quarter = 4;
							$year--;
						}
						else
						{
							$quarter--;
						}

						switch ($quarter)
						{
							case '1':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-04-01'));
								break;
							case '2':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-04-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-07-01'));
								break;
							case '3':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-07-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-10-01'));
								break;
							case '4':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-10-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year++ . '-01-01'));
								break;
						}
						break;
					case 'thisyear':
						$year = date('Y');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
						$year++;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-01-01'));
						break;
					case 'lastyear':
						$year = date('Y');
						$year--;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
						$year++;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-01-01'));
						break;
				}
			}
			else
			{
				$fromDate = $this->template->get('fromdate', false);

				if ($fromDate)
				{
					$fdate = \JFactory::getDate($fromDate);
					$query->where($this->db->quoteName('c.' . $checkDatefield) . ' >= ' . $this->db->quote($fdate->toSql()));
				}

				$toDate = $this->template->get('todate', false);

				if ($toDate)
				{
					$tdate = \JFactory::getDate($toDate);
					$query->where($this->db->quoteName('c.' . $checkDatefield) . ' <= ' . $this->db->quote($tdate->toSql()));
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

			// Add export limits
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

					// Clean some settings
					$attribs = '';
					$images = '';
					$urls = '';

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
								$query->clear()
									->select($this->db->quoteName('path'))
									->from($this->db->quoteName('#__categories'))
									->where($this->db->quoteName('id') . ' = ' . (int) $record->catid);
								$this->db->setQuery($query);
								$fieldvalue = $this->db->loadResult();
								break;
							case 'article_url':
								// Let's create a SEF URL
								$fieldvalue = $this->sef->getSefUrl(\ContentHelperRoute::getArticleRoute($record->id, $record->catid));
								break;
							case 'show_title':
							case 'link_titles':
							case 'show_intro':
							case 'show_category':
							case 'link_category':
							case 'show_parent_category':
							case 'link_parent_category':
							case 'show_author':
							case 'link_author':
							case 'show_create_date':
							case 'show_modify_date':
							case 'show_publish_date':
							case 'show_item_navigation':
							case 'show_icons':
							case 'show_print_icon':
							case 'show_email_icon':
							case 'show_vote':
							case 'show_hits':
							case 'show_noauth':
							case 'urls_position':
							case 'alternative_readmore':
							case 'article_layout':
							case 'show_publishing_options':
							case 'show_article_options':
							case 'show_urls_images_backend':
							case 'show_urls_images_frontend':
							case 'article_page_title':
							case 'show_tags':
							case 'info_block_position':
							case 'info_block_show_title':
							case 'show_associations':
								if (empty($attribs))
								{
									$attribs = json_decode($record->attribs);
								}

								if (isset($attribs->$fieldname))
								{
									$fieldvalue = $attribs->$fieldname;
								}
								break;
							case 'image_intro':
							case 'float_intro':
							case 'image_intro_alt':
							case 'image_intro_caption':
							case 'image_fulltext':
							case 'float_fulltext':
							case 'image_fulltext_alt':
							case 'image_fulltext_caption':
								if (empty($images))
								{
									$images = json_decode($record->images);
								}

								if (isset($images->$fieldname))
								{
									$fieldvalue = $images->$fieldname;
								}
								break;
							case 'urla':
							case 'urlatext':
							case 'targeta':
							case 'urlb':
							case 'urlbtext':
							case 'targetb':
							case 'urlc':
							case 'urlctext':
							case 'targetc':
								if (empty($urls))
								{
									$urls = json_decode($record->urls);
								}

								if (isset($urls->$fieldname))
								{
									$fieldvalue = $urls->$fieldname;
								}
								break;
							case 'tags':
								$query->clear()
									->select($this->db->quoteName('tag_id'))
									->from($this->db->quoteName('#__contentitem_tag_map'))
									->where($this->db->quoteName('content_item_id') . ' = ' . (int) $record->id)
									->where($this->db->quoteName('type_alias') . ' = ' . $this->db->quote('com_content.article'));
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
									$advClause[]  = 'c2.language != ' . $this->db->quote(Factory::getLanguage()->getTag());
									$associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $record->id, 'id', 'alias', 'catid', $advClause);

									foreach ($associations as $tag => $item)
									{
										$id                  = explode(':', $item->id);
										$alias               = $id[1];
										$language            = $item->language;
										$associationsArray[] = $language . '#' . $alias;
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
											'operation' => 'content',
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
				$this->addExportContent(\JText::_('COM_CSVI_NO_DATA_FOUND'));

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
				'operation' => 'content',
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
			->where($this->db->quoteName('context') . ' = ' . $this->db->quote('com_content.article'));
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach ($results as $result)
		{
			$this->customFields[] = $result->name;
		}

		$this->log->add('Load the Joomla custom fields for articles');
	}
}
