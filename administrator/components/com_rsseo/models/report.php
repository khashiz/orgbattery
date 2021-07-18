<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoModelReport extends JModelLegacy
{
	public function getForm() {
		$form = JForm::getInstance('report', JPATH_ADMINISTRATOR.'/components/com_rsseo/models/forms/report.xml', array('control' => 'jform'));
		$data = $this->getData();
		
		$form->bind($data);
		
		return $form;
	}
	
	public function getData($prop = null) {
		try {
			$data	= rsseoHelper::getConfig('report');
			$data	= json_decode($data);
		} catch(Exception $e) {
			$data = $this->defaults();
		}
		
		return !is_null($prop) ? $data->$prop : $data;
	}
	
	public function save($data) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$component	= JComponentHelper::getComponent('com_rsseo');
		$cparams	= $component->params;
		$data		= json_encode($data);
		
		if ($cparams instanceof JRegistry) {
			$cparams->set('report', $data);
			
			$query->update($db->qn('#__extensions'));
			$query->set($db->qn('params'). ' = '.$db->q((string) $cparams));
			$query->where($db->qn('extension_id'). ' = '. $db->q($component->id));
			
			$db->setQuery($query);
			$db->execute();
		}
		
		return true;
	}
	
	public function getStatistics() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsseo_statistics'));
		$db->setQuery($query);
		return (array) $db->loadObject();
	}
	
	public function getLastCrawled() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$limit		= (int) $this->getData('limit');
		
		$query->select($db->qn('id'))->select($db->qn('url'))
			->select($db->qn('sef'))->select($db->qn('date'))
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('crawled').' = '.$db->q(1))
			->order($db->qn('date').' DESC');
		
		$db->setQuery($query, 0, $limit);
		return $db->loadObjectList();
	}
	
	public function getNoTitle() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$limit		= (int) $this->getData('limit');
		
		$query->select($db->qn('id'))->select($db->qn('url'))
			->select($db->qn('sef'))->select($db->qn('date'))
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('title').' = '.$db->q(''))
			->where($db->qn('published').' = '.$db->q(1));
		
		$db->setQuery($query, 0, $limit);
		return $db->loadObjectList();
	}
	
	public function getNoDesc() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$limit		= (int) $this->getData('limit');
		
		$query->select($db->qn('id'))->select($db->qn('url'))
			->select($db->qn('sef'))->select($db->qn('date'))
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('description').' = '.$db->q(''))
			->where($db->qn('published').' = '.$db->q(1));
		
		$db->setQuery($query, 0, $limit);
		return $db->loadObjectList();
	}
	
	public function getMostVisited() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$limit		= (int) $this->getData('limit');
		
		$query->select($db->qn('id'))->select($db->qn('url'))
			->select($db->qn('sef'))->select($db->qn('hits'))
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('hits').' > 0')
			->order($db->qn('hits').' DESC');
		
		$db->setQuery($query, 0, $limit);
		return $db->loadObjectList();
	}
	
	public function getErrorLinks() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$limit		= (int) $this->getData('limit');
		
		$query->select($db->qn('url'))->select($db->qn('code'))
			->select($db->qn('count'))
			->from($db->qn('#__rsseo_error_links'))
			->order($db->qn('count').' DESC');
		
		$db->setQuery($query, 0, $limit);
		return $db->loadObjectList();
	}
	
	public function getCompetitors() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$data		= $this->getData();
		
		if (isset($data->competitors) && !empty($data->competitors)) {
			$data->competitors = array_map('intval', $data->competitors);
			
			$query->select('*')
				->from($db->qn('#__rsseo_competitors'))
				->where($db->qn('id').' IN ('.implode(',',$data->competitors).')');
			
			$db->setQuery($query);
			if ($competitors = $db->loadObjectList()) {
				foreach ($competitors as $competitor) {
					$competitor->name = str_replace(array('http://','https://'), '', $competitor->name);
					$competitor->googlep = $competitor->googlep == -1 ? '-' : number_format($competitor->googlep, 0, '', '.');
					$competitor->googleb = $competitor->googleb == -1 ? '-' : number_format($competitor->googleb, 0, '', '.');
					$competitor->googler = $competitor->googler == -1 ? '-' : number_format($competitor->googler, 0, '', '.');
					$competitor->bingp = $competitor->bingp == -1 ? '-' : number_format($competitor->bingp, 0, '', '.');
					$competitor->bingb = $competitor->bingb == -1 ? '-' : number_format($competitor->bingb, 0, '', '.');
					$competitor->alexa = $competitor->alexa == -1 ? '-' : number_format($competitor->alexa, 0, '', '.');
					$competitor->mozpagerank = $competitor->mozpagerank == -1 ? '-' : number_format($competitor->mozpagerank, 0, '', '.');
					$competitor->mozda = $competitor->mozda == -1 ? '-' : number_format($competitor->mozda, 0, '', '.');
					$competitor->mozpa = $competitor->mozpa == -1 ? '-' : number_format($competitor->mozpa, 0, '', '.');
				}
				
				return $competitors;
			}
		}
		
		return array();
	}
	
	public function getGKeywords() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$data		= $this->getData();
		
		if (isset($data->keywords) && !empty($data->keywords)) {
			$data->keywords = array_map('intval', $data->keywords);
			
			$query->select('*')
				->from($db->qn('#__rsseo_gkeywords'))
				->where($db->qn('id').' IN ('.implode(',',$data->keywords).')');
			
			$db->setQuery($query);
			if ($keywords = $db->loadObjectList()) {
				foreach ($keywords as $i => $keyword) {
					$query->clear()
						->select($db->qn('date'))
						->select('COUNT(DISTINCT '.$db->qn('page').') AS pages')
						->select('SUM('.$db->qn('impressions').') AS impressions')
						->select('SUM('.$db->qn('clicks').') AS clicks')
						->select('SUM('.$db->qn('position').' * '.$db->qn('impressions').') / SUM('.$db->qn('impressions').') AS avgposition')
						->select('AVG('.$db->qn('ctr').') AS ctr')
						->from($db->qn('#__rsseo_gkeywords_data'))
						->where($db->qn('idk').' = '.$db->q($keyword->id))
						->group($db->qn('date'));
					$db->setQuery($query);
					if ($gKeywordData = $db->loadObjectList()) {
						$pages = $impressions = $clicks = $avg = 0;
						
						foreach ($gKeywordData as $data) {
							$pages += (int) $data->pages;
							$impressions += (int) $data->impressions;
							$clicks += (int) $data->clicks;
							$avg += $data->avgposition;
						}
						
						$keywords[$i]->pages = $pages;
						$keywords[$i]->impressions = $impressions;
						$keywords[$i]->clicks = $clicks;
						$keywords[$i]->avg = number_format($avg / count($gKeywordData), 2);
						$keywords[$i]->ctr = number_format(($clicks / $impressions) * 100, 2).'%';
					} else {
						$keywords[$i]->pages = '-';
						$keywords[$i]->impressions = '-';
						$keywords[$i]->clicks = '-';
						$keywords[$i]->avg = '-';
						$keywords[$i]->ctr = '-';
					}
				}
				
				return $keywords;
			}
		}
		
		return array();
	}
	
	public function getTabs() {
		$tabs =  new RSSeoAdapterTabs('report');
		return $tabs;
	}
	
	protected function defaults() {
		return (object) array('email_report' => 0, 'email' => '', 'mode' => 'weekly', 'mode_days' => 1, 'mode_day' => 1, 'font' => 'times', 'orientation' => 'portrait', 'paper' => 'a4', 'statistics' => 0, 'last_crawled' => 0, 'most_visited' => 0, 'error_links' => 0, 'no_title' => 0, 'no_desc' => 0, 'limit' => 10, 'enable_competitors' => 0, 'competitors' => array(), 'enable_gkeywords' => 0, 'keywords' => array());
	}
}