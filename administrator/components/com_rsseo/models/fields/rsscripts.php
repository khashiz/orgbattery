<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/
defined('JPATH_PLATFORM') or die;

class JFormFieldRsscripts extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Rsscripts';

	public function __construct() {
		$js = array();
		
		$js[] = "function rsseo_sitemap() {\n";
		$js[] = "\t document.getElementById('sitemapToken').innerHTML = document.getElementById('jform_sitemap_cron_security').value;\n";
		$js[] = "\t document.getElementById('siteAddress').innerHTML = '".addslashes(JURI::root())."';\n";
		$js[] = "}\n";
		$js[] = "\n";
		$js[] = "document.addEventListener('DOMContentLoaded', function() { rsseo_sitemap(); });";
		
		
		JFactory::getDocument()->addScriptDeclaration(implode('',$js));
	}
	
	protected function getInput() {}
}