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
<div class="uk-container">
    <div class="uk-slider-container-offset" data-uk-slider="autoplay: true; autoplay-interval: 1500;">
        <div class="uk-position-relative">
            <ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-4@m" data-uk-grid data-uk-scrollspy="cls: uk-animation-slide-bottom-small; target: > li; delay: 200;">
                <?php foreach ($list as $item) : ?>
                <li>
                    <div class="blogItem uk-text-zero uk-height-1-1" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting"><?php require JModuleHelper::getLayoutPath('mod_articles_news', '_homeblogitem'); ?></div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
    </div>
</div>