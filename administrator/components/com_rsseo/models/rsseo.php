<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoModelRsseo extends JModelLegacy
{
	
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	public function getLastCrawled() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('id'))->select($db->qn('url'))
			->select($db->qn('sef'))->select($db->qn('date'))
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('crawled').' = '.$db->q(1))
			->order($db->qn('date').' DESC');
		
		$db->setQuery($query,0,10);
		return $db->loadObjectList();
	}
	
	public function getInfo() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$info	= new stdClass();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('title').' = '.$db->q(''))
			->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$info->missing_title = (int) $db->loadResult();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('keywords').' = '.$db->q(''))
			->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$info->missing_keywords = (int) $db->loadResult();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('description').' = '.$db->q(''))
			->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$info->missing_description = (int) $db->loadResult();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$info->total_pages = (int) $db->loadResult();
		
		return $info;
	}
	
	public function getKeywords() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)->select('*')->from($db->qn('#__rsseo_gkeywords'));
		$array	= array(array(JHtml::_('select.option', '', JText::_('COM_RSSEO_GKEYWORD_SELECT'))));
		
		$db->setQuery($query);
		if ($keywords = $db->loadObjectList()) {
			foreach ($keywords as $keyword) {
				$array[$keyword->site][$keyword->id] = JHtml::_('select.option', $keyword->id, $keyword->name);
			}
		}
		
		return $array;
	}
	
	public function getCache() {
		$data = new stdClass();
		
		JFactory::getApplication()->triggerEvent('onrsseo_cache', array(array('data' => &$data)));
		
		return $data;
	}
}