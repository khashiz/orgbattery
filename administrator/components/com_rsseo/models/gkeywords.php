<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoModelGkeywords extends JModelList
{
	protected $gapi;
	
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'name', 'site'
			);
		}
		
		$rsseoCfg	= rsseoHelper::getConfig();
		$secret		= JFactory::getConfig()->get('secret');
		
		try {
			require_once JPATH_ADMINISTRATOR.'/components/com_rsseo/helpers/gapi.php';
		
			$options = array(
				'email'		=> $rsseoCfg->accountID,
				'scope'		=> 'https://www.googleapis.com/auth/webmasters.readonly',
				'key'		=> file_get_contents(JPATH_ADMINISTRATOR.'/components/com_rsseo/assets/keys/'.md5($secret.'private_key').'.p12')
			);
			
			$this->gapi = rsseoGoogleAPI::getInstance($options);
		} catch (Exception $e) {
			rsseoHelper::saveLog('gkeywords', JText::sprintf('COM_RSSEO_LOG_MESSAGE', $e->getMessage(), __FILE__, __LINE__));
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		
		parent::__construct($config);
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// Select fields
		$query->select('*');
		
		// Select from table
		$query->from($db->qn('#__rsseo_gkeywords'));
		
		// Filter by site
		if ($site = $this->getState('filter.site')) {
			$query->where($db->qn('site').' = '.$db->q($site));
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where($db->qn('name').' LIKE '.$search.' ');
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'id');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);
		
		return $query;
	}
	
	public function getSites() {
		try {
			return ($this->gapi instanceof rsseoGoogleAPI) ? $this->gapi->getSites() : array();
		} catch (Exception $e) {
			rsseoHelper::saveLog('gkeywords', JText::sprintf('COM_RSSEO_LOG_MESSAGE', $e->getMessage(), __FILE__, __LINE__));
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
	
	public function import() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$date	= $input->getString('date');
		$i		= 0;
		
		$query->select($db->qn('name'))->select($db->qn('site'))
			->from($db->qn('#__rsseo_gkeywords'))
			->where($db->qn('id').' = '.$input->getInt('id'));
		$db->setQuery($query);
		if ($keyword = $db->loadObject()) {
			try {
				$options = array('keyword' => $keyword->name, 'site' => $keyword->site, 'start' => $date, 'end' => $date);
				if ($data = $this->gapi->getSearchData($options)) {
					foreach ($data as $object) {
						$query->clear()
							->insert($db->qn('#__rsseo_gkeywords_data'))
							->set($db->qn('idk').' = '.$db->q($input->getInt('id')))
							->set($db->qn('date').' = '.$db->q($date))
							->set($db->qn('page').' = '.$db->q($object->keys[1]))
							->set($db->qn('device').' = '.$db->q($object->keys[2]))
							->set($db->qn('country').' = '.$db->q($object->keys[3]))
							->set($db->qn('clicks').' = '.$db->q($object->clicks))
							->set($db->qn('impressions').' = '.$db->q($object->impressions))
							->set($db->qn('ctr').' = '.$db->q($object->ctr))
							->set($db->qn('position').' = '.$db->q($object->position));
						
						$db->setQuery($query);
						$db->execute();
						$i++;
					}
				}
				
				return '<i class="fa fa-check fa-fw"></i> '.JText::sprintf('COM_RSSEO_GKEYWORDS_IMPORTED', $i, $date);
				
			} catch (Exception $e) {
				rsseoHelper::saveLog('gkeywords', JText::sprintf('COM_RSSEO_LOG_MESSAGE', $e->getMessage(), __FILE__, __LINE__));
				$this->setError($e->getMessage());
				return false;
			}
		} else {
			$this->setError(JText::_('COM_RSSEO_GKEYWORDS_IMPORT_ERROR_NO_KEYWORD'));
			return false;
		}
	}
	
	public function getLogs() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('*')
			->from($db->qn('#__rsseo_logs'))
			->where($db->qn('type').' = '.$db->q('gkeywords'))
			->order($db->qn('date').' DESC');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}