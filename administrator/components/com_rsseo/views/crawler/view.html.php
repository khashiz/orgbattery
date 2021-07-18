<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewCrawler extends JViewLegacy
{	
	public function display($tpl = null) {
		
		$config			= JFactory::getConfig();
		$this->config  	= rsseoHelper::getConfig();
		$this->offline 	= $config->get('offline');
		$this->shared	= $config->get('shared_session');
		
		if ($this->offline) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_RSSEO_CRAWLER_SITE_OFFLINE'), 'error');
		}
		
		$this->document->addScriptDeclaration('RSSeo.seconds = '.$this->config->request_timeout.';');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_CRAWLER'),'rsseo');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}