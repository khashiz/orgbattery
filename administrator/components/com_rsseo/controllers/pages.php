<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

class rsseoControllerPages extends JControllerAdmin
{
	protected $text_prefix = 'COM_RSSEO_PAGES';
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rsseoControllerPages
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('removesitemap',	'addsitemap');
	}
	
	/**
	 *	Method to include or exculde pages from the sitemap
	 *
	 * @return	void
	 */
	public function addsitemap() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids    = JFactory::getApplication()->input->get('cid', array(), 'array');
		$values = array('addsitemap' => 1, 'removesitemap' => 0);
		$task   = $this->getTask();
		$value  = ArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids)) {
			throw new Exception(JText::_('JERROR_NO_ITEMS_SELECTED'), 500);
		} else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->addsitemap($ids, $value)) {
				$this->setMessage($model->getError(),'error');
			}
		}
		
		$this->setRedirect('index.php?option=com_rsseo&view=pages');
	}
	
	/**
	 *	Method to remove all pages
	 *
	 * @return	void
	 */
	public function removeall() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the model.
		$model = $this->getModel();

		// Publish the items.
		if (!$model->removeall()) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('COM_RSSEO_ALL_PAGES_DELETED'));
		}
		
		$this->setRedirect('index.php?option=com_rsseo&view=pages');
	}
	
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Page', $prefix = 'rsseoModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 * @since   1.6
	 */
	public function batch() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel();
		$pks    = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		if (!$model->batchProcess($pks)) {
			throw new Exception($model->getError(), 500);
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_RSSEO_BATCH_COMPLETED'));
		}
		
		// Preset the redirect
		$this->setRedirect('index.php?option=com_rsseo&view=pages');
	}
	
	public function simple() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		JFactory::getSession()->set('com_rsseo.pages.simple',true);
		
		$this->setRedirect('index.php?option=com_rsseo&view=pages');
	}
	
	public function standard() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		JFactory::getSession()->set('com_rsseo.pages.simple',false);
		
		$this->setRedirect('index.php?option=com_rsseo&view=pages');
	}
	
	public function ajax() {
		// Get the model.
		$model = $this->getModel();
		
		$model->ajax();
		
		die();
	}
}