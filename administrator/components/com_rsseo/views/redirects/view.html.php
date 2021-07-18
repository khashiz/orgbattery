<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewRedirects extends JViewLegacy
{
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->filterForm	= $this->get('FilterForm');
		$this->activeFilters= $this->get('ActiveFilters');
		
		$this->addToolBar();
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_LIST_REDIRECTS'),'rsseo');
		
		JToolBarHelper::addNew('redirect.add');
		JToolBarHelper::editList('redirect.edit');
		JToolBarHelper::deleteList('COM_RSSEO_GLOBAL_CONFIRM_DELETE', 'redirects.delete');
		JToolBarHelper::publishList('redirects.publish');
		JToolBarHelper::unpublishList('redirects.unpublish');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo')) {
			JToolBarHelper::preferences('com_rsseo');
		}
	}
}