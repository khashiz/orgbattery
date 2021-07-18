<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewError extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item			= $this->get('Item');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_ERROR_NEW_EDIT'),'rsseo');
		
		$this->document->addScriptDeclaration("RSSeo.errorType(".(isset($this->item->type) ? $this->item->type : 1).");");
			
		JToolBarHelper::apply('error.apply');
		JToolBarHelper::save('error.save');
		JToolBarHelper::cancel('error.cancel');
	}
}