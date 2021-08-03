<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class plgContentRsformInstallerScript
{
	protected static $minJoomla = '3.7.0';
	protected static $minComponent = '3.0.0';
	
	public function preflight($type, $parent)
	{
		if ($type == 'uninstall')
		{
			return true;
		}

		try
		{
			$jversion = new JVersion();

			if (!$jversion->isCompatible(static::$minJoomla))
			{
				throw new Exception('Please upgrade to at least Joomla! ' . static::$minJoomla . ' before continuing!');
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php'))
			{
				throw new Exception('Please install the RSForm! Pro component before continuing.');
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php') || !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php'))
			{
				throw new Exception('Please upgrade RSForm! Pro to at least version ' . static::$minComponent . ' before continuing!');
			}

			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';

			if (!class_exists('RSFormProVersion') || version_compare((string) new RSFormProVersion, static::$minComponent, '<'))
			{
				throw new Exception('Please upgrade RSForm! Pro to at least version ' . static::$minComponent . ' before continuing!');
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}
	
	public function postflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id')
			  ->from($db->qn('#__extensions'))
			  ->where($db->qn('type').' = '.$db->q('plugin'))
			  ->where($db->qn('folder').' = '.$db->q('content'))
			  ->where($db->qn('element').' = '.$db->q('rsform'));
		$pluginId = $db->setQuery($query)->loadResult();
		?>
		<style type="text/css">
		.version-history {
			margin: 0 0 2em 0;
			padding: 0;
			list-style-type: none;
		}
		.version-history > li {
			margin: 0 0 0.5em 0;
			padding: 0 0 0 4em;
			text-align:left;
			font-weight:normal;
		}
		.version-new,
		.version-fixed,
		.version-upgraded {
			float: left;
			font-size: 0.8em;
			margin-left: -4.9em;
			width: 4.5em;
			color: white;
			text-align: center;
			font-weight: bold;
			text-transform: uppercase;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
		}

		.version-new {
			background: #7dc35b;
		}
		.version-fixed {
			background: #e9a130;
		}
		.version-upgraded {
			background: #61b3de;
		}
		</style>

		<h3>RSForm! Pro Content Plugin v3.0.0 Changelog</h3>
		<ul class="version-history">
			<li><span class="version-upgraded">Upg</span> Joomla! 4.0 and RSForm! Pro 3.0 compatibility.</li>
		</ul>
		<?php if ($pluginId) { ?>
		<a class="btn btn-primary btn-large" href="<?php echo JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id='.$pluginId); ?>">Start using the RSForm! Pro Content Plugin.</a>
		<?php } ?>
		<a class="btn" href="https://www.rsjoomla.com/support/documentation/rsform-pro/plugins-and-modules/content-plugin-plgcontent-display-the-form-in-an-article.html" target="_blank">Read the documentation</a>
		<a class="btn btn-secondary" href="https://www.rsjoomla.com/support.html" target="_blank">Get Support!</a>
		<div style="clear: both;"></div>
		<?php
	}
}