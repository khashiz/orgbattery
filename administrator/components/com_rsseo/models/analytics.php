<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoModelAnalytics extends JModelList
{
	protected $analytics;
	
	public function __construct() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsseo/helpers/gapi.php';
		
		$config = rsseoHelper::getConfig();
		
		$options = array(
			"clientID"		=> trim($config->analytics_client_id),
			"clientSecret"	=> trim($config->analytics_secret),
			"scope"			=> "https://www.googleapis.com/auth/analytics",
			"redirect"  	=> JURI::root()."administrator/index.php?option=com_rsseo&task=analytics.connect",
			"sessionID"		=> 'rsseo.access_token'
		);
		
		$this->analytics = rsseoGoogleAPI::getInstance($options);
		
		parent::__construct();
	}
	
	public function getTabs() {
		$tabs =  new RSSeoAdapterTabs('com-rsseo-analytics');
		return $tabs;
	}
	
	public function getIsValid() {
		return !$this->analytics->valid();
	}
	
	public function getAuthUrl() {
		return $this->analytics->getAuthUrl();
	}
	
	public function connect() {
		$this->analytics->authenticate();
	}
	
	public function getProfiles() {
		$config = rsseoHelper::getConfig();
		
		if (empty($config->analytics_profiles)) {
			return $this->analytics->getProfiles();
		} else {
			$profiles = $this->analytics->getProfiles();
			
			foreach ($profiles as $i => $profile) {
				if (!in_array($profile->value, $config->analytics_profiles) && $profile->value != '') {
					unset($profiles[$i]);
				}
			}
			
			return $profiles;
		}
	}
	
	public function getSelected() {
		return isset($_COOKIE['rsseoAnalyticsID']) ? $_COOKIE['rsseoAnalyticsID'] : null;
	}
	
	public function getGAgeneral() {
		try {
			$data = array();
			if ($general = $this->analytics->general()) {
				foreach ($general as $property => $value) {
					$string = strtoupper(str_replace(':','_',$property));
					$object = new stdClass();
					$object->title = JText::_('COM_RSSEO_GA_GENERAL_'.$string);
					$object->value = $value == '' ? JText::_('COM_RSSEO_NOT_AVAILABLE') : $this->clean($property, $value);
					$object->descr = JText::_('COM_RSSEO_GA_GENERAL_'.$string.'_DESC');
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAnewreturning() {
		try {
			$data = array();
			if ($newvsreturning = $this->analytics->newvsreturning()) {
				foreach ($newvsreturning as $array) {
					$object		= new stdClass;
					$key		= $array[0] == 'Returning Visitor' ? JText::_('COM_RSSEO_RETURNINGVISITOR') : JText::_('COM_RSSEO_NEWVISITOR');
					$data[$key] = array();
					
					$object->sessions	= isset($array[1]) ? $this->clean('ga:sessions', $array[1]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->pageviews	= isset($array[2]) ? $this->clean('ga:pageviewsPerSession', $array[2], 2) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->duration	= isset($array[3]) ? $this->clean('ga:avgSessionDuration', $array[3]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->bouncerate	= isset($array[4]) ? $this->clean('ga:bounceRate', $array[4]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					
					$data[$key] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAvisits() {
		try {
			$data = array();
			if ($sessions = $this->analytics->sessions()) {
				$total	= isset($sessions['total']) ? $sessions['total']['ga:sessions'] : 1;
				$rows	= isset($sessions['rows']) ? $sessions['rows'] : array();
				
				foreach ($rows as $row) {
					$object = new stdClass();
					$object->date	 	= isset($row[0]) ? JFactory::getDate(substr($row[0],0,4).'-'.substr($row[0],4,2).'-'.substr($row[0],6,2))->format('l, F d, Y') : '';
					$object->sessions	= isset($row[1]) ? $this->clean('ga:sessions', $row[1]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->percent	= isset($row[1]) && $total ? number_format((($row[1] * 100) / $total), 2). ' %' : '-';
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAgeocountry() {
		try {
			$data = array();
			if ($rows = $this->analytics->geocountry()) {
				
				foreach ($rows as $row) {
					$object = new stdClass();
					$object->country		= isset($row[0]) ? $row[0] : '';
					$object->visits			= isset($row[1]) ? $this->clean('ga:sessions', $row[1]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->newvisitsp		= isset($row[2]) ? $this->clean('ga:percentNewSessions', $row[2]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->newvisits		= isset($row[3]) ? $this->clean('ga:newUsers', $row[3]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->bouncerate		= isset($row[4]) ? $this->clean('ga:bounceRate', $row[4]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->pagesvisits	= isset($row[5]) ? $this->clean('ga:pageviewsPerSession', $row[5]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->avgtimesite	= isset($row[6]) ? $this->clean('ga:avgSessionDuration', $row[6]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAbrowsers() {
		try {
			$data = array();
			if ($rows = $this->analytics->browsers()) {
				
				foreach ($rows as $row) {
					$object = new stdClass();
					$object->browser		= isset($row[0]) ? $row[0] : '';
					$object->visits			= isset($row[1]) ? $this->clean('ga:sessions', $row[1]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->pagesvisits	= isset($row[2]) ? $this->clean('ga:pageviewsPerSession', $row[2]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->avgtimesite	= isset($row[3]) ? $this->clean('ga:avgSessionDuration', $row[3]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->bouncerate		= isset($row[4]) ? $this->clean('ga:bounceRate', $row[4]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAmobiles() {
		try {
			$data = array();
			if ($rows = $this->analytics->mobiles()) {
				
				foreach ($rows as $row) {
					$object = new stdClass();
					$object->browser		= isset($row[0]) ? $row[0] : '';
					$object->visits			= isset($row[1]) ? $this->clean('ga:sessions', $row[1]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->pagesvisits	= isset($row[2]) ? $this->clean('ga:pageviewsPerSession', $row[2]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->avgtimesite	= isset($row[3]) ? $this->clean('ga:avgSessionDuration', $row[3]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->bouncerate		= isset($row[4]) ? $this->clean('ga:bounceRate', $row[4]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAsources() {
		try {
			$data = array();
			if ($rows = $this->analytics->sources()) {
				
				foreach ($rows as $row) {
					$object = new stdClass();
					$object->source			= isset($row[0]) && isset($row[1]) ? $row[0].' / '.$row[1] : '';
					$object->visits			= isset($row[2]) ? $this->clean('ga:sessions', $row[2]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->pagesvisits	= isset($row[3]) ? $this->clean('ga:pageviewsPerSession', $row[3]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->avgtimesite	= isset($row[4]) ? $this->clean('ga:avgSessionDuration', $row[4]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->bouncerate		= isset($row[5]) ? $this->clean('ga:bounceRate', $row[5]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->newvisits		= isset($row[6]) ? $this->clean('ga:percentNewSessions', $row[6]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAcontent() {
		try {
			$data = array();
			if ($rows = $this->analytics->content()) {
				
				foreach ($rows as $row) {
					$object = new stdClass();
					$object->page			= isset($row[0]) ? $row[0] : '';
					$object->visits			= isset($row[1]) ? $this->clean('ga:sessions', $row[1]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->pageviews		= isset($row[2]) ? $this->clean('ga:pageviews', $row[2]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->upageviews		= isset($row[3]) ? $this->clean('ga:uniquePageviews', $row[3]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->exits			= isset($row[4]) ? $this->clean('ga:exitRate', $row[4]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->avgtimesite	= isset($row[5]) ? $this->clean('ga:avgTimeOnPage', $row[5]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					$object->bouncerate		= isset($row[6]) ? $this->clean('ga:bounceRate', $row[6]) : JText::_('COM_RSSEO_NOT_AVAILABLE');
					
					$data[] = $object;
				}
			}
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	public function getGAsourceschart() {
		try {
			$data = $this->analytics->sourcesChart();
		} catch (Exception $e) {
			$data = $e->getMessage();
		}
		
		return $data;
	}
	
	protected function clean($property, $value, $decimals = 0) {
		$percentage = array('ga:percentNewSessions','ga:bounceRate','ga:exitRate');
		$time = array('ga:avgSessionDuration','ga:avgTimeOnPage');
		
		if (in_array($property,$percentage)) {
			return number_format($value,2).' %';
		} else if (in_array($property, $time)) {
			return rsseoHelper::convertseconds(number_format($value,0));
		} else {
			return number_format($value,$decimals);
		}
	}
}