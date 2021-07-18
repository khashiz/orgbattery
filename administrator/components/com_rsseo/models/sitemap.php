<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoModelSitemap extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_RSSEO';
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_rsseo.sitemap', 'sitemap', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		
		$protocol = rsseoHelper::getConfig('sitemapprotocol');
		$protocol = isset($protocol) ? $protocol : 0;
		$port = rsseoHelper::getConfig('sitemapport');
		$port = isset($port) ? $port : 0;
		
		$form->setValue('modified',null,JHtml::_('date','now','Y-m-d'));
		$form->setValue('auto',null,rsseoHelper::getConfig('sitemapauto'));
		$form->setValue('protocol',null,$protocol);
		$form->setValue('port',null,$port);
		
		return $form;
	}
	
	/**
	 *	Method to get the percentage of processed pages
	*/
	public function getPercent() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$config = rsseoHelper::getConfig();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('insitemap').' = 1')
			->where($db->qn('published').' != -1')
			->where($db->qn('canonical').' = '.$db->q(''));
		
		if ($config->exclude_noindex) {
			$query->where($db->qn('robots').' NOT LIKE '.$db->q('%"index":"0"%'));
		}
		
		if ($config->exclude_autocrawled) {
			$query->where($db->qn('level').' <> '.$db->q('127'));
		}
		
		$db->setQuery($query);
		$total = (int) $db->loadResult();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rsseo_pages'))
			->where($db->qn('sitemap').' = 1')
			->where($db->qn('insitemap').' = 1')
			->where($db->qn('published').' != -1')
			->where($db->qn('canonical').' = '.$db->q(''));
		
		if ($config->exclude_noindex) {
			$query->where($db->qn('robots').' NOT LIKE '.$db->q('%"index":"0"%'));
		}
		
		if ($config->exclude_autocrawled) {
			$query->where($db->qn('level').' <> '.$db->q('127'));
		}
		
		$db->setQuery($query);
		$processed = (int) $db->loadResult();
		
		return $total > 0 ? ceil($processed * 100 / $total) : 0;
	}
}