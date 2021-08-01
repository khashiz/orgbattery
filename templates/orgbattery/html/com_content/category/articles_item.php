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
    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>" class="uk-card uk-card-default uk-display-block uk-box-shadow-small uk-box-shadow-hover-medium uk-border-rounded uk-overflow-hidden uk-height-1-1 uk-inline-clip uk-transition-toggle hoverAccent uk-text-dark hoverAccent">
        <div class="uk-card-media-top uk-box-shadow-small uk-overflow-hidden"><?php echo JLayoutHelper::render('joomla.content.image_blog_list', $this->item); ?></div>
        <div class="uk-card-body uk-padding-small">
            <div class="uk-padding-small">
                <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font f500"><?php echo JHtml::date($this->item->published_up, 'D ، d M Y') ?></span>
                <?php echo JLayoutHelper::render('joomla.content.blog_title', $this->item); ?>
            </div>
        </div>
    </a>
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
<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate()) || ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate())) : ?></div><?php endif; ?>

<?php /* if (!$params->get('show_intro')) : ?>
    <?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>
<?php echo $this->item->event->afterDisplayContent; */ ?>