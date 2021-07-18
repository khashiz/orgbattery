<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoTableError extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rsseo_errors', 'id', $db);
	}
	
	public function check() {
		if (parent::check()) {
			if ($this->type == 2 && empty($this->url)) {
				$this->setError(JText::_('COM_RSSEO_ERRORS_EMPTY_URL'));
				return false;
			}
			
			if ($this->type == 1 && empty($this->layout)) {
				$this->setError(JText::_('COM_RSSEO_ERRORS_EMPTY_LAYOUT'));
				return false;
			}
			
			$this->itemid = (int) $this->itemid;
			
			return true;
		}
	}
	
	public function store($updateNulls = false) {
		// Verify that the error code is unique
		$table = JTable::getInstance('Error', 'rsseoTable', array('dbo' => $this->getDbo()));
		if ($table->load(array('error' => $this->error)) && ($table->id != $this->id || $this->id == 0)) {
			$this->setError(JText::_('COM_RSSEO_ERRORS_SAME_ERROR_CODE'));
			return false;
		}
		
		return parent::store($updateNulls);
	}
}