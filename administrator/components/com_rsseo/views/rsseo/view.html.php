<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewRsseo extends JViewLegacy
{
	public function display($tpl=null) {
		$this->version		= (string) new RSSeoVersion();
		$this->code			= rsseoHelper::getConfig('global_register_code');
		$this->statistics	= rsseoHelper::getStatistics();
		$this->pages		= rsseoHelper::getMostVisited();
		$this->lastcrawled	= $this->get('LastCrawled');
		$this->info			= $this->get('Info');
		$this->keywords		= $this->get('Keywords');
		$this->cache		= $this->get('Cache');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_GLOBAL_COMPONENT'),'rsseo');
		
		if ($this->keywords) {
			$this->document->addScript('https://www.gstatic.com/charts/loader.js');
			$this->document->addScriptDeclaration("google.charts.load('current', {packages: ['corechart', 'line']});
			google.charts.setOnLoadCallback(function() {
				RSSeo.drawGoogleKeywordChartDashboard();
			});

			jQuery(document).ready(function() {
				jQuery(window).resize(function() {
					RSSeo.drawGoogleKeywordChartDashboard();
				});
			});");
		}
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}