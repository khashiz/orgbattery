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
    <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font"><?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?></span>
    <span class="uk-text-small uk-text-secondary uk-display-block uk-text-bold font"><?php echo $this->escape($this->data->name); ?></span>
</div>
<div>
    <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font"><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></span>
    <span class="uk-text-small uk-text-secondary uk-display-block uk-text-bold font"><?php echo $this->escape($this->data->username); ?></span>
</div>
<div>
    <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font"><?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?></span>
    <span class="uk-text-small uk-text-secondary uk-display-block uk-text-bold font"><?php echo JHtml::_('date', $this->data->registerDate, JText::_('DATE_FORMAT_LC1')); ?></span>
</div>
<div>
    <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font"><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></span>
    <?php if ($this->data->lastvisitDate != $this->db->getNullDate()) : ?>
        <span class="uk-text-small uk-text-secondary uk-display-block uk-text-bold font"><?php echo JHtml::_('date', $this->data->lastvisitDate, JText::_('DATE_FORMAT_LC1')); ?></span>
    <?php else : ?>
        <span class="uk-text-small uk-text-secondary uk-display-block uk-text-bold font"><?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?></span>
    <?php endif; ?>
</div>