<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/
defined('JPATH_PLATFORM') or die;

class JFormFieldPlugincheck extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Plugincheck';
	
	public function __construct() {
		JFactory::getDocument()->addScriptDeclaration("
			document.addEventListener('DOMContentLoaded', function() {
				document.getElementById('component-form').setAttribute('enctype', 'multipart/form-data');
			});
		");
	}
	
	protected function getLabel() {
		return '';
	}
	
	protected function getInput() {
		if (!JPluginHelper::isEnabled('system', 'rsseo')) {
			return JText::_('COM_RSSEO_PLEASE_ENABLE_RSSEO_PLUGIN');
		} else {
			JFactory::getDocument()->addStyleDeclaration("#webmasters .control-group:first-child { display:none; }");
		}
	}
}