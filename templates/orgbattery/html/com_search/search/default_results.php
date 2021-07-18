<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="uk-list uk-list-striped uk-text-zero search-results<?php echo $this->pageclass_sfx; ?>">
    <?php foreach ($this->results as $result) : ?>
        <div>
            <div data-uk-grid>
                <div class="uk-width-expand">
                    <?php if ($result->href) : ?>
                        <a class="uk-text-bold hoverAccent uk-text-small font" href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) : ?> target="_blank"<?php endif; ?>><?php echo $result->title; ?></a>
                    <?php else : ?>
                        <?php echo $result->title; ?>
                    <?php endif; ?>
                </div>
                <?php if ($this->params->get('show_date')) : ?>
                    <div>
                        <span class="uk-label uk-label-success uk-text-tiny result-created"><?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($result->section) : ?>
                    <div class="uk-width-auto">
                        <span class="uk-label uk-label-warning uk-text-tiny font result-category"><?php echo $this->escape($result->section); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>