<?php
/**
 * @package     RolandD
 * @subpackage  Administrator
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

/**
 * Plugin to handle downloads
 *
 * @since  1.0.0
 */
class PlgSystemRolandd extends CMSPlugin
{
	/**
	 * Application
	 *
	 * @var    CMSApplication
	 * @since  7.15.1
	 */
	protected $app;

	/**
	 * Database handler
	 *
	 * @var    JDatabaseDriver
	 * @since  7.15.1
	 */
	protected $db;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * @var    string  base update url, to decide whether to process the event or not
	 *
	 * @since  1.0.0
	 */
	private $baseUrl = 'https://rolandd.com/';

	/**
	 * The extensions to download
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	private $extension = '';

	/**
	 * The extension title
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	private $extensionTitle = '';

	/**
	 * List of RO CSVI plugins
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	private $rocsviPlugins = [
		'csviext',
		'csviaddon'
	];

	/**
	 * List of RO Payments plugins
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	private $ropaymentsPlugins = [

	];

	/**
	 * Adding required headers for successful extension update
	 *
	 * @param   string  &$url      URL from which package is going to be downloaded
	 * @param   array   &$headers  Headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true    Always true, regardless of success
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// Are we trying to update our own extensions?
		if (strpos($url, $this->baseUrl) !== 0
			&& strpos($url, 'https://csvimproved.com') !== 0
			&& strpos($url, 'https://jdideal.nl') !== 0)
		{
			return true;
		}

		// Check which extension this is
		$uri          = new Uri($url);
		$cids         = $this->app->input->get('cid');
		$db           = $this->db;
		$query        = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'name',
						'type',
						'element',
						'folder'
					]
				)
			)
			->from($db->quoteName('#__updates'))
			->where($db->quoteName('element') . ' = ' . $db->quote($uri->getVar('element')));

		foreach ($cids as $cid)
		{
			$query->clear('where')
				->where($db->quoteName('update_id') . '= ' . (int) $cid);
			$db->setQuery($query);
			$extension = $db->loadObject();
			$this->extensionTitle = $extension->name;

			switch ($extension->type) {
				case 'package':
					if ($extension->element === 'pkg_csvi')
					{
						$this->extension = 'com_csvi';
					}

					if ($extension->element === 'pkg_jdidealgateway')
					{
						$this->extension = 'com_jdidealgateway';
					}
					break;
				case 'component':
					if ($extension->element === 'com_rousers')
					{
						$this->extension = 'com_rousers';
					}
					break;
				case 'plugin':
					if (in_array($extension->folder, $this->rocsviPlugins, true))
					{
						$this->extension = 'com_csvi';

					}

					if (in_array($extension->folder, $this->ropaymentsPlugins, true))
					{
						$this->extension = 'com_jdidealgateway';

					}
					break;
				case 'module':
					break;
			}
		}

		// Get the Download ID from component params
		$downloadId = ComponentHelper::getParams($this->extension)->get('downloadid', '');

		// Set Download ID first
		if (empty($downloadId))
		{
			Factory::getApplication()->enqueueMessage(
				Text::sprintf(
					'PLG_SYSTEM_ROLANDD_DOWNLOAD_ID_REQUIRED',
					$this->extensionTitle,
					Text::_('PLG_SYSTEM_ROLANDD_' . $this->extension)
				),
				'error'
			);

			return true;
		}
		// Append the Download ID
		else
		{
			$separator = strpos($url, '?') !== false ? '&' : '?';
			$url       .= $separator . 'key=' . $downloadId;
		}

		return true;
	}
}
