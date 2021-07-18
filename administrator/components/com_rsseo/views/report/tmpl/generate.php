<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>

<style type="text/css">
.table {
	width: 100%;
	margin-bottom: 18px;
	border-spacing: 0;
	border-collapse: collapse;
}
.table th,
.table td {
	padding: 8px;
	line-height: 18px;
	text-align: left;
	vertical-align: top;
	border-top: 1px solid #ddd;
}
.table th {
	font-weight: bold;
}
.table thead th {
	vertical-align: bottom;
}
.table td.center,
.table thead th.center {
	text-align: center;
}
</style>

<center><h1><?php echo JText::sprintf('COM_RSSEO_REPORT_GENERATED', JHtml::_('date', 'now', rsseoHelper::getConfig('global_dateformat'))); ?></h1></center>

<?php if ($this->data->statistics && !empty($this->statistics)) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_SITE_STATISTICS'); ?></h2>
<table class="table">
<?php foreach ($this->statistics as $type => $value) { ?>
<?php if ($type == 'date' || $type == 'id' || $type == 'gplus') continue; ?>
	<tr>
		<td width="80%"><?php echo JText::_('COM_RSSEO_STATISTICS_'.strtoupper($type)); ?></td>
		<td class="center">
			<?php if ($type == 'age') {
				echo $value == -1 ? JText::_('COM_RSSEO_NA') : rsseoHelper::convertage($value);
			} else { ?>
			<?php echo $value == -1 ? JText::_('COM_RSSEO_NA') : $value; ?>
			<?php } ?>
		</td>
	</tr>
<?php } ?>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->last_crawled && !empty($this->lcrawled)) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_LAST_CRAWLED'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_REPORT_PAGE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_DATE'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->lcrawled as $page) { ?>
	<?php $url = rsseoHelper::showURL($page->url, $page->sef); ?>
	<?php $pageurl = ($page->id == 1) ? JURI::root() : $url; ?>
		<tr>
			<td width="70%" style="word-break:break-all; word-wrap:break-word;"><?php echo $pageurl; ?></td>
			<td class="center"><?php echo JHtml::_('date', $page->date, rsseoHelper::getConfig('global_dateformat')); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->most_visited && $this->mvisited) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_MOST_VISITED'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_REPORT_PAGE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_HITS'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->mvisited as $page) { ?>
	<?php $url = rsseoHelper::showURL($page->url, $page->sef); ?>
	<?php $pageurl = ($page->id == 1) ? JURI::root() : $url; ?>
		<tr>
			<td width="70%" style="word-break:break-all; word-wrap:break-word;"><?php echo $pageurl; ?></td>
			<td class="center"><?php echo $page->hits; ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->no_title && $this->notitle) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_NO_TITLE'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_REPORT_PAGE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_DATE'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->notitle as $page) { ?>
	<?php $url = rsseoHelper::showURL($page->url, $page->sef); ?>
	<?php $pageurl = ($page->id == 1) ? JURI::root() : $url; ?>
		<tr>
			<td width="70%" style="word-break:break-all; word-wrap:break-word;"><?php echo $pageurl; ?></td>
			<td class="center"><?php echo JHtml::_('date', $page->date, rsseoHelper::getConfig('global_dateformat')); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->no_desc && $this->nodesc) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_NO_DESC'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_REPORT_PAGE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_DATE'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->nodesc as $page) { ?>
	<?php $url = rsseoHelper::showURL($page->url, $page->sef); ?>
	<?php $pageurl = ($page->id == 1) ? JURI::root() : $url; ?>
		<tr>
			<td width="70%" style="word-break:break-all; word-wrap:break-word;"><?php echo $pageurl; ?></td>
			<td class="center"><?php echo JHtml::_('date', $page->date, rsseoHelper::getConfig('global_dateformat')); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->error_links && $this->elinks) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_ERROR_LINKS'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_REPORT_PAGE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_CODE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_COUNT'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->elinks as $page) { ?>
	<tr>
		<td width="70%" style="word-break:break-all; word-wrap:break-word;"><?php echo $page->url; ?></td>
		<td class="center"><?php echo $page->code; ?></td>
		<td class="center"><?php echo $page->count; ?></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->enable_competitors && !empty($this->data->competitors) && !empty($this->competitors)) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_COMPETITORS'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_COMPETITORS_COMPETITOR'); ?></th>
			<?php if ($this->config->enable_age) { ?><th class="center" width="6%"><?php echo JText::_('COM_RSSEO_COMPETITORS_DOMAIN_AGE'); ?></th><?php } ?>
			<?php if ($this->config->enable_bingp) { ?><th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_BING_PAGES'); ?></th><?php } ?>
			<?php if ($this->config->enable_bingb) { ?><th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_BING_BACKLINKS'); ?></th><?php } ?>
			<?php if ($this->config->enable_alexa) { ?><th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_ALEXA_RANK'); ?></th><?php } ?>
			<?php if ($this->config->enable_moz) { ?>
			<th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_MOZ_RANK'); ?></th>
			<th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_MOZ_PA'); ?></th>
			<th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_MOZ_DA'); ?></th>
			<?php } ?>
			<th class="center" width="6%"><?php echo JText::_( 'COM_RSSEO_COMPETITORS_DATE'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->competitors as $competitor) { ?>
		<tr>
			<td style="word-break:break-all; word-wrap:break-word;"><?php echo $competitor->name; ?></td>
			<?php if ($this->config->enable_age) { ?><td class="center"><?php echo (int) $competitor->age <= 0 ? '-' : rsseoHelper::convertage($competitor->age); ?></td><?php } ?>
			<?php if ($this->config->enable_bingp) { ?><td class="center"><?php echo $competitor->bingp; ?></td><?php } ?>
			<?php if ($this->config->enable_bingb) { ?><td class="center"><?php echo $competitor->bingb; ?></td><?php } ?>
			<?php if ($this->config->enable_alexa) { ?><td class="center"><?php echo $competitor->alexa; ?></td><?php } ?>
			<?php if ($this->config->enable_moz) { ?>
			<td class="center"><?php echo $competitor->mozpagerank; ?></td>
			<td class="center"><?php echo $competitor->mozpa; ?></td>
			<td class="center"><?php echo $competitor->mozda; ?></td>
			<?php } ?>
			<td class="center"><?php echo JHtml::_('date', $competitor->date, $this->config->global_dateformat); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<div style="page-break-after: always;"></div>
<?php } ?>

<?php if ($this->data->enable_gkeywords && !empty($this->data->keywords) && !empty($this->keywords)) { ?>
<h2><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORDS'); ?></h2>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORD'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORD_PAGES'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORD_IMPRESSIONS'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORD_CLICKS'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORD_AVGPOSITION'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSSEO_REPORT_GKEYWORD_CTR'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->keywords as $keyword) { ?>
		<tr>
			<td style="word-break:break-all; word-wrap:break-word;"><?php echo $keyword->name; ?> (<?php echo $keyword->site; ?>)</td>
			<td class="center"><?php echo $keyword->pages; ?></td>
			<td class="center"><?php echo $keyword->impressions; ?></td>
			<td class="center"><?php echo $keyword->clicks; ?></td>
			<td class="center"><?php echo $keyword->avg; ?></td>
			<td class="center"><?php echo $keyword->ctr; ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php } ?>