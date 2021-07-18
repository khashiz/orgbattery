<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewKeyword extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item			= $this->get('Item');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_KEYWORD_NEW_EDIT'),'rsseo');
		
		JToolBarHelper::apply('keyword.apply');
		JToolBarHelper::save('keyword.save');
		JToolBarHelper::save2new('keyword.save2new');
		JToolBarHelper::cancel('keyword.cancel');
	}
}