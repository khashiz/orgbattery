<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewBackup extends JViewLegacy
{
	public function display($tpl = null) {
		$this->process = JFactory::getApplication()->input->getString('process');
		
		if ($this->process == 'backup') {
			$this->backup = $this->backup();
		} else if ($this->process == 'restore') {
			$this->restore = $this->restore();
		}
		
		$this->cleanup();
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_BACKUP_RESTORE'),'rsseo');
		
		if ($this->process) {
			JToolBar::getInstance('toolbar')->appendButton('Link', 'arrow-left', JText::_('COM_RSSEO_GLOBAL_BACK'), 'index.php?option=com_rsseo&view=backup');
		}
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
	
	protected function backup() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsseo/helpers/backup.php';
		
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$options	= array();
		
		$tables	= array('#__rsseo_competitors' => 'id',
			'#__rsseo_pages' => 'id', 
			'#__rsseo_redirects' => 'id', 
			'#__rsseo_keywords' => 'id', 
			'#__rsseo_errors' => 'id',
			'#__rsseo_gkeywords_data' => 'id',
			'#__rsseo_gkeywords' => null
		);
		
		foreach ($tables as $table => $primary) {
			$options['queries'][] = array('query' => 'SELECT * FROM '.$table , 'primary' => $primary);
		}
		
		$package = new RSPackage($options);
		$package->backup();
		return $package->displayProgressBar();
	}
	
	protected function restore() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsseo/helpers/backup.php';
		
		$options = array();
		$options['redirect'] = 'index.php?option=com_rsseo&view=backup';
		
		$package = new RSPackage($options);
		$package->restore();
		return $package->displayProgressBar();
	}
	
	protected function cleanup() {
		jimport('joomla.filesystem.folder');
		
		if ($folder = JFactory::getApplication()->input->getString('delfolder')) {
			$folder = base64_decode($folder);
			if (JFolder::exists($folder))
				JFolder::delete($folder);
		}
	}
}