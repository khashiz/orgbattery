<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoModelStatistics extends JModelLegacy
{
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$app	= JFactory::getApplication();
		$input	= $app->input;
		
		// Get pagination request variables
		$limitp = $app->getUserStateFromRequest('com_rsseo.pageviews.limit', 'limit', 10, 'int');
		$limitstartp = $input->getInt('limitstart', 0);
		$limitv = $app->getUserStateFromRequest('com_rsseo.visitors.limit', 'limit', 10, 'int');
		$limitstartv = $input->getInt('limitstart', 0);
		
		// In case limit has been changed, adjust it
		$limitstartp = ($limitp != 0 ? (floor($limitstartp / $limitp) * $limitp) : 0);
		$limitstartv = ($limitv != 0 ? (floor($limitstartv / $limitv) * $limitv) : 0);

		$this->setState('com_rsseo.pageviews.limit', $limitp);
		$this->setState('com_rsseo.pageviews.limitstart', $limitstartp);
		$this->setState('com_rsseo.visitors.limit', $limitv);
		$this->setState('com_rsseo.visitors.limitstart', $limitstartv);
		
		$this->setPageViewsQuery();
		$this->setVisitorsQuery();
	}
	
	public function getTotalVisitors() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		// Get total visitors
		$query->select('COUNT(DISTINCT('.$db->qn('session_id').'))')
			->from($db->qn('#__rsseo_visitors'));
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	public function getTotalPageViews() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		// Get total visitors
		$query->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_visitors'));
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	public function getTotalVisitorsTimeframe() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$input		= JFactory::getApplication()->input;
		$dFrom		= JFactory::getDate()->modify('-7 days')->toSql();
		$dTo		= JFactory::getDate()->setTime(23,59,59)->toSql();
		$from		= $input->getString('from', $dFrom);
		$to			= $input->getString('to', $dTo);
		$from		= JFactory::getDate($from)->setTime(0,0,0)->toSql();
		$to			= JFactory::getDate($to)->setTime(23,59,59)->toSql();
		
		// Get total visitors
		$query->select('COUNT(DISTINCT('.$db->qn('session_id').'))')
			->from($db->qn('#__rsseo_visitors'))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($from))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) < '.$db->q($to));
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	public function getTotalPageViewsTimeframe() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$input		= JFactory::getApplication()->input;
		$dFrom		= JFactory::getDate()->modify('-7 days')->toSql();
		$dTo		= JFactory::getDate()->setTime(23,59,59)->toSql();
		$from		= $input->getString('from', $dFrom);
		$to			= $input->getString('to', $dTo);
		$from		= JFactory::getDate($from)->setTime(0,0,0)->toSql();
		$to			= JFactory::getDate($to)->setTime(23,59,59)->toSql();
		
		// Get total visitors
		$query->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_visitors'))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($from))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) < '.$db->q($to));
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	public function getChartVisitors() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$input		= JFactory::getApplication()->input;
		$dFrom		= JFactory::getDate()->modify('-7 days')->toSql();
		$dTo		= JFactory::getDate()->setTime(23,59,59)->toSql();
		$from		= $input->getString('from', $dFrom);
		$to			= $input->getString('to', $dTo);
		$from		= JFactory::getDate($from)->setTime(0,0,0)->toSql();
		$to			= JFactory::getDate($to)->setTime(23,59,59)->toSql();
		$return		= array();
		
		// Get the visitors
		$query->clear()
			->select('COUNT(DISTINCT('.$db->qn('session_id').')) AS count')
			->select('DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)) as thedate')
			->from($db->qn('#__rsseo_visitors'))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($from))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) < '.$db->q($to))
			->group('DATE('.$db->qn('date').')');
		$db->setQuery($query);
		$visitors = $db->loadObjectList();
		
		if ($visitors) {
			$return[] = array(JText::_('COM_RSSEO_CHART_DATE'), JText::_('COM_RSSEO_CHART_VISITORS'));
			
			foreach ($visitors as $visitor) {
				$date = JFactory::getDate($visitor->thedate)->format('d M Y');
				$return[] = array($date, (int) $visitor->count);
			}
		}
		
		return $return;
	}
	
	public function getChartPageViews() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$input		= JFactory::getApplication()->input;
		$dFrom		= JFactory::getDate()->modify('-7 days')->toSql();
		$dTo		= JFactory::getDate()->setTime(23,59,59)->toSql();
		$from		= $input->getString('from', $dFrom);
		$to			= $input->getString('to', $dTo);
		$from		= JFactory::getDate($from)->setTime(0,0,0)->toSql();
		$to			= JFactory::getDate($to)->setTime(23,59,59)->toSql();
		$return		= array();
		
		// Get the pageviews
		$query->clear()
			->select('COUNT('.$db->qn('id').') AS count')
			->select('DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)) as thedate')
			->from($db->qn('#__rsseo_visitors'))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($from))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) < '.$db->q($to))
			->group('DATE('.$db->qn('date').')');
		$db->setQuery($query);
		$pageviews = $db->loadObjectList();
		
		if ($pageviews) {
			$return[] = array(JText::_('COM_RSSEO_CHART_DATE'), JText::_('COM_RSSEO_CHART_PAGEVIEWS'));
			
			foreach ($pageviews as $pageview) {
				$date = JFactory::getDate($pageview->thedate)->format('d M Y');
				$return[] = array($date, (int) $pageview->count);
			}
		}
		
		return $return;
	}
	
	public function getVisitors() {
		$db		= JFactory::getDbo();
		
		$db->setQuery($this->visitorsQuery, $this->getState('com_rsseo.visitors.limitstart'), $this->getState('com_rsseo.visitors.limit'));
		return $db->loadObjectList();
	}
	
	public function getVisitorsTotal() {
		$db		= JFactory::getDbo();
		
		$db->setQuery($this->visitorsQuery);
		$db->execute();
		
		return (int) $db->getNumRows();
	}
	
	protected function setVisitorsQuery() {
		$db			= JFactory::getDbo();
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$input		= JFactory::getApplication()->input;
		$dFrom		= JFactory::getDate()->modify('-7 days')->toSql();
		$dTo		= JFactory::getDate()->setTime(23,59,59)->toSql();
		$from		= $input->getString('from', $dFrom);
		$to			= $input->getString('to', $dTo);
		$from		= JFactory::getDate($from)->setTime(0,0,0)->toSql();
		$to			= JFactory::getDate($to)->setTime(23,59,59)->toSql();
		
		$subquery = $db->getQuery(true)
			->clear()
			->select($db->qn('session_id'))->select('MAX('.$db->qn('date').') AS '.$db->qn('date'))
			->from($db->qn('#__rsseo_visitors'))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($from))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) < '.$db->q($to))
			->group($db->qn('session_id'));
		
		$this->visitorsQuery = 'SELECT * FROM ('.$subquery.') AS filtered_rsseo_visitors JOIN '.$db->qn('#__rsseo_visitors').' USING ('.$db->qn('session_id').', '.$db->qn('date').') GROUP BY '.$db->qn('session_id').', '.$db->qn('date').' ORDER BY '.$db->qn('date').' DESC';
	}
	
	public function getPageViews() {
		$db		= JFactory::getDbo();
		
		$db->setQuery($this->pageviewsQuery, $this->getState('com_rsseo.pageviews.limitstart'), $this->getState('com_rsseo.pageviews.limit'));
		return $db->loadObjectList();
	}
	
	public function getPageViewsTotal() {
		$db		= JFactory::getDbo();
		
		$db->setQuery($this->pageviewsQuery);
		$db->execute();
		
		return (int) $db->getNumRows();
	}
	
	public function getPageViewsPagination() {
		jimport('joomla.html.pagination');
		return new JPagination($this->getPageViewsTotal(), $this->getState('com_rsseo.pageviews.limitstart'), $this->getState('com_rsseo.pageviews.limit'));
	}
	
	protected function setPageViewsQuery() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$id		= $input->getInt('id',0);
		
		if ($input->get('layout','') == 'pageviews') {		
			$query->select('v2.*')
				->select($db->qn('u.username'))
				->from($db->qn('#__rsseo_visitors','v1'))
				->join('LEFT',$db->qn('#__rsseo_visitors','v2').' ON '.$db->qn('v1.session_id').' = '.$db->qn('v2.session_id'))
				->join('LEFT',$db->qn('#__users','u').' ON '.$db->qn('v2.user_id').' = '.$db->qn('u.id'))
				->where($db->qn('v1.id').' = '.$db->q($id))
				->order($db->qn('date').' ASC');
				
			$this->pageviewsQuery = (string) $query;
		}
	}
}