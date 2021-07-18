<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>

<button class="btn btn-danger" type="button" onclick="if (confirm('<?php echo JText::_('COM_RSSEO_DELETE_LOG_CONFIRM',true); ?>')) Joomla.submitbutton('gkeywords.deletelog')">
	<?php echo JText::_('COM_RSSEO_DELETE_LOG'); ?>
</button>