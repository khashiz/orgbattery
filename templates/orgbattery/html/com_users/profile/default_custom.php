<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::register('users.spacer', array('JHtmlUsers', 'spacer'));

$fieldsets = $this->form->getFieldsets();

if (isset($fieldsets['core']))
{
	unset($fieldsets['core']);
}

if (isset($fieldsets['params']))
{
	unset($fieldsets['params']);
}

$tmp          = isset($this->data->jcfields) ? $this->data->jcfields : array();
$customFields = array();

foreach ($tmp as $customField)
{
	$customFields[$customField->name] = $customField;
}

$user = JFactory::getUser();
?>
<?php foreach ($fieldsets as $group => $fieldset) : ?>
	<?php $fields = $this->form->getFieldset($group); ?>
	<?php if (count($fields)) : ?>
        <?php foreach ($fields as $field) : ?>
        <div class="<?php if (in_array(11, $user->groups) &&  ($field->fieldname == 'shenasemelli' || $field->fieldname == 'financialcode')) echo ' uk-hidden'; ?><?php if (in_array(10, $user->groups) &&  $field->fieldname == 'codemelli') echo ' uk-hidden'; ?>">
            <?php if (!$field->hidden && $field->type !== 'Spacer') : ?>
                <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font"><?php echo $field->title; ?></span>
                <span class="uk-text-small uk-text-secondary uk-display-block uk-text-bold font">
                    <?php if (key_exists($field->fieldname, $customFields)) : ?>
                        <?php echo strlen($customFields[$field->fieldname]->value) ? $customFields[$field->fieldname]->value : JText::_('COM_USERS_PROFILE_VALUE_NOT_FOUND'); ?>
                    <?php elseif (JHtml::isRegistered('users.' . $field->id)) : ?>
                        <?php echo JHtml::_('users.' . $field->id, $field->value); ?>
                    <?php elseif (JHtml::isRegistered('users.' . $field->fieldname)) : ?>
                        <?php echo JHtml::_('users.' . $field->fieldname, $field->value); ?>
                    <?php elseif (JHtml::isRegistered('users.' . $field->type)) : ?>
                        <?php echo JHtml::_('users.' . $field->type, $field->value); ?>
                    <?php else : ?>
                        <?php echo JHtml::_('users.value', $field->value); ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
	<?php endif; ?>
<?php endforeach; ?>