<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); 
$url = rsseoHelper::showURL($this->item->url, $this->item->sef); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsseo&view=page&layout=details&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSSeoAdapterGrid::row(); ?>">
		<div class="<?php echo RSSeoAdapterGrid::column(12); ?>">
			<strong><?php echo JText::_('COM_RSSEO_GLOBAL_URL'); ?></strong>: <a href="<?php echo JURI::root().$url; ?>" target="_blank"><?php echo JURI::root().$url; ?></a>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%">#</th>
						<th><?php echo JText::_('COM_RSSEO_PAGE_ELEMENT'); ?></th>
						<th class="center text-center"><?php echo JText::_('COM_RSSEO_PAGE_ELEMENT_FILESIZE'); ?></th>
						<th class="center text-center"><?php echo JText::_('COM_RSSEO_PAGE_ELEMENT_FREQUENCY'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($this->details['pages'])) { ?>
					<?php $i = 1; ?>
					<?php foreach ($this->details['pages'] as $page) { ?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $page->url; ?></td>
						<td class="center text-center"><?php echo $page->size; ?></td>
						<td class="center text-center"><?php echo $page->freq; ?></td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
					<tr>
						<td colspan="2"><strong><?php echo JText::_('COM_RSSEO_GLOBAL_TOTAL'); ?></strong></td>
						<td colspan="2"><strong><?php echo $this->details['total']; ?></strong></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
</form>