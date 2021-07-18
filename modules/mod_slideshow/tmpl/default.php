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
        <div class="uk-position-relative uk-visible-toggle uk-light" data-uk-slideshow="ratio: 1950:350;">
            <ul class="uk-slideshow-items">
                <?php for ($i=0;$i<$total;$i++) { ?>
                    <?php if (!empty($slides['media'][$i]) || !empty($slides['video'][$i])) { ?>
                    <li>
                        <?php if (!empty($slides['video'][$i])) { ?>
                        <video src="images/videos/<?php echo $slides['video'][$i].'.mp4'; ?>" poster="<?php echo $slides['media'][$i]; ?>" autoplay loop muted playsinline data-uk-cover></video>
                        <?php } else { ?>
                        <img src="<?php echo $slides['media'][$i]; ?>" width="1980" height="660" alt="<?php if (!empty($slides['title'][$i])) echo $slides['title'][$i]; ?>" data-uk-cove>
                        <?php } ?>
                        <?php if ($slides['cover'][$i]) { ?>
                            <div class="uk-position-absolute uk-position-cover cover"></div>
                        <?php } ?>
                        <div class="uk-position-center<?php echo $slides['align'][$i]; ?> uk-text-center uk-text<?php echo $slides['align'][$i]; ?>@m uk-light uk-width-1-1">
                            <div class="uk-container">
                                <div class="uk-child-width-1-1 uk-grid-medium" data-uk-grid>
                                    <?php if (!empty($slides['title'][$i])) { ?>
                                        <div><h2 class="uk-margin-remove font" data-uk-slideshow-parallax="x: 300,-300"><?php echo $slides['title'][$i]; ?></h2></div>
                                    <?php } ?>
                                    <?php if (!empty($slides['text'][$i])) { ?>
                                        <div><p class="uk-width-1-1 uk-width-1-2@m<?php if (str_replace(' ', '', $slides['align'][$i]) == 'center') {echo ' uk-margin-auto';} elseif (str_replace('-', '', $slides['align'][$i]) == 'right') {echo ' uk-margin-auto-left';} elseif (str_replace('-', '', $slides['align'][$i]) == 'left') {echo ' uk-margin-auto-right';} ?> font" data-uk-slideshow-parallax="x: 500,-500"><?php echo $slides['text'][$i]; ?></p></div>
                                    <?php } ?>
                                    <?php if (!empty($slides['link'][$i])) { ?>
                                        <div><a href="<?php echo $slides['link'][$i]; ?>" class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-text-capitalize font" data-uk-slideshow-parallax="x: 700,-700"><?php echo $slides['btnlabel'][$i]; ?></a></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php } ?>
                <?php } ?>
            </ul>
            <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-visible@m" href="#" data-uk-slidenav-next data-uk-slideshow-item="previous"></a>
            <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-visible@m" href="#" data-uk-slidenav-previous data-uk-slideshow-item="next"></a>
        </div>
    </section>
<?php } ?>