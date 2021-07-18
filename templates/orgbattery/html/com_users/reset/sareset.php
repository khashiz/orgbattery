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



<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small login <?php echo $this->pageclass_sfx; ?>">
    <div>
        <div class="uk-padding">
            <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                <div>
                    <div class="uk-alert uk-alert-warning uk-border-rounded uk-text-center uk-text-tiny uk-margin-medium-bottom font"><?php echo JTEXT::_('RESTPASSDESC'); ?></div>
                    <form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.request'); ?>" method="post" class="uk-margin-remove regForm form-validate form-horizontal well">
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