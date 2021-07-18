<?php
/**
 * @package     CSVI
 * @subpackage  Install
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - @year@ RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;

/**
 * Script to run on installation of CSVI package.
 *
 * @package     CSVI
 * @subpackage  Install
 * @since       6.0
 */
class Pkg_CsviInstallerScript extends InstallerScript
{
	/**
	 * Extension script constructor.
	 *
	 * @since   3.0.0
	 */
	public function __construct()
	{
		$this->minimumJoomla = '3.7';
		$this->minimumPhp    = '7.1';
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   string  $type    The type of installation being done
	 * @param   object  $parent  The parent calling class
	 *
	 * @return void
	 *
	 * @since   7.0
	 *
	 * @throws  RuntimeException
	 */
	public function postflight($type, $parent)
	{
		// All Joomla loaded, set our exception handler
		require_once JPATH_ADMINISTRATOR . '/components/com_csvi/rantai/error/exception.php';

		// Load the default classes
		require_once JPATH_ADMINISTRATOR . '/components/com_csvi/controllers/default.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_csvi/models/default.php';

		// Setup the autoloader
		JLoader::registerPrefix('Csvi', JPATH_ADMINISTRATOR . '/components/com_csvi');

		// Load language files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_csvi', JPATH_ADMINISTRATOR . '/components/com_csvi/', 'en-GB', true);
		$jlang->load('com_csvi', JPATH_ADMINISTRATOR . '/components/com_csvi/', $jlang->getDefault(), true);
		$jlang->load('com_csvi', JPATH_ADMINISTRATOR . '/components/com_csvi/', null, true);

		// Load the tasks
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_csvi/models');
		$tasksModel = JModelLegacy::getInstance('Task', 'CsviModel', array('ignore_request' => true));

		try
		{
			$tasksModel->reload();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Clean the update sites if needed
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('update_site_id'))
			->from($db->quoteName('#__update_sites'))
			->where($db->quoteName('name') . ' = ' . $db->quote('RO CSVI'))
			->where($db->quoteName('type') . ' = ' . $db->quote('extension'));
		$db->setQuery($query);
		$entry = $db->loadResult();

		if (!$entry)
		{
			$query->clear()
				->insert($db->quoteName('#__update_sites'))
				->columns(
					$db->quoteName(
						array(
							'name',
							'type',
							'location',
							'enabled',
							'last_check_timestamp'
						)
					)
				)
				->values(
					$db->quote('RO CSVI') . ', '
					. $db->quote('extension') . ', '
					. $db->quote('https://csvimproved.com/updates/csvipro.xml') . ', '
					. '1, '
					. '0'
				);
			$db->setQuery($query)->execute();
		}

		// Remove SCF plugin, we don't need it anymore
		$query->clear()
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('csviext'))
			->where($db->quoteName('element') . ' = ' . $db->quote('stockablecustomfields'));
		$extensionId = $db->setQuery($query)->loadResult();

		if ($extensionId)
		{
			$installer = JInstaller::getInstance();
			$installer->uninstall('plugin', $extensionId);
			JFactory::getApplication()->enqueueMessage(JText::_('PKG_ROCSVI_STOCKABLE_CUSTOM_FIELDS_REMOVED'));
		}
	}
}
