<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Create a shortcut for params.
$params = $this->item->params;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));

?>

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate()) || ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate())) : ?><div class="system-unpublished"><?php endif; ?>
<div class="uk-text-zero" data-uk-grid>
    <div class="uk-width-1-1 uk-width-1-3@m">
        <div class="uk-border-rounded uk-overflow-hidden uk-box-shadow-small blogItemBody"><?php echo JLayoutHelper::render('joomla.content.image43', $this->item); ?></div>
    </div>
    <div class="uk-width-1-1 uk-width-2-3@m">
        <div class="uk-height-1-1 uk-flex uk-flex-middle">
            <div>
                <div class="blogItemHeading uk-margin-small-bottom">
                    <div class="meta uk-margin-small-bottom"><?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?></div>
                    <?php echo JLayoutHelper::render('joomla.content.blog_title', $this->item); ?>
                </div>
                <div class="blogItemBody">
                    <div class="font uk-text-justify"><?php echo $this->item->introtext; ?></div>
                    <?php if ($params->get('show_readmore') && $this->item->readmore) :
                        if ($params->get('access-view')) :
                            $link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
                        else :
                            $menu = JFactory::getApplication()->getMenu();
                            $active = $menu->getActive();
                            $itemId = $active->id;
                            $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
                            $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
                        endif; ?>
                        <?php echo JLayoutHelper::render('joomla.content.sa_blog_readmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

            <?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
                <?php // echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
            <?php endif; ?>

            <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
                || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam); ?>



    </div>
</div>
<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate()) || ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate())) : ?></div><?php endif; ?>

<?php /* if (!$params->get('show_intro')) : ?>
    <?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>
<?php echo $this->item->event->afterDisplayContent; */ ?>