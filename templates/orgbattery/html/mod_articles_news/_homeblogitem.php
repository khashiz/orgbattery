<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="uk-height-1-1">
    <a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>" class="uk-card uk-card-default uk-display-block uk-box-shadow-small uk-box-shadow-hover-medium uk-border-rounded uk-overflow-hidden uk-height-1-1 uk-inline-clip uk-transition-toggle hoverAccent uk-text-dark hoverAccent">
        <div class="uk-card-media-top uk-box-shadow-small uk-overflow-hidden"><?php echo JLayoutHelper::render('joomla.content.image_blog_module', $item); ?></div>
        <div class="uk-card-body uk-padding-small">
            <div class="uk-padding-small">
                <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom font f500">سه شنبه ، 12 مرداد 1400</span>
                <div class="title">
                    <h2 itemprop="name" class="uk-text-small uk-margin-remove font f600 blogListTitle"><?php echo $item->title; ?></h2>
                </div>
            </div>
        </div>
    </a>
</div>