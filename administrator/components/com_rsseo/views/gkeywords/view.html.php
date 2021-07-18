<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewGkeywords extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->config	= rsseoHelper::getConfig();

		// Check if we can show the Google keywords form
		$this->check();
		
		$this->state 		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->filterForm	= $this->get('FilterForm');
		$this->activeFilters= $this->get('ActiveFilters');
		$this->logs		 	= $this->get('Logs');
		
		$this->addToolBar();	
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_GKEYWORDS'),'rsseo');
		JToolBarHelper::addNew('gkeyword.add');
		JToolBarHelper::editList('gkeyword.edit');
		JToolBarHelper::deleteList('COM_RSSEO_GLOBAL_CONFIRM_DELETE', 'gkeywords.delete');
		
		// Get the toolbar object instance
		$layout = new JLayoutFile('joomla.toolbar.popup');
		$dhtml = $layout->render(array('text' => JText::_('COM_RSSEO_GKEYWORDS_LOG'), 'btnClass' => 'btn', 'htmlAttributes' => '', 'selector' => 'rsseo-logs', 'name' => 'rsseo-logs', 'class' => 'icon-list', 'doTask' => ''));
		JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'process');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
	
	protected function check() {
		$app	= JFactory::getApplication();
		$secret	= JFactory::getConfig()->get('secret');
		
		if (!extension_loaded('curl')) {
			$app->enqueueMessage(JText::_('COM_RSSEO_NO_CURL'));
			$app->redirect('index.php?option=com_rsseo');
		}
		
		if (trim($this->config->accountID) == '' || !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsseo/assets/keys/'.md5($secret.'private_key').'.p12')) {
			$app->enqueueMessage(JText::_('COM_RSSEO_WEBMASTERS_CREDENTIALS_ERROR'));
			$app->redirect('index.php?option=com_rsseo');
		}
	}
}