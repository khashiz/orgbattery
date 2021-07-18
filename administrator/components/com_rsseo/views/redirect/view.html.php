<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewRedirect extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item			= $this->get('Item');
		$this->referrers	= $this->get('Referrers');
		$this->eid			= JFactory::getApplication()->input->getString('eid','');
		
		if (!$this->eid) { 
			$this->document->addScriptDeclaration("jQuery(function($){ jQuery('#jform_from').on('keyup', function() { RSSeo.generateRSResults(1); }); });");
		}
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_REDIRECT_NEW_EDIT'),'rsseo');
		
		if ($this->eid) {
			JToolBarHelper::save('redirect.savemultiple');
		} else {
			JToolBarHelper::apply('redirect.apply');
			JToolBarHelper::save('redirect.save');
			JToolBarHelper::save2new('redirect.save2new');
		}
		
		JToolBarHelper::cancel('redirect.cancel');
	}
}