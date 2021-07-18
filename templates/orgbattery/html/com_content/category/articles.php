<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.caption');

$dispatcher = JEventDispatcher::getInstance();

$this->category->text = $this->category->description;
$dispatcher->trigger('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $dispatcher->trigger('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $dispatcher->trigger('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $dispatcher->trigger('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));


$app  = JFactory::getApplication();
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();
$pageparams = $menu->getParams( $active->id );
$pageclass = $pageparams->get( 'pageclass_sfx' );

?>
<div class="pageWrapper">
    <div itemscope itemtype="https://schema.org/Blog">
        <div class="uk-card uk-card-default uk-box-shadow-small uk-border-rounded">
            <div class="uk-padding">
                <?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
                    <h2> <?php echo $this->escape($this->params->get('page_subheading')); ?>
                        <?php if ($this->params->get('show_category_title')) : ?>
                            <span class="subheading-category"><?php echo $this->category->title; ?></span>
                        <?php endif; ?>
                    </h2>
                <?php endif; ?>
                <?php echo $afterDisplayTitle; ?>

                <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
                    <?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
                    <?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
                <?php endif; ?>

                <?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
                    <div class="category-desc">
                        <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
                            <img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($this->category->getParams()->get('image_alt'), ENT_COMPAT, 'UTF-8'); ?>"/>
                        <?php endif; ?>
                        <?php echo $beforeDisplayContent; ?>
                        <?php if ($this->params->get('show_description') && $this->category->description) : ?>
                            <?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
                        <?php endif; ?>
                        <?php echo $afterDisplayContent; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
                    <?php if ($this->params->get('show_no_articles', 1)) : ?>
                        <p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($this->lead_items)) : ?>
                    <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                        <?php foreach ($this->lead_items as &$item) : ?>
                            <div class="blogItem uk-text-zero" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                                <?php $this->item = &$item; echo $this->loadTemplate('item'); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>


                <?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
                    <div class="cat-children">
                        <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
                            <h3> <?php echo JText::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
                        <?php endif; ?>
                        <?php echo $this->loadTemplate('children'); ?>
                    </div>
                <?php endif; ?>

                <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
                    <div class="pagination uk-margin-medium-top">
                        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                            <p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?></p>
                        <?php endif; ?>
                        <?php echo $this->pagination->getPagesLinks(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>