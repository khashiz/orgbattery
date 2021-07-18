<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewErrorlinks extends JViewLegacy
{	
	public function display($tpl = null) {
		$layout = $this->getLayout();
		
		if ($layout == 'referrals') {
			$this->referrals	= $this->get('Referrals');
		} else {
			$this->items 		= $this->get('Items');
			$this->pagination 	= $this->get('Pagination');
			$this->state 		= $this->get('State');
			$this->filterForm	= $this->get('FilterForm');
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_LIST_ERROR_LINKS'),'rsseo');
		JToolBarHelper::custom('errorlinks.createRedirect','new', 'new', 'COM_RSSEO_CREATE_REDIRECT');
		JToolBarHelper::deleteList('COM_RSSEO_GLOBAL_CONFIRM_DELETE', 'errorlinks.delete');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}