<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoControllerAnalytics extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rsseoControllerSitemap
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	public function connect() {
		$model = $this->getModel('Analytics');
		
		try {
			$model->connect();
		} catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage());
		}
		
		$this->setRedirect('index.php?option=com_rsseo&view=analytics');
	}
	
	public function logout() {
		$model = $this->getModel('Analytics');
		
		$session = JFactory::getSession();
		$session->clear('rsseo.access_token');
		unset($_COOKIE['rsseoAnalyticsID']);
		setcookie('rsseoAnalyticsID', null, -1, '/');
		
		$this->setRedirect('index.php?option=com_rsseo&view=analytics');
	}
}