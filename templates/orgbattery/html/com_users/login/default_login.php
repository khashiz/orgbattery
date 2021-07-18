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

?>
<div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
    <div>
        <div class="uk-padding">
            <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                <div>
                    <?php if ($this->params->get('logindescription_show') == 1) : ?>
                        <div class="uk-alert uk-alert-warning uk-border-rounded uk-text-center uk-text-tiny uk-margin-medium-bottom font"><?php echo $this->params->get('login_description'); ?></div>
                    <?php endif; ?>
                    <form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well uk-margin-remove regForm">
                        <fieldset class="uk-form-stacked uk-margin-remove uk-padding-remove">
                            <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid-medium" data-uk-grid>
                                <?php echo $this->form->renderFieldset('credentials'); ?>
                                <?php if ($this->tfa) : ?>
                                    <?php echo $this->form->renderField('secretkey'); ?>
                                <?php endif; ?>
                                <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                                    <div class="control-group uk-hidden">
                                        <div class="control-label">
                                            <label for="remember">
                                                <?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>
                                            </label>
                                        </div>
                                        <div class="controls">
                                            <input id="remember" type="checkbox" name="remember" class="inputbox" value="yes" checked />
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="uk-width-1-1">
                                    <button type="submit" class="uk-button uk-button-success uk-button-large uk-width-1-1 uk-border-rounded uk-box-shadow-small font"><?php echo JText::_('JLOGIN'); ?></button>
                                </div>
                            </div>
                            <?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
                            <input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
                            <?php echo JHtml::_('form.token'); ?>
                        </fieldset>
                    </form>
                </div>
                <div>
                    <ul class="uk-grid-medium uk-grid-divider uk-flex-center uk-text-zero" data-uk-grid>
                        <li>
                            <a class="uk-flex uk-flex-middle uk-text-gray hoverAccent" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
                                <span class="uk-margin-small-left"><img src="<?php echo JURI::base().'images/sprite.svg#lock' ?>" width="16" height="16" data-uk-svg></span>
                                <span class="uk-text-tiny font"><?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></span>
                            </a>
                        </li>
                        <?php /* ?>
                                                    <li>
                                                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
                                                    </li>
                                                    <?php */ ?>
                        <?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
                        <?php if ($usersConfig->get('allowUserRegistration')) : ?>
                            <li>
                                <a class="uk-flex uk-flex-middle uk-text-gray hoverAccent" href="<?php echo JRoute::_("index.php?Itemid=165"); ?>">
                                    <span class="uk-margin-small-left"><img src="<?php echo JURI::base().'images/sprite.svg#user' ?>" width="16" height="16" data-uk-svg></span>
                                    <span class="uk-text-tiny font"><?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>