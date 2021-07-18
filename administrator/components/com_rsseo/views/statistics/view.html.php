<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewStatistics extends JViewLegacy
{
	public function display($tpl = null) {
		$layout			= $this->getLayout();
		$this->config	= rsseoHelper::getConfig();
		
		if ($layout == 'pageviews') {
			$this->pageviews	= $this->get('PageViews');
			$this->pagination	= $this->get('PageViewsPagination');
			
		} else {
			$this->from		= JFactory::getDate()->modify('-7 days')->format('Y-m-d');
			$this->to		= JFactory::getDate()->format('Y-m-d');
			
			$this->totalvisitors	= $this->get('TotalVisitors');
			$this->totalpageviews	= $this->get('TotalPageViews');
			$this->totalvisitorst	= $this->get('TotalVisitorsTimeframe');
			$this->totalpageviewst	= $this->get('TotalPageViewsTimeframe');
			
			$this->visitors		= $this->get('Visitors');
			$this->total		= $this->get('VisitorsTotal');
			$this->count		= count($this->visitors);
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_LIST_STATISTICS'),'rsseo');
		
		JToolBarHelper::deleteList('','removeVisitors');
		
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Confirm',JText::_('COM_RSSEO_DELETE_ALL_VISITORS_MESSAGE',true),'delete',JText::_('COM_RSSEO_DELETE_ALL_VISITORS'),'removeAllVisitors',false);
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
		
		$this->document->addScript('https://www.google.com/jsapi');
		$this->document->addScriptDeclaration("google.load('visualization', '1', {packages: ['corechart', 'line']});");
		$this->document->addScriptDeclaration("jQuery(document).ready(function() {
			RSSeo.updateCharts();
			
			jQuery(window).resize(function() {
				RSSeo.updateCharts();
			});
			
			jQuery('a[href=\"#stat-visitors\"]').on('click', function() {
				RSSeo.updateCharts();
			});
		});");
	}
}