<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewPages extends JViewLegacy
{
	public function display($tpl = null) {
		$this->simple		= JFactory::getSession()->get('com_rsseo.pages.simple',false);
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->config 		= rsseoHelper::getConfig();
		$this->batch		= $this->get('BatchFields');
		$this->sef			= JFactory::getConfig()->get('sef');
		$this->filterForm	= $this->get('FilterForm');
		$this->activeFilters= $this->get('ActiveFilters');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_LIST_PAGES'),'rsseo');
		
		$toolbar = JToolbar::getInstance('toolbar');
		JToolBarHelper::addNew('page.add');
		
		if (rsseoHelper::isJ4()) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			
			$childBar->edit('page.edit')->listCheck(true);
			
			$childBar->delete('pages.delete')
				->text('JTOOLBAR_DELETE')
				->message('JGLOBAL_CONFIRM_DELETE')
				->icon('icon-trash')
				->listCheck(true);
			
			$childBar->publish('pages.publish')->listCheck(true);
			$childBar->unpublish('pages.unpublish')->listCheck(true);
			
			$toolbar->appendButton('Confirm',JText::_('COM_RSSEO_DELETE_ALL_PAGES_MESSAGE',true),'delete',JText::_('COM_RSSEO_DELETE_ALL_PAGES'),'pages.removeall',false);
			
			if (!$this->simple) {
				$childBar->appendButton(
					'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'pages.addsitemap\')" '
					. 'class="button-addsitemap dropdown-item"><span class="fas fa-plus" aria-hidden="true"></span>'
					. JText::_('COM_RSSEO_PAGE_ADDTOSITEMAP') . '</button></joomla-toolbar-button>', 'addsitemap'
				);
				$childBar->appendButton(
					'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'restore.removesitemap\')" '
					. 'class="button-removesitemap dropdown-item"><span class="fas fa-trash" aria-hidden="true"></span>'
					. JText::_('COM_RSSEO_PAGE_REMOVEFROMSITEMAP') . '</button></joomla-toolbar-button>', 'removesitemap'
				);
				$childBar->appendButton(
					'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'restore\')" '
					. 'class="button-restore dropdown-item"><span class="fas fa-flag" aria-hidden="true"></span>'
					. JText::_('COM_RSSEO_RESTORE_PAGES') . '</button></joomla-toolbar-button>', 'restore'
				);
				$childBar->appendButton(
					'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'refresh\')" '
					. 'class="button-refresh dropdown-item"><span class="fas fa-refresh" aria-hidden="true"></span>'
					. JText::_('COM_RSSEO_BULK_REFRESH') . '</button></joomla-toolbar-button>', 'refresh'
				);
				$toolbar->appendButton(
					'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'pages.simple\')" '
					. 'class="btn"><span class="fas fa-compress" aria-hidden="true"></span>'
					. JText::_('COM_RSSEO_SIMPLE_VIEW') . '</button></joomla-toolbar-button>', 'simple'
				);
			} else {
				$toolbar->appendButton(
					'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'pages.standard\')" '
					. 'class="btn"><span class="fas fa-expand" aria-hidden="true"></span>'
					. JText::_('COM_RSSEO_STANDARD_VIEW') . '</button></joomla-toolbar-button>', 'standard'
				);
			}
		} else {
			JToolBarHelper::editList('page.edit');
			JToolBarHelper::deleteList('COM_RSSEO_PAGE_CONFIRM_DELETE','pages.delete');
			$toolbar->appendButton('Confirm',JText::_('COM_RSSEO_DELETE_ALL_PAGES_MESSAGE',true),'delete',JText::_('COM_RSSEO_DELETE_ALL_PAGES'),'pages.removeall',false);
			JToolBarHelper::publishList('pages.publish');
			JToolBarHelper::unpublishList('pages.unpublish');
			
			if (!$this->simple) {
				JToolBarHelper::custom('pages.addsitemap','new','new',JText::_('COM_RSSEO_PAGE_ADDTOSITEMAP'));
				JToolBarHelper::custom('pages.removesitemap','trash','trash',JText::_('COM_RSSEO_PAGE_REMOVEFROMSITEMAP'));
				JToolBarHelper::custom('restore','flag','flag',JText::_('COM_RSSEO_RESTORE_PAGES'));
				JToolBarHelper::custom('refresh','refresh','refresh',JText::_('COM_RSSEO_BULK_REFRESH'));
				JToolBarHelper::custom('pages.simple','contract','contract',JText::_('COM_RSSEO_SIMPLE_VIEW'),false);
			} else {
				JToolBarHelper::custom('pages.standard','expand','expand',JText::_('COM_RSSEO_STANDARD_VIEW'),false);
			}
		}
		
		$layout = new JLayoutFile('joomla.toolbar.popup');
		$dhtml = $layout->render(array('text' => JText::_('COM_RSSEO_BATCH'), 'btnClass' => 'btn', 'htmlAttributes' => '', 'name' => 'batchpages', 'selector' => 'batchpages', 'class' => 'icon-checkbox-partial', 'doTask' => ''));
		$toolbar->appendButton('Custom', $dhtml, 'batch');
		
		$script = array();
		$script[] = "Joomla.submitbutton = function(task) {";
		$script[] = "if (task == 'refresh') {";
		$script[] = "jQuery('input[name=\"cid[]\"]:checked').each(function() {";
		$script[] = $this->config->crawler_type == 'ajax' ? "jQuery('#refresh' + jQuery(this).val()).click();" : "RSSeo.checkPage(jQuery(this).val(),0);";
		$script[] = "});";
		$script[] = "} else if (task == 'restore') {";
		$script[] = "jQuery('input[name=\"cid[]\"]:checked').each(function() {";
		$script[] = $this->config->crawler_type == 'ajax' ? "jQuery('#restore' + jQuery(this).val()).click();" : "RSSeo.checkPage(jQuery(this).val(),1);";
		$script[] = "});";
		$script[] = "} else Joomla.submitform(task);";
		$script[] = "return false;";
		$script[] = "}";
		
		$this->document->addScriptDeclaration(implode("\n", $script));
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}