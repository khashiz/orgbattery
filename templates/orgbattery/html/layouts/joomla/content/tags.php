<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

$authorised = JFactory::getUser()->getAuthorisedViewLevels();

?>
<?php if (!empty($displayData)) : ?>
    <hr class="uk-divider-icon uk-margin-medium-top uk-margin-medium-bottom">
    <div id="hikashop_product_tags_main" class="tagsWrapper uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-margin-medium-top">
        <div class="uk-padding-small">
            <ul class="uk-grid-collapse tags inline uk-grid-small" data-uk-grid>
                <?php foreach ($displayData as $i => $tag) : ?>
                    <?php if (in_array($tag->access, $authorised)) : ?>
                        <?php $tagParams = new Registry($tag->params); ?>
                        <?php $link_class = $tagParams->get('tag_link_class', 'label label-info'); ?>
                        <li class="tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
                            <a href="<?php echo JRoute::_(TagsHelperRoute::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="<?php echo $link_class; ?> uk-button uk-button-small uk-button-default uk-border-rounded uk-text-tiny font">
                                <?php echo $this->escape($tag->title); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
