<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.tooltip');


// Load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);

?>
<div>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
        <div>
            <div class="uk-padding">
                <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h4 font"><?php echo JText::sprintf('EDITPROFILE'); ?></h3>
                <div class="profile-edit<?php echo $this->pageclass_sfx; ?>">
                    <script type="text/javascript">
                        Joomla.twoFactorMethodChange = function(e)
                        {
                            var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

                            jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el)
                            {
                                if (el.id != selectedPane)
                                {
                                    jQuery('#' + el.id).hide(0);
                                }
                                else
                                {
                                    jQuery('#' + el.id).show(0);
                                }
                            });
                        }
                    </script>
                    <form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="regForm form-validate form-horizontal well" enctype="multipart/form-data">
                        <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid-medium uk-form-stacked" data-uk-grid>
                        <?php // Iterate through the form fieldsets and display each one. ?>
                        <?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
                            <?php $fields = $this->form->getFieldset($group); ?>
                            <?php if (count($fields)) : ?>


                                <?php foreach ($fields as $field) : ?>
                                    <?php // If the field is hidden, just display the input. ?>
                                    <?php if ($field->hidden) : ?>
                                        <?php echo $field->input; ?>
                                    <?php else : ?>
                                        <div class="control-group <?php if ($field->fieldname == 'type' || $field->fieldname == 'codemelli' || $field->fieldname == 'shenasemelli' || $field->fieldname == 'financialcode') echo 'uk-hidden'; ?>">
                                            <label class="uk-form-label"><?php echo $field->label; ?></label>
                                            <div class="controls">
                                                <?php if ($field->fieldname === 'password1') : ?>
                                                    <?php // Disables autocomplete ?>
                                                    <input type="password" style="display:none">
                                                <?php endif; ?>
                                                <?php echo $field->input; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>


                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (count($this->twofactormethods) > 1) : ?>
                            <fieldset>
                                <legend><?php echo JText::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH'); ?></legend>
                                <div class="control-group">
                                    <div class="control-label">
                                        <label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
                                               title="<?php echo '<strong>' . JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') . '</strong><br />' . JText::_('COM_USERS_PROFILE_TWOFACTOR_DESC'); ?>">
                                            <?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <?php echo JHtml::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false); ?>
                                    </div>
                                </div>
                                <div id="com_users_twofactor_forms_container">
                                    <?php foreach ($this->twofactorform as $form) : ?>
                                        <?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
                                        <div id="com_users_twofactor_<?php echo $form['method']; ?>" style="<?php echo $style; ?>">
                                            <?php echo $form['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>
                                    <?php echo JText::_('COM_USERS_PROFILE_OTEPS'); ?>
                                </legend>
                                <div class="alert alert-info">
                                    <?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
                                </div>
                                <?php if (empty($this->otpConfig->otep)) : ?>
                                    <div class="alert alert-warning">
                                        <?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($this->otpConfig->otep as $otep) : ?>
                                        <span class="span3">
							<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
						</span>
                                    <?php endforeach; ?>
                                    <div class="clearfix"></div>
                                <?php endif; ?>
                            </fieldset>
                        <?php endif; ?>
                            <div class="uk-width-1-1 uk-width-1-3@m">
                                <button type="submit" class="uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button uk-button-success uk-height-1-1 validate"><?php echo JText::_('JSUBMIT'); ?></button>
                            </div>
                            <div class="uk-width-1-1 uk-width-1-6@m">
                                <a class="uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button uk-button-danger uk-height-1-1" href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
                            </div>
                            <input type="hidden" name="option" value="com_users" />
                            <input type="hidden" name="task" value="profile.save" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
