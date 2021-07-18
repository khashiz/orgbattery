<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$params = $displayData->params;
?>

<?php $images = json_decode($displayData->images); ?>
<?php $imgfloat = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro; ?>

<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
    <div class="uk-cover-container">
        <canvas width="400" height="300"></canvas>
        <div class="<?php if (empty($images->image_intro)) {echo 'uk-flex uk-flex-middle';} ?>" data-uk-cover>
            <div>
                <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>" class="uk-inline-clip uk-transition-toggle uk-display-block">
                    <img class="uk-transition-scale-up uk-transition-opaque" src="<?php echo !empty($images->image_intro) ? htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8') : 'images/sprite.svg#placeholder'; ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'); ?>" itemprop="thumbnailUrl" <?php if (empty($images->image_intro)) {echo ' data-uk-svg';} ?>>
                    <div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-primary uk-flex uk-flex-center uk-flex-middle"><i class="fas fa-link fa-2x"></i></div>
                </a>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="uk-cover-container">
        <canvas width="400" height="300"></canvas>
        <div data-uk-cover>
            <div class="uk-inline-clip">
                <img src="<?php echo htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'); ?>" itemprop="thumbnailUrl">
            </div>
        </div>
    </div>
<?php endif; ?>