<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>
<?php foreach ($this->items as $i => $item) { ?>
	<tr>
		<td class="center small hidden-phone"><?php echo JHTML::_('grid.id', $i, $item->session_id); ?></td>
		<td><?php echo JHtml::_('date', $item->date, $this->config->global_dateformat); ?></td>
		<td><?php echo rsseoHelper::obfuscateIP($item->ip); ?></td>
		<td><a href="<?php echo JUri::root().rsseoHelper::getSef($item->page); ?>" target="_blank"><?php echo empty($item->page) ? JUri::root() : rsseoHelper::getSef($item->page); ?></a></td>
		<td><a href="javascript:void(0);" onclick="RSSeo.showModal('<?php echo JRoute::_('index.php?option=com_rsseo&view=statistics&layout=pageviews&tmpl=component&id='.$item->id,false); ?>')"><?php echo JText::_('COM_RSSEO_VIEW_PAGEVIEWS'); ?></a></td>
	</tr>
<?php } ?>