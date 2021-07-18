<?php
/**
 * @package     RolandD
 * @subpackage  Installer
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2020 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Load the RolandD installer.
 *
 * @package  CSVI
 *
 * @since    1.0.0
 */
class PlgsystemrolanddInstallerScript
{
	/**
	 * Run the postflight operations.
	 *
	 * @param   object  $parent  The parent class.
	 *
	 * @return  bool True on success | False on failure.
	 *
	 * @since   1.0.0
	 * @throws  Exception
	 */
	public function postflight($parent)
	{
		$app = Factory::getApplication();
		/** @var JDatabaseDriver $db */
		$db = Factory::getDbo();

		// Enable the plugin
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' =  1')
			->where($db->quoteName('element') . ' = ' . $db->quote('rolandd'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);

		if (!$db->execute())
		{
			$app->enqueueMessage(Text::sprintf('PLG_SYSTEM_ROLANDD_PLUGIN_NOT_ENABLED', $db->getErrorMsg()), 'error');

			return false;
		}

		$app->enqueueMessage(Text::_('PLG_SYSTEM_ROLANDD_PLUGIN_ENABLED'));

		return true;
	}
}
