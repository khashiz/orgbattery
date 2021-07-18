<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="post" class="regForm">
	<div class="btn-toolbar uk-grid-small uk-margin-bottom" data-uk-grid>
		<div class="btn-group pull-left uk-width-1-1 uk-width-expand@m">
			<input type="text" name="searchword" title="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="uk-input uk-width-1-1 uk-border-rounded font inputbox" />
		</div>
		<div class="btn-group pull-left uk-width-1-1 uk-width-small@m">
			<button name="Search" onclick="this.form.submit()" class="uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button uk-button-success uk-height-1-1" title="<?php echo JHtml::_('tooltipText', 'COM_SEARCH_SEARCH');?>"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		</div>
		<input type="hidden" name="task" value="search" />
	</div>
    <?php if (!empty($this->searchword)) : ?>
        <div class="uk-alert uk-alert-<?php echo $this->total == 0 ? 'danger' : 'success'; ?> uk-text-center uk-border-rounded uk-margin-remove uk-padding-small searchintro <?php echo $this->params->get('pageclass_sfx'); ?>">
            <p class="uk-margin-remove uk-text-tiny uk-text-bold font">
                <?php if ($this->total == 0) { ?>
                    <?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_NO_RESULTS', $this->total); ?>
                <?php } else { ?>
                    <?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total); ?>
                <?php } ?>
            </p>
        </div>
    <?php endif; ?>
	<?php if ($this->params->get('search_phrases', 1)) : ?>
		<fieldset class="phrases">
			<legend>
				<?php echo JText::_('COM_SEARCH_FOR'); ?>
			</legend>
			<div class="phrases-box">
				<?php echo $this->lists['searchphrase']; ?>
			</div>
			<div class="ordering-box">
				<label for="ordering" class="ordering">
					<?php echo JText::_('COM_SEARCH_ORDERING'); ?>
				</label>
				<?php echo $this->lists['ordering']; ?>
			</div>
		</fieldset>
	<?php endif; ?>
	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only">
			<legend>
				<?php echo JText::_('COM_SEARCH_SEARCH_ONLY'); ?>
			</legend>
			<?php foreach ($this->searchareas['search'] as $val => $txt) : ?>
				<?php $checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : ''; ?>
				<label for="area-<?php echo $val; ?>" class="checkbox">
					<input type="checkbox" name="areas[]" value="<?php echo $val; ?>" id="area-<?php echo $val; ?>" <?php echo $checked; ?> />
					<?php echo JText::_($txt); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>
	<?php if ($this->total > 0) : ?>
		<div class="form-limit uk-hidden">
			<label for="limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<p class="counter uk-hidden"><?php echo $this->pagination->getPagesCounter(); ?></p>
	<?php endif; ?>
</form>
