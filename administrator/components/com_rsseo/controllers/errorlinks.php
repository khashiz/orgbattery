<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoControllerErrorlinks extends JControllerLegacy
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rsseoControllerErrorlinks
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		$cid = array_map('intval', $cid);
		
		// Get the model.
		$model = $this->getModel('Errorlinks');
		
		$model->delete($cid);
		
		$this->setMessage(JText::_('COM_RSSEO_ERROR_LINKS_REMOVED'));
		$this->setRedirect(JRoute::_('index.php?option=com_rsseo&view=errorlinks',false));
	}
	
	public function createRedirect() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		$cid = array_map('intval', $cid);
		
		if ($cid) {
			return $this->setRedirect(JRoute::_('index.php?option=com_rsseo&view=redirect&layout=edit&eid='.implode(',',$cid) , false));
		}
		
		return $this->setRedirect(JRoute::_('index.php?option=com_rsseo&view=errorlinks', false));
	}
}