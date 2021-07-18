<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>
<?php if (is_array($this->general)) { ?>
<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSSEO_GA_GENERAL'); ?></legend>
	<table class="table table-striped table-bordered">
		<tbody>
		<?php if (!empty($this->general)) { ?>
		<?php foreach ($this->general as $result) { ?>
			<tr class="hasTooltip" title="<?php echo $result->descr; ?>">
				<td style="text-align:right;"><?php echo $result->title; ?></td>
				<td class="key" style="text-align:left;"><?php echo $result->value; ?></td>
			</tr>
		<?php } ?>
		<?php } ?>
		</tbody>
	</table>
</fieldset>
<?php } else echo $this->general; ?>