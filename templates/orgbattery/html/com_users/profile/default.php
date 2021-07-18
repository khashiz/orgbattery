<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
        <div>
            <div class="uk-padding">
                <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h4 font"><?php echo $this->escape($this->params->get('page_heading')); ?></h3>
                <div class="uk-margin-medium-bottom profile<?php echo $this->pageclass_sfx; ?>">
                    <div class="uk-child-width-1-1 uk-child-width-1-4@m uk-grid-divider uk-text-zero" data-uk-grid>
                        <?php echo $this->loadTemplate('core'); ?>
                        <?php echo $this->loadTemplate('params'); ?>
                        <?php echo $this->loadTemplate('custom'); ?>
                    </div>
                </div>
                <?php if (JFactory::getUser()->id == $this->data->id) : ?>
                    <div>
                        <a class="uk-button uk-button-success uk-border-rounded uk-box-shadow-small uk-width-1-1 uk-width-auto@m font" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>"><?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>