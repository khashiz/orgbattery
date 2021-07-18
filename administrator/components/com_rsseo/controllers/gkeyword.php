<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoControllerGkeyword extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since	1.6
	 */
	public function __construct() {
		parent::__construct();
	}
	
	public function import() {
		$model	= $this->getModel('gkeywords');
		$data	= $model->import();
		
		if ($data === false) {
			echo json_encode(array('error' => '<i class="fa fa-info-circle fa-fw"></i> '.$model->getError()));
		} else {
			echo json_encode(array('message' => $data));
		}
		
		JFactory::getApplication()->close();
	}
	
	public function statistics() {
		echo $this->getModel()->getStatistics();
		
		JFactory::getApplication()->close();
	}
}