<?php
/**
 * @package     CSVI
 * @subpackage  Install
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;
use Joomla\Registry\Registry;

/**
 * Script to run on installation of CSVI.
 *
 * @package     CSVI
 * @subpackage  Install
 * @since       6.0
 */
class Com_CsviInstallerScript extends InstallerScript
{
	/**
	 * The version of the extension installed
	 *
	 * @var   string
	 *
	 * @since  7.4.1
	 */
	protected $extensionVersion;

	/**
	 * Extension script constructor.
	 *
	 * @since   3.0.0
	 */
	public function __construct()
	{
		$this->minimumJoomla = '3.7';
		$this->minimumPhp    = '7.1';

		$this->deleteFiles = array(
			'/administrator/components/com_csvi/models/abouts.php',
			'/administrator/language/en-GB/en-GB.com_csvi.ini',
			'/administrator/language/en-GB/en-GB.com_csvi.sys.ini',
			'/administrator/components/com_csvi/assets/css/images/index.html',
			'/administrator/components/com_csvi/addon/com_categories/install/csvi_templates.xml',
			'/administrator/components/com_csvi/log.txt',
			'/administrator/components/com_csvi/assets/js/autocomplete.js',
			'/administrator/components/com_csvi/assets/js/jquery-ui.js',
			'/administrator/components/com_csvi/assets/js/jquery.js',
			'/administrator/components/com_csvi/controllers/availablefield.php',
			'/administrator/components/com_csvi/controllers/cpanel.php',
			'/administrator/components/com_csvi/controllers/settings.php',
			'/administrator/components/com_csvi/controllers/settings.php',
			'/administrator/components/com_csvi/models/abouts.php',
			'/administrator/components/com_csvi/models/analyzers.php',
			'/administrator/components/com_csvi/models/forms/settings_google.xml',
			'/administrator/components/com_csvi/models/forms/settings_icecat.xml',
			'/administrator/components/com_csvi/models/forms/settings_log.xml',
			'/administrator/components/com_csvi/models/forms/settings_site.xml',
			'/administrator/components/com_csvi/models/forms/settings_yandex.xml',
			'/administrator/components/com_csvi/models/settings.php',
			'/administrator/components/com_csvi/views/map/tmpl/form.php',
			'/administrator/components/com_csvi/views/rule/tmpl/form.php',
			'/administrator/components/com_csvi/views/task/tmpl/form.form.xml',
			'/administrator/components/com_csvi/views/task/tmpl/form.php',
			'/administrator/components/com_csvi/views/templatefield/tmpl/form.php',
			'/administrator/components/com_csvi/views/templates/tmpl/form.default.xml',
			'/administrator/components/com_csvi/views/templates/view.form.php',
			'/administrator/components/com_csvi/controllers/addons.php',
			'/administrator/components/com_csvi/dispatcher.php',
			'/administrator/components/com_csvi/helper/db.php',
			'/administrator/components/com_csvi/models/addons.php',
			'/administrator/components/com_csvi/models/maintenances.php',
			'/administrator/components/com_csvi/toolbar.php',
			'/administrator/components/com_csvi/views/imports/tmpl/steps.php',
			'/components/com_csvi/controllers/exports.php',
			'/components/com_csvi/controllers/imports.php',
			'/components/com_csvi/models/imports.php',
		);

		$this->deleteFolders = array(
			'/administrator/components/com_csvi/views/settings',
			'/administrator/components/com_csvi/views/cpanel',
			'/administrator/components/com_csvi/assets/render',
			'/administrator/components/com_csvi/views/addons',
			'/administrator/components/com_csvi/views/default',
			'/administrator/components/com_csvi/addon',
			'/layouts/csvi',
			'/components/com_csvi',
		);
	}


	/**
	 * Method to install the component
	 *
	 * @param   string  $type    Installation type (install, update, discover_install)
	 * @param   object  $parent  The parent calling class
	 *
	 * @return  boolean  True to let the installation proceed, false to halt the installation
	 *
	 * @since   6.0
	 *
	 * @throws  \Exception
	 */
	public function preflight($type, $parent)
	{
		if (!defined('CSVIPATH_DEBUG'))
		{
			define('CSVIPATH_DEBUG', JPath::clean(JFactory::getConfig()->get('log_path'), '/'));
		}

		// Get the extension version number
		$this->extensionVersion = $parent->get('manifest')->version;

		// Clean up files and folders if any
		$this->removeFiles();

		/** @var JDatabaseDriver $db */
		$db     = JFactory::getDbo();
		$tables = $db->getTableList();
		$table  = $db->getPrefix() . 'csvi_settings';
		$app    = JFactory::getApplication();
		$query  = $db->getQuery(true);

		// Move the settings from the old csvi_settings to the Joomla Global Settings
		if (in_array($table, $tables))
		{
			try
			{
				$query->clear()
					->select($db->quoteName('params'))
					->from($db->quoteName($table))
					->where($db->quoteName('csvi_setting_id') . ' = 1');
				$db->setQuery($query);
				$csvisettings = $db->loadResult();
			}
			catch (Exception $e)
			{
				$csvisettings = false;
			}

			if ($csvisettings === false)
			{
				// The csvi_settings table exists but not the csvi_setting_id column, let's try with the old ID column
				try
				{
					$query->clear()
						->select($db->quoteName('params'))
						->from($db->quoteName($table))
						->where($db->quoteName('id') . ' = 1');
					$db->setQuery($query);
					$csvisettings = $db->loadResult();
				}
				catch (Exception $e)
				{
					$app->enqueueMessage(JText::_('COM_CSVI_INSTALL_CORRUPT_TABLE'));

					return false;
				}
			}

			// Make sure the user has any saved settings
			if ($csvisettings !== '')
			{
				$csviregistry = json_decode($csvisettings, true);

				$query->clear()
					->select($db->quoteName('params'))
					->from($db->quoteName('#__extensions'))
					->where($db->quoteName('element') . ' = ' . $db->quote('com_csvi'))
					->where($db->quoteName('type') . ' = ' . $db->quote('component'));
				$db->setQuery($query);
				$extsettings = $db->loadResult();

				if (!$extsettings)
				{
					$extsettings = array();
				}

				$extregistry = json_decode($extsettings, true);
				$newparams   = array_merge($csviregistry, $extregistry);
				$newparams   = new Registry($newparams);

				$query->clear()
					->update($db->quoteName('#__extensions'))
					->set($db->quoteName('params') . ' = ' . $db->quote($newparams))
					->where($db->quoteName('element') . ' = ' . $db->quote('com_csvi'))
					->where($db->quoteName('type') . ' = ' . $db->quote('component'));
				$db->setQuery($query)->execute();
			}
		}

		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   string  $type    The type of installation being done
	 * @param   object  $parent  The parent calling class
	 *
	 * @return void
	 *
	 * @since   6.0
	 *
	 * @throws  RuntimeException
	 */
	public function postflight($type, $parent)
	{
		// Check the database structure is OK
		$this->checkDatabase();

		// Convert any pre version 6 templates if needed
		$this->convertTemplates();
	}

	/**
	 * Convert old templates to the new CSVI 6 format.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 *
	 * @throws  RuntimeException
	 */
	private function convertTemplates()
	{
		/** @var JDatabaseDriver $db */
		$db = JFactory::getDbo();

		// Load all the existing templates
		$query = $db->getQuery(true)
			->select(
				array(
					$db->quoteName('csvi_template_id'),
					$db->quoteName('settings'),
					$db->quoteName('template_alias'),
					$db->quoteName('template_name')
				)
			)
			->from($db->quoteName('#__csvi_templates'));
		$db->setQuery($query);
		$templates = $db->loadObjectList('csvi_template_id');

		$existingTemplates = [];

		foreach ($templates as $csvi_template_id => $template)
		{
			// Collect existing template ids
			$existingTemplates[] = $csvi_template_id;

			// Check if the template is in the old format
			if (0 === strpos($template->settings, '{"options'))
			{
				// Get the old data format
				$oldformat = json_decode($template->settings);

				// Store everything in the new format
				$newformat = array();

				foreach ($oldformat as $section => $settings)
				{
					$newformat = array_merge($newformat, (array) $settings);
				}

				// Perform some extra changes
				if (isset($newformat['operation']))
				{
					$newformat['operation'] = str_replace(array('import', 'export'), '', $newformat['operation']);
				}

				if (isset($newformat['exportto']))
				{
					$newformat['exportto'] = array($newformat['exportto']);
				}

				// Store the new template format
				$query->clear()
					->update($db->quoteName('#__csvi_templates'))
					->set($db->quoteName('settings') . ' = ' . $db->quote(json_encode($newformat)))
					->where($db->quoteName('csvi_template_id') . ' = ' . (int) $csvi_template_id);
				$db->setQuery($query)->execute();
			}

			// Set template alias if it is empty
			if (!$template->template_alias)
			{
				$templateAlias = JApplicationHelper::stringURLSafe($template->template_name);
				$query->clear()
					->select($db->quoteName('csvi_template_id'))
					->from($db->quoteName('#__csvi_templates'))
					->where($db->quoteName('template_alias') . ' = ' . $db->quote($templateAlias));
				$db->setQuery($query);
				$templateId = $db->loadResult();

				if ($templateId)
				{
					require_once JPATH_ADMINISTRATOR . '/components/com_csvi/models/template.php';
					$templateModel  = new CsviModelTemplate();
					$templateAlias = $templateModel->generateDuplicateAlias($templateAlias);
				}

				$query->clear()
					->update($db->quoteName('#__csvi_templates'))
					->set($db->quoteName('template_alias') . ' = ' . $db->quote($templateAlias))
					->where($db->quoteName('csvi_template_id') . ' = ' . (int) $csvi_template_id);
				$db->setQuery($query)->execute();
			}
		}

		// Clean up leftover template fields
		if ($existingTemplates)
		{
			$this->cleanTemplateRelations($existingTemplates);
		}
	}

	/**
	 * Actions to perform after un-installation.
	 *
	 * @param   object  $parent  The parent object.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   7.0.1
	 */
	public function uninstall($parent)
	{
		// Clean up the cache
		$cache = JFactory::getCache('com_csvi', '');
		$cache->clean('com_csvi');

		return true;
	}

	/**
	 * Check the database structure.
	 *
	 * @return  void
	 *
	 * @since   7.5.0
	 */
	private function checkDatabase()
	{
		/** @var JDatabaseDriver $db */
		$db = JFactory::getDbo();

		require_once JPATH_ADMINISTRATOR . '/components/com_csvi/helper/database.php';
		$databaseCheck = new CsviHelperDatabase($db);
		$databaseCheck->process(JPATH_ADMINISTRATOR . '/components/com_csvi/assets/core/database.xml');
	}

	/**
	 * Clean Rules and template fields
	 *
	 * @param  array $existingTemplates Existing template id array
	 *
	 * @return  void
	 *
	 * @since   7.15.1
	 */
	private function cleanTemplateRelations($existingTemplates)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('csvi_templatefield_id'))
			->from($db->quoteName('#__csvi_templatefields'))
			->where($db->quoteName('csvi_template_id') . ' NOT IN ( ' . implode(',', $existingTemplates) . ')');
		$db->setQuery($query);
		$templateFields = $db->loadColumn();

		if ($templateFields)
		{
			$query->clear()
				->delete($db->quoteName('#__csvi_templatefields'))
				->where($db->quoteName('csvi_templatefield_id') . ' IN ( ' . implode(',', $templateFields) . ')');
			$db->setQuery($query)->execute();

			$query->clear()
				->delete($db->quoteName('#__csvi_templatefields_rules'))
				->where($db->quoteName('csvi_templatefield_id') . ' IN ( ' . implode(',', $templateFields) . ')');
			$db->setQuery($query)->execute();
		}

		$query->clear()
			->delete($db->quoteName('#__csvi_processes'))
			->where($db->quoteName('csvi_template_id') . ' NOT IN ( ' . implode(',', $existingTemplates) . ')');
		$db->setQuery($query)->execute();
	}
}
