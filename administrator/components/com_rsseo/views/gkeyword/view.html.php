<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewGkeyword extends JViewLegacy
{
	public function display($tpl = null) {
		$template			= JFactory::getApplication()->input->get('tpl');
		$tpl				= $template ? $template : $tpl;
		$this->form 		= $this->get('Form');
		$this->item			= $this->get('Item');
		$this->dates		= $this->get('Dates');
		$this->data			= $this->get('Data');
		$this->total		= $this->get('Total');
		$this->json			= $this->get('Json');
		$this->devices		= $this->get('Devices');
		$this->device		= $this->get('Device');
		$this->countries	= $this->get('Countries');
		$this->country		= $this->get('Country');
		$this->from			= $this->get('From');
		$this->to			= $this->get('To');
		
		if ($tpl == 'page') {
			$this->items 	= $this->get('Pages');
		}
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_($this->item->id ? 'COM_RSSEO_GKEYWORD_VIEW_DATA' : 'COM_RSSEO_GKEYWORD_NEW'),'rsseo');
		
		JToolBarHelper::apply('gkeyword.apply');
		JToolBarHelper::save('gkeyword.save');
		JToolBarHelper::save2new('gkeyword.save2new');
		JToolBarHelper::cancel('gkeyword.cancel');
		
		if ($this->item->id) {
			$this->document->addScript('https://www.gstatic.com/charts/loader.js');
			
			if ($this->data) {
				$this->document->addScriptDeclaration("RSSeo.jsonPositionChartData = ".$this->json.";
					google.charts.load('current', {packages: ['corechart', 'line']});
					google.charts.setOnLoadCallback(function() {
						RSSeo.drawGoogleKeywordChart();
					});
		
					jQuery(document).ready(function() {
						jQuery(window).resize(function() {
							RSSeo.drawGoogleKeywordChart();
						});
					});
				");
			}
			
			// Get the toolbar object instance
			$layout = new JLayoutFile('joomla.toolbar.popup');
			$dhtml = $layout->render(array('text' => JText::_('COM_RSSEO_GKEYWORD_IMPORT'), 'btnClass' => 'btn', 'htmlAttributes' => '', 'selector' => 'process-data', 'name' => 'process-data', 'class' => 'icon-cog', 'doTask' => ''));
			JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'process');
		}
	}
}