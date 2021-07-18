<?php
/**
 * @package     CSVI
 * @subpackage  Administrator
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_csvi'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Get the input object
$jinput = JFactory::getApplication()->input;

// Include dependencies
jimport('joomla.application.component.controller');

// Define our version number
define('CSVI_VERSION', '7.20.0');

// Set CLI mode
define('CSVI_CLI', false);

// Setup the autoloader
JLoader::registerPrefix('Csvi', JPATH_ADMINISTRATOR . '/components/com_csvi');
JLoader::registerPrefix('Rantai', JPATH_ADMINISTRATOR . '/components/com_csvi/rantai');
JLoader::registerNamespace('phpseclib', JPATH_ADMINISTRATOR . '/components/com_csvi/assets/phpseclib/phpseclib', false, false, 'psr4');
JLoader::import('google.vendor.autoload', JPATH_ADMINISTRATOR . '/components/com_csvi/assets');

// All Joomla loaded, set our exception handler
require_once JPATH_BASE . '/components/com_csvi/rantai/error/exception.php';

// Load the helper class for the submenu
require_once JPATH_ADMINISTRATOR . '/components/com_csvi/helper/csvi.php';

// Set the folder path to the models
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_csvi/models');

// Get the database object
$db = JFactory::getDbo();

// Define the tmp folder
$config = JFactory::getConfig();

if (!defined('CSVIPATH_TMP'))
{
	$tmpPath = $config->get('tmp_path');

	if (!is_dir($tmpPath))
	{
		$tmpPath = JPath::clean(JPATH_SITE . '/tmp', '/');
	}

	define('CSVIPATH_TMP', $tmpPath . '/com_csvi');
}

if (!defined('CSVIPATH_DEBUG'))
{
	$logPath = $config->get('log_path');

	if (!is_dir($logPath))
	{
		$logPath = JPath::clean(JPATH_SITE . '/logs', '/');
	}

	define('CSVIPATH_DEBUG', $logPath);
}

// Set the global settings
$csvisettings = new CsviHelperSettings($db);

// Load jQuery framework because csvi.js depends on it. The rest is loaded by the Joomla template.
JHtml::_('jquery.framework');

// Add stylesheets
$document = JFactory::getDocument();
$document->addStyleSheetVersion(JUri::root() . 'administrator/components/com_csvi/assets/css/display.css');
$document->addStyleSheetVersion(JUri::root() . 'administrator/components/com_csvi/assets/css/dropzone.css');

//Load Dropzone js file
$document->addScriptVersion(JUri::root() . 'administrator/components/com_csvi/assets/js/dropzone.js');

// Load our own JS library
$document->addScriptVersion(JUri::root() . 'administrator/components/com_csvi/assets/js/csvi.js');

JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_csvi/models/fields');

// Add language strings to JavaScript
// General
JText::script('COM_CSVI_ERROR');
JText::script('COM_CSVI_ERROR_500');
JText::script('COM_CSVI_INFORMATION');
JText::script('COM_CSVI_CLOSE_DIALOG');
JText::script('COM_CSVI_NO_CLIENT_ID_AND_SECRET');

// Template wizard
JText::script('COM_CSVI_FILEHEADER');
JText::script('COM_CSVI_TEMPLATEHEADER');
JText::script('COM_CSVI_ERROR_NO_CONNECTION');

// About view
JText::script('COM_CSVI_ERROR_CREATING_FOLDER');

// Process
JText::script('COM_CSVI_ERROR_DURING_PROCESS');
JText::script('COM_CSVI_CHOOSE_TEMPLATE_FIELD');

// Maintenance
JText::script('COM_CSVI_ERROR_PROCESSING_RECORDS');

try
{
	// Load the defaults
	require_once JPATH_ADMINISTRATOR . '/components/com_csvi/controllers/default.php';
	require_once JPATH_ADMINISTRATOR . '/components/com_csvi/models/default.php';
	require_once JPATH_ADMINISTRATOR . '/components/com_csvi/tables/default.php';

	// Add the path of the form location
	JFormHelper::addFormPath(JPATH_ADMINISTRATOR . '/components/com_csvi/models/forms/');
	JFormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_csvi/models/fields/');

	$controller = JControllerLegacy::getInstance('csvi');
	$controller->execute($jinput->get('task'));
	$controller->redirect();

	$format = $jinput->getCmd('format', $jinput->getCmd('tmpl', ''));

	if (empty($format))
	{
		?>
        <div class="span10 center">
            <a href="https://rolandd.com/" target="_blank">RO CSVI</a> 7.20.0 | Copyright (C) 2006 - 2021
            <a href="https://rolandd.com/" target="_blank">RolandD Cyber Produksi</a>
        </div>
        <?php
	}
}
catch (Exception $e)
{
	$oldUrl = JUri::getInstance($_SERVER['HTTP_REFERER']);
	JFactory::getApplication()->redirect('index.php?option=com_csvi&view=' . $oldUrl->getVar('view', ''), $e->getMessage(), 'error');
}
