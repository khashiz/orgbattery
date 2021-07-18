<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

if ($task = JFactory::getApplication()->input->get('task')) {
	require_once JPATH_COMPONENT.'/controller.php';
	
	$controller	= JControllerLegacy::getInstance('RSSeo');
	$controller->execute($task);
	$controller->redirect();
} else {
	require_once JPATH_COMPONENT.'/helper.php';
	
	$params	= JFactory::getApplication()->getParams('com_rsseo');

	if ($params->get('show_page_heading', 1)) {
		echo '<h1>'.$params->get('page_heading').'</h1>';
	}

	echo rsseoMenuHelper::generateSitemap();
}