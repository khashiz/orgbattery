<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewAnalytics extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->config	= rsseoHelper::getConfig();
		$this->app		= JFactory::getApplication();
		
		if ($this->app->input->getInt('ajax',0)) {
			$layout = $this->getLayout();
			$this->{$layout} = $this->get('GA'.ucfirst($layout));
		} else {
			// Check if we can show the analytics form
			$this->check();
			
			// Check if the user is authentified and the token is valid
			$this->valid = $this->get('IsValid');
			
			if (!$this->valid) {
				$this->document->addScriptDeclaration("var rsseoWindow;");
				$this->document->addScriptDeclaration("function rsOpenWindow(url) { rsseoWindow = window.open(url, 'gconnect', 'width=800, height=600'); }");
				$this->document->addScriptDeclaration("function rsCloseWindow() { rsseoWindow.close(); }");
			} else {
				$this->document->addScriptDeclaration("jQuery(document).ready(function () {
					if (jQuery('#profile').val() != '') {
						RSSeo.updateAnalytics();
					}
				});
				google.load('visualization', '1', {packages: ['corechart','corechart']});");
			}
			
			// Get the authorization URL
			$this->auth = $this->get('AuthUrl');
			
			// Get user profiles
			$this->profiles = $this->get('Profiles');
			$this->selected = $this->get('Selected');
			
			$now			= JFactory::getDate()->toUnix(); 
			$this->rsstart	= JHtml::_('date', ($now - 604800), 'Y-m-d');
			$this->rsend	= JHtml::_('date', ($now - 86400), 'Y-m-d');
			$this->tabs		= $this->get('Tabs');
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
		
		if ($this->app->input->getInt('ajax')) {
			$this->app->close();
		}
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_GOOGLE_ANALYTICS'),'rsseo');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
		
		$this->document->addScript('https://www.google.com/jsapi');
	}
	
	protected function check() {
		if (!extension_loaded('curl')) {
			$this->app->enqueueMessage(JText::_('COM_RSSEO_NO_CURL'));
			$this->app->redirect('index.php?option=com_rsseo');
		}
		
		if (trim($this->config->analytics_client_id) == '' || trim($this->config->analytics_secret) == '' || $this->config->analytics_enable == 0) {
			$this->app->enqueueMessage(JText::_('COM_RSSEO_GA_ERROR'));
			$this->app->redirect('index.php?option=com_rsseo');
		}
	}
}