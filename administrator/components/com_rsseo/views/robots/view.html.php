<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.folder');
jimport( 'joomla.filesystem.file');

class rsseoViewRobots extends JViewLegacy
{
	public function display($tpl = null) {
		$this->check 		= $this->get('IsFile');
		$this->writtable	= $this->get('IsWrittable');
		$this->contents		= $this->get('Contents');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_ROBOTS'),'rsseo');
		
		if ($this->check && $this->writtable) {
			JToolBarHelper::custom('saverobots', 'save', 'save', JText::_('COM_RSSEO_ROBOTS_SAVE'), false);
		}
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}