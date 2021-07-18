<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewData extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form = $this->get('form');
		$this->tabs = $this->get('tabs');
		
		JPluginHelper::importPlugin('rsseo');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_STRUCTURED_DATA'),'rsseo');
		JToolBarHelper::apply('data.save');
		
		$this->document->addScriptDeclaration("jQuery(document).ready(function () {
			if (typeof(Storage) !== 'undefined') {
				if (sessionStorage.rsseoSelectedTab) {
					jQuery('#structuredDataTabs > li a[href=\"#' + sessionStorage.rsseoSelectedTab + '\"]').click();
				} else {
					jQuery('#structuredDataTabs > li a:first').click();
				}
				
				jQuery('#structuredDataTabs > li > a').click(function() {
					sessionStorage.rsseoSelectedTab = jQuery(this).attr('href').replace('#','');
				});
			}
		});");
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}