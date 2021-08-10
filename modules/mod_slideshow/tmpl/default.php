<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$slides = json_decode( $params->get('slides'),true);
$total = count($slides['media']);
?>
<?php if ($total > 0) { ?>
    <section class="<?php echo $moduleclass_sfx; ?>">
        <div class="uk-position-relative uk-visible-toggle uk-light" data-uk-slideshow="ratio: 4:1; autoplay: true; autoplay-interval: 4000; animation: pull; min-height: 250;">
            <div class="uk-slideshow-items">
                <?php for ($i=0;$i<$total;$i++) { ?>
                    <?php if (!empty($slides['media'][$i])) { ?>
                    <div>
                        <picture>
                            <source media="(max-width:759px)" srcset="<?php echo $slides['mobilemedia'][$i]; ?>">
                            <img src="<?php echo $slides['media'][$i]; ?>" alt="Flowers"<?php if (!empty($slides['title'][$i])) echo ' alt="'.$slides['title'][$i].'"'; ?> data-uk-cover>
                        </picture>
                    </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-visible@m" href="#" data-uk-slidenav-next data-uk-slideshow-item="previous"></a>
            <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-visible@m" href="#" data-uk-slidenav-previous data-uk-slideshow-item="next"></a>
            <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-position-bottom-center uk-margin-small"></ul>
        </div>
    </section>
<?php } ?>