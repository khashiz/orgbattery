<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!JFactory::getUser()->authorise('core.manage', 'com_rsseo')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

require_once(JPATH_COMPONENT.'/helpers/rsseo.php');
require_once(JPATH_COMPONENT.'/helpers/adapter/adapter.php');
require_once(JPATH_COMPONENT.'/controller.php');
JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rsseo/helpers');

// Load scripts
rsseoHelper::setScripts('administrator');
// Check for keywords config
rsseoHelper::keywords();

$controller	= JControllerLegacy::getInstance('RSSeo');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();