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
<main class="login <?php echo $this->pageclass_sfx; ?>" data-uk-height-viewport="expand: true">
    <div class="uk-padding uk-padding-remove-horizontal">
        <div>
            <div class="uk-container">
                <div>
                    <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-flex-center" data-uk-grid>
                        <div>
                            <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
                                <div>
                                    <div class="uk-padding">
                                        <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                                            <div>
                                                <div class="uk-alert uk-alert-warning uk-border-rounded uk-text-center uk-text-tiny uk-margin-medium-bottom font"><?php echo JTEXT::_('RESTPASSDESC'); ?></div>
                                                <form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="uk-margin-remove regForm form-validate form-horizontal well">
                                                    <div class="uk-child-width-1-1 uk-grid-medium" data-uk-grid>
                                                        <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
                                                            <div>
                                                                <fieldset class="uk-form-stacked uk-margin-remove uk-padding-remove">
                                                                    <?php echo $this->form->renderFieldset($fieldset->name); ?>
                                                                </fieldset>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <div class="control-group">
                                                            <div class="controls">
                                                                <button type="submit" class="uk-button uk-button-success uk-button-large uk-width-1-1 uk-border-rounded uk-box-shadow-small font validate"><?php echo JText::_('JSUBMIT'); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>