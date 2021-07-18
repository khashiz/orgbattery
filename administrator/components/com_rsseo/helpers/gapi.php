<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE.'/administrator/components/com_rsseo/helpers/Google/autoload.php';

class rsseoGoogleAPI {
	
	protected static $client;
	
	protected static $sessionID;
	
	public function __construct($options) {
		$client = new Google_Client();
		
		if (isset($options['clientID'])) {
			$client->setClientId($options['clientID']);
			$client->setClientSecret($options['clientSecret']);
			$client->setRedirectUri($options['redirect']);
			$client->addScope($options['scope']);
			
			self::$sessionID = $options['sessionID'];
			
		} else if (isset($options['email'])) {
			$credentials = new Google_Auth_AssertionCredentials($options['email'], $options['scope'], $options['key']);
			$client->setAssertionCredentials($credentials);
			
			if ($client->getAuth()->isAccessTokenExpired()) {
				$client->getAuth()->refreshTokenWithAssertion();
			}
		}
		
		self::$client = $client;
	}
	
	public static function getInstance($options) {
		static $instance;
		
		if (!$instance) {
			$instance = new rsseoGoogleAPI($options);
		}
		
		return $instance;
	}
	
	// Create the authentification URL
	public static function getAuthUrl() {
		return self::$client->createAuthUrl();
	}
	
	// Get the access token
	public static function getToken() {
		$session = JFactory::getSession();
		return $session->get(self::$sessionID,'');
	}
	
	// Authenticate user
	public static function authenticate() {
		$code = JFactory::getApplication()->input->getString('code','');
		if ($code) {
			if (self::$client->authenticate($code)) {
				$session = JFactory::getSession();
				$session->set(self::$sessionID, self::$client->getAccessToken());
			}
		}
	}
	
	// Is the current access token a valid one ?
	public static function valid() {
		$token = self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			$invalid = self::$client->isAccessTokenExpired();
			
			if ($invalid) {
				$session = JFactory::getSession();
				$session->clear(self::$sessionID);
			}
			
			return $invalid;
		}
		
		return true;
	}
	
	// Get Webmasters sites options array
	public static function getSites($select = false) {
		$data	 = $select ? array(JHTML::_('select.option', '', JText::_('COM_RSSEO_GKEYWORDS_SELECT_SITE'))) : array();
		
		$cache = JFactory::getCache('rsseo_google_sites');
		$cache->setCaching(true);
		$cache->setLifeTime(300);
		$array = $cache->get(array('rsseoGoogleAPI', 'getSitesData'));
		$cache->gc();
		
		if ($array) {
			foreach ($array as $site) {
				$data[] = JHTML::_('select.option', $site->siteUrl, $site->siteUrl);
			}
		}
		
		return $data;
	}
	
	// Get Webmasters sites
	public static function getSitesData() {
		$service = new Google_Service_Webmasters(self::$client);
		$sites	 = $service->sites->listSites();
		
		return $sites->getSiteEntry();
	}
	
	// Get webmasters search analytics
	public static function getSearchData($options = array()) {
		$service = new Google_Service_Webmasters(self::$client);
		$request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest;
		$filter	 = new Google_Service_Webmasters_ApiDimensionFilterGroup;
		$dFilter = new Google_Service_Webmasters_ApiDimensionFilter;
		
		$request->setStartDate($options['start']);
		$request->setEndDate($options['end']);
		$request->setDimensions(array('query', 'page', 'device', 'country'));
		$request->setSearchType('web');
		$request->setRowLimit(5000);
		
		$dFilter->setDimension('query');
		$dFilter->setOperator('equals');
		$dFilter->setExpression($options['keyword']);
		
		$filter->setFilters(array($dFilter));
		$request->setDimensionFilterGroups(array($filter));
		
		$keywords = $service->searchanalytics->query($options['site'], $request);
		
		return $keywords->getRows();
	}
	
	// Get users profiles
	public static function getProfiles($select = true) {
		$token	= self::getToken();
		$data	= $select ? array(JHTML::_('select.option', '', JText::_('COM_RSSEO_SELECT_GA_ACCOUNT'))) : array();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$service	= new Google_Service_Analytics(self::$client);
			$accounts	= $service->management_accounts->listManagementAccounts();

			if ($accounts->getItems()) {
				foreach ($accounts->getItems() as $account) {
					$properties = $service->management_webproperties->listManagementWebproperties($account['id']);
					if ($properties->getItems()) {
						foreach ($properties->getItems() as $property) {
							$profiles = $service->management_profiles->listManagementProfiles($account['id'], $property->getId());
							if ($profiles->getItems()) {
								foreach ($profiles->getItems() as $profile) {
									$data[] = JHTML::_('select.option', $profile->getId(), $profile->getName().' ('.$property->getName().')');
								}
							}
						}
					}
				}
			}
		}
		
		return $data;
	}
	
	// Get general data
	public static function general() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			$general = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:percentNewSessions,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:uniquePageviews');
			
			return $general->getTotalsForAllResults();
		}
		
		return false;
	}
	
	// Get new vs returning sessions
	public static function newvsreturning() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$newvsreturning = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:pageviewsPerSession,ga:avgSessionDuration,ga:bounceRate', array('dimensions' => 'ga:userType'));
			return $newvsreturning->getRows();
		}
		
		return false;
	}
	
	public static function sessions() {
		$token	= self::getToken();
		$data	= array();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$sessions = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions', array('dimensions' => 'ga:date'));
			$data['rows']  = $sessions->getRows();
			$data['total'] = $sessions->getTotalsForAllResults();
			
			return $data;
		}
		
		return false;
	}
	
	public static function geocountry() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$geocountry = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration', array('dimensions' => 'ga:country', 'sort' => '-ga:sessions'));
			return $geocountry->getRows();
		}
		
		return false;
	}
	
	public static function browsers() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$browsers = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:pageviewsPerSession,ga:avgSessionDuration,ga:bounceRate', array('dimensions' => 'ga:browser', 'sort' => '-ga:sessions'));
			return $browsers->getRows();
		}
		
		return false;
	}
	
	public static function mobiles() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$mobiles = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:pageviewsPerSession,ga:avgSessionDuration,ga:bounceRate', array('dimensions' => 'ga:operatingSystem','segment' => 'gaid::-14', 'sort' => '-ga:sessions'));
			return $mobiles->getRows();
		}
		
		return false;
	}
	
	public static function sources() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$sources = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:pageviewsPerSession,ga:avgSessionDuration,ga:bounceRate,ga:percentNewSessions', array('dimensions' => 'ga:source,ga:medium','sort' => '-ga:sessions', 'max-results' => 20));
			
			return $sources->getRows();
		}
		
		return false;
	}
	
	public static function sourcesChart() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$directvisits = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions', array('dimensions' => 'ga:medium','filters' => 'ga:medium==(none)'));
			$directvisitstotal = $directvisits->getTotalsForAllResults();
			
			$searchvisits = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions', array('dimensions' => 'ga:medium','filters' => 'ga:medium==organic'));
			$searchvisitstotal = $searchvisits->getTotalsForAllResults();
			
			$refferingvisits = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions', array('dimensions' => 'ga:medium','filters' => 'ga:medium==referral'));
			$refferingvisitstotal = $refferingvisits->getTotalsForAllResults();
			
			return array($directvisitstotal['ga:sessions'],$searchvisitstotal['ga:sessions'],$refferingvisitstotal['ga:sessions']);
		}
		
		return false;
	}
	
	public static function content() {
		$token	= self::getToken();
		
		if ($token) {
			self::$client->setAccessToken($token);
			
			$input	 = JFactory::getApplication()->input;
			$profile = $input->getInt('profile',0);
			$start	 = $input->getString('start','30daysAgo');
			$end	 = $input->getString('end','yesterday');
			$service = new Google_Service_Analytics(self::$client);
			
			$content = $service->data_ga->get('ga:'.$profile, $start, $end, 'ga:sessions,ga:pageviews,ga:uniquePageviews,ga:exitRate,ga:avgTimeOnPage,ga:bounceRate', array('dimensions' => 'ga:pagePath','sort' => '-ga:pageviews', 'max-results' => 20));
			return $content->getRows();
		}
		
		return false;
	}
}