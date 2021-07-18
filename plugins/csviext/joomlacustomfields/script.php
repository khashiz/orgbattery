<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaCategories
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Joomla! Custom Fields extension installer.
 *
 * @package  CSVI
 * @since    7.15.0
 */
class PlgcsviextjoomlacustomfieldsInstallerScript
{
	/**
	 * Actions to perform after installation.
	 *
	 * @param   object  $parent  The parent object.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   7.15.0
	 */
	public function postflight($parent)
	{
		// Load the application
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		try
		{
			// Enable the plugin
			$query = $db->getQuery(true)
				->update($db->quoteName('#__extensions'))
				->set($db->quoteName('enabled') . ' =  1')
				->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
				->where($db->quoteName('element') . ' = ' . $db->quote('joomlacustomfields'))
				->where($db->quoteName('folder') . ' = ' . $db->quote('csviext'));

			$db->setQuery($query)->execute();
			$app->enqueueMessage(JText::_('PLG_CSVIEXT_PLUGIN_ENABLED'));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage());

			return false;
		}

		return true;
	}
}
