<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.folder');
jimport( 'joomla.filesystem.file');

class rsseoViewSitemap extends JViewLegacy
{
	public function display($tpl = null) {
		$this->sitemap		= JFile::exists(JPATH_SITE.'/sitemap.xml');
		$this->ror			= JFile::exists(JPATH_SITE.'/ror.xml');
		$this->form			= $this->get('Form');
		$this->percent		= $this->get('Percent');
		
		JHtml::_('formbehavior.chosen', '.advancedSelect');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_SITEMAP'),'rsseo');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}