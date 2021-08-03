<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$style = '';
if ($params->get('paddingtop') != 0) {$style .= 'padding-top:'.$params->get('paddingtop').'px;';}
if ($params->get('paddingbottom') != 0) {$style .= 'padding-bottom:'.$params->get('paddingbottom').'px;';}
if (!empty($params->get('bgcolor'))) {$style .= 'background-color:'.$params->get('bgcolor').';';}
if (!empty($params->get('bgimage'))) {$style .= 'background-image:url('.$params->get('bgimage').');background-size:cover;background-position:center center;';}
?>
<section>
    <div class="uk-container">
        <div class="uk-position-relative uk-border-rounded uk-box-shadow-medium uk-overflow-hidden <?php echo $moduleclass_sfx; ?> uk-position-relative" style="<?php echo $style; ?>" <?php if ($params->get('parallax') == 1) {echo 'data-uk-parallax="bgy: -300"';} ?>>
            <?php if ($params->get('cover')) { ?><div class="uk-position-absolute uk-position-cover" <?php if (!empty($params->get('covercolor'))) {echo 'style="background-color:'.$params->get('covercolor').'";';} ?>></div><?php } ?>
            <div class="<?php echo $params->get('gridsize') == 'normal' ? 'uk-container': 'uk-container uk-container-'.$params->get('gridsize'); ?> uk-position-relative uk-position-z-index">
                <div class="uk-grid-large" data-uk-grid>
                    <div class="uk-width-1-1">
                        <?php if ($params->get('title')) { ?><h3 class="font f700 uk-h2 uk-text-center uk-text-<?php echo $params->get('align'); ?>@m uk-margin-bottom" style="color: <?php echo $params->get('titlecolor', '#000'); ?>"><?php echo $params->get('title'); ?></h3><?php } ?>
                        <?php if (!empty($params->get('desc'))) { ?><div class="description font uk-text-<?php echo $params->get('align'); ?> uk-margin-medium-bottom" style="color: <?php echo $params->get('desccolor', '#666'); ?>"><?php echo $params->get('desc'); ?></div><?php } ?>
                        <?php $buttons = json_decode( $params->get('buttons'),true); $totalbuttons = count($buttons['url']); if (count($buttons) > 0) { ?>
                            <div class="buttons">
                                <div class="uk-grid-small uk-flex-<?php echo $params->get('align'); ?>" data-uk-grid>
                                    <?php for ($i=0;$i<$totalbuttons;$i++) { ?>
                                        <div class="uk-width-<?php echo $buttons['width'][$i]; ?>@m uk-text-center">
                                            <a href="<?php echo $buttons['url'][$i]; ?>" class="uk-border-rounded uk-box-shadow-small uk-button uk-button-success uk-button-<?php echo $buttons['size'][$i]; ?> font"><?php echo $buttons['label'][$i]; ?></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>