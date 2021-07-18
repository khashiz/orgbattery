<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');


$lang   = JFactory::getLanguage();
$user   = JFactory::getUser();
$groups = $user->getAuthorisedViewLevels();

if ($this->maxLevel != 0 && count($this->children[$this->category->id]) > 0) : ?>
<div class="uk-grid-collapse uk-flex uk-flex-wrap uk-child-width-1-1 uk-child-width-1-4@m productGridWrapper">
	<?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
		<?php // Check whether category access level allows access to subcategories. ?>
		<?php if (in_array($child->access, $groups)) : ?>
			<?php if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
				if (!isset($this->children[$this->category->id][$id + 1])) :
					$class = ' class="last"';
				endif;
			?>
			<div class="gridItem">
                <div class="hikashop_container uk-margin-remove uk-position-relative uk-text-center uk-padding-small">
                    <div>
                        <a class="uk-display-block uk-text-secondary uk-padding-small" href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)); ?>" title="<?php echo $this->escape($child->title); ?>">
                            <img src="<?php echo JURI::base().'images/sprite.svg#'.$child->alias; ?>" width="64" height="64" alt="<?php echo $this->escape($child->title); ?>" data-uk-svg>
                        </a>
                    </div>
                    <?php if ($lang->isRtl()) : ?>
                        <h3 class="page-header item-title uk-margin-remove">
                            <?php if ( $this->params->get('show_cat_num_articles', 1)) : ?>
                                <span class="badge badge-info tip hasTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_CONTENT_NUM_ITEMS_TIP'); ?>"><?php echo $child->getNumItems(true); ?></span>
                            <?php endif; ?>
                            <a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)); ?>" class="uk-display-block uk-padding-small  uk-text-small uk-text-bold uk-text-ddark hoverAccent font"><?php echo $this->escape($child->title); ?></a>

                            <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                                <a href="#category-<?php echo $child->id; ?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right" aria-label="<?php echo JText::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                            <?php endif; ?>
                        </h3>
                    <?php else : ?>
                        <h3 class="page-header item-title">
                            <a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)); ?>" class="uk-display-block uk-text-small uk-text-bold uk-text-ddark hoverAccent font"><?php echo $this->escape($child->title); ?></a>
                            <?php if ( $this->params->get('show_cat_num_articles', 1)) : ?>
                                <span class="badge badge-info tip hasTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_CONTENT_NUM_ITEMS_TIP'); ?>">
                                    <?php echo JText::_('COM_CONTENT_NUM_ITEMS'); ?><?php echo $child->getNumItems(true); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                                <a href="#category-<?php echo $child->id; ?>" data-toggle="collapse" data-toggle="button" class=" uk-display-block uk-text-small uk-text-bold uk-text-ddark hoverAccent font" aria-label="<?php echo JText::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                            <?php endif; ?>
                        </h3>
                    <?php endif; ?>
                    <?php if ($this->params->get('show_subcat_desc') == 1) : ?>
                        <?php if ($child->description) : ?>
                            <div class="category-desc">
                                <?php echo JHtml::_('content.prepare', $child->description, '', 'com_content.category'); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                        <div class="collapse fade" id="category-<?php echo $child->id; ?>">
                            <?php
                            $this->children[$child->id] = $child->getChildren();
                            $this->category = $child;
                            $this->maxLevel--;
                            echo $this->loadTemplate('children');
                            $this->category = $child->getParent();
                            $this->maxLevel++;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
			</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>