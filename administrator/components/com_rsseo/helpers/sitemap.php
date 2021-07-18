<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

class sitemapHelper {
	
	protected $sitemap;
	protected $ror;
	protected $new;
	protected $protocol;
	protected $modified;
	protected $auto;
	protected $replace = array();
	protected $root;
	protected $port;
	
	public function __construct($options) {
		// The sitemap.xml path
		$this->sitemap = JPATH_SITE.'/sitemap.xml';
		// The ror.xml path
		$this->ror = JPATH_SITE.'/ror.xml';
		// Do we create a new sitemap ?
		$this->new = isset($options['new']) ? $options['new'] : 0;
		// Set protocol
		$this->protocol = isset($options['protocol']) ? $options['protocol'] : 1;
		// Set last modified time
		$this->modified = isset($options['modified']) ? $options['modified'] : JHtml::_('date', 'NOW', 'Y-m-d');
		// Set auto-crawled
		$this->auto = isset($options['auto']) ? $options['auto'] : 0;
		// Show port
		$this->port = isset($options['port']) ? $options['port'] : 0;
		// Set root
		$this->root = $this->getRoot();
		
		if (substr($this->root,0,8) == 'https://' && $this->protocol == 0) {
			$this->root = str_replace('https://','http://',$this->root);
		}
		
		if (substr($this->root,0,7) == 'http://' && $this->protocol == 1) {
			$this->root = str_replace('http://','https://',$this->root);
		}
		
		$this->update();
		
		$empty = '';
		if (file_exists($this->sitemap) && $this->new) {
			$this->write($this->sitemap,$empty,'w');
		}
		
		if (file_exists($this->ror) && $this->new) {
			$this->write($this->ror,$empty,'w');
		}
		
		// Reset file 
		$this->reset();
		
		$this->redirects();
	}
	
	public static function getInstance($options) {
		$modelClass = 'sitemapHelper';
		return new $modelClass($options);
	}
	
	/**
	 *	Add XML Headers
	 */
	public function setHeader($update = false) {
		if ($this->new || $update) {
			if (JFile::exists($this->sitemap)) {
				$header = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
				$header .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
				
				$this->write($this->sitemap,$header,'a');
			}
			
			if (JFile::exists($this->ror)) {
				$header = '<?xml version="1.0" encoding="utf-8"?>'."\n";
				$header .= '<rss version="2.0" xmlns:ror="http://rorweb.com/0.1/">'."\n";
				$header .= '<channel>'."\n";
				$header .= "\t".'<title>ROR Sitemap for '.$this->root.'</title>'."\n";
				$header .= "\t".'<description>ROR Sitemap for '.$this->root.'</description>'."\n";
				$header .= "\t".'<link>'.$this->root.'</link>'."\n";
				$header .= "\t".'<item>'."\n";
				$header .= "\t\t".'<title>ROR Sitemap for '.$this->root.'</title>'."\n";
				$header .= "\t\t".'<link>'.$this->root.'</link>'."\n";
				$header .= "\t\t".'<ror:about>sitemap</ror:about>'."\n";
				$header .= "\t\t".'<ror:type>SiteMap</ror:type>'."\n";
				$header .= "\t".'</item>'."\n";
				
				$this->write($this->ror,$header,'a');
			}
		}
	}
	
	protected function update() {
		if ($this->new) {
			$db			= JFactory::getDbo();
			$query		= $db->getQuery(true);
			$component	= JComponentHelper::getComponent('com_rsseo');
			$cparams	= $component->params;
			
			if ($cparams instanceof JRegistry) {
				$cparams->set('sitemapauto', $this->auto);
				$cparams->set('sitemapprotocol', $this->protocol);
				$cparams->set('sitemapport', $this->port);
				$query->clear();
				$query->update($db->qn('#__extensions'));
				$query->set($db->qn('params'). ' = '.$db->q((string) $cparams));
				$query->where($db->qn('extension_id'). ' = '. $db->q($component->id));
				
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	public function add($page, $update = false) {
		if (JFile::exists($this->sitemap)) {
			$this->addSitemap($page, $update);
		}
		
		if (JFile::exists($this->ror)) {
			$this->addRor($page, $update);
		}
	}
	
	public function close() {
		if (JFile::exists($this->sitemap)) {
			$this->closeSitemap();
		}
		
		if (JFile::exists($this->ror)) {
			$this->closeRor();
		}
	}
	
	public function clear() {
		if (JFile::exists($this->sitemap)) {
			$fh = fopen($this->sitemap,'w');
			fclose($fh);
		}
		
		if (JFile::exists($this->ror)) {
			$fh = fopen($this->ror,'w');
			fclose($fh);
		}
	}
	
	protected function addSitemap($page, $update) {
		if (!empty($this->replace[$page->url])) {
			$page->url = $this->replace[$page->url];
		}
		
		if (strpos($page->url,$this->root) === false) {
			$href = $this->root.$page->url;
		} else {
			$href = $page->url;
		}
		
		$string = "\t".'<url>'."\n";
		$string .= "\t\t".'<loc>'.$this->xmlentities($href).'</loc>'."\n";
		$string .= "\t\t".'<priority>'.($page->priority ? $page->priority : '0.5').'</priority>'."\n";
		$string .= "\t\t".'<changefreq>'.($page->frequency ? $page->frequency : 'weekly').'</changefreq>'."\n";
		$string .= "\t\t".'<lastmod>'.$this->modified.'</lastmod>'."\n";
		$string .= "\t".'</url>'."\n";
		
		$this->write($this->sitemap, $string, 'a', $update, 'sitemap');
	}
	
	protected function addRor($page, $update) {
		if (!empty($this->replace[$page->url])) {
			$page->url = $this->replace[$page->url];
		}
		
		if (strpos($page->url,$this->root) === false) {
			$href = $this->root.$page->url;
		} else {
			$href = $page->url;
		}
		
		$string = "\t".'<item>'."\n";
		$string .= "\t\t".'<link>'.$this->xmlentities($href).'</link>'."\n";
		$string .= "\t\t".'<title>'.$this->xmlentities($page->title).'</title>'."\n";
		$string .= "\t\t".'<ror:updatePeriod>'.($page->frequency ? $page->frequency : 'weekly').'</ror:updatePeriod>'."\n";
		$string .= "\t\t".'<ror:sortOrder>'.$page->level.'</ror:sortOrder>'."\n";
		$string .= "\t\t".'<ror:resourceOf>sitemap</ror:resourceOf>'."\n";
		$string .= "\t".'</item>'."\n";
		
		$this->write($this->ror, $string, 'a', $update, 'ror');
	}
	
	protected function closeSitemap() {
		$string = '</urlset>';
		$this->write($this->sitemap, $string, 'a');
	}
	
	protected function closeRor() {
		$string = '</channel>'."\n";
		$string .= '</rss>';
		
		$this->write($this->ror, $string, 'a');
	}
	
	protected function redirects() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('from'))->select($db->qn('to'))
			->from($db->qn('#__rsseo_redirects'))
			->where($db->qn('published').' = '.$db->q(1));
			
		$db->setQuery($query);
		if ($redirects = $db->loadObjectList()) {
			foreach ($redirects as $redirect) {
				$redirect->from = htmlentities($redirect->from);
				$redirect->to = htmlentities($redirect->to);
				$this->replace[$redirect->from] = $redirect->to;
			}
		}
	}
	
	protected function reset() {
		if ($this->new) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->update($db->qn('#__rsseo_pages'))->set($db->qn('sitemap').' = 0');
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	protected function xmlentities($string) {
		$string = str_replace('&amp;','&',$string);
		$string = htmlspecialchars($string);
		
		return $string;
	}
	
	protected function write($filename, $string, $write_type, $update = false, $type = null) {
		$write_type = $update ? 'r+' : $write_type;
		
		if (!is_null($type) && $update) {
			if ($type == 'sitemap')
				$string = $string."</urlset>";
			elseif ($type == 'ror') {
				$string = $string."</channel>\n</rss>";
			}
		}
		
		if (is_writable($filename)) {
			if (!$handle = fopen($filename, $write_type)) {
				throw new Exception(JText::sprintf('COM_RSSEO_SITEMAP_CANNOT_OPEN_FILE', $filename));
			}
			if ($update) {
				if (!is_null($type)) {
					if ($type == 'sitemap') {
						fseek($handle, -9, SEEK_END);
					} elseif ($type == 'ror') {
						fseek($handle, -17, SEEK_END);
					}
				}
			}
			// Write $somecontent to our opened file.
			if (fwrite($handle, $string) === FALSE) {
				throw new Exception(JText::sprintf('COM_RSSEO_SITEMAP_CANNOT_OPEN_FILE', $filename));
			}
			fclose($handle);
		} else {
			throw new Exception(JText::sprintf('COM_RSSEO_SITEMAP_CANNOT_WRITE_FILE', $filename));
		}
	}
	
	protected function getRoot() {
		$uri	 = JUri::getInstance(JUri::base());
		$options = $this->port ? array('scheme', 'host') : array('scheme', 'host', 'port');
		$prefix  = $uri->toString($options);
		$path	 = $uri->toString(array('path'));
		$path	 = strpos($path, 'administrator') !== false ? rtrim($path, '/\\') : $path;
		
		return str_replace('administrator', '', $prefix.$path);
	}
}