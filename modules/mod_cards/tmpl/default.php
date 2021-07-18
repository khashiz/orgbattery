<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$cards = json_decode( $params->get('cards'),true);
$total = count($cards['title']);
?>
<div class="uk-container">
    <div>
        <div class="uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid data-uk-scrollspy="cls: uk-animation-slide-bottom-small; target: > div; delay: 200;">
            <?php for($i=0;$i<$total;$i++) { ?>
                <?php if ($cards['title'][$i] != '') { ?>
                    <div>
                        <div class="uk-border-rounded uk-box-shadow-small uk-card uk-card-default uk-padding uk-text-center">
                            <div class="uk-margin-bottom uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#'.$cards['icon'][$i]; ?>" width="64" height="64" alt="<?php echo $cards['title'][$i]; ?>" data-uk-svg></div>
                            <h3 class="uk-margin-remove-top uk-margin-small-bottom uk-h4 font"><?php echo $cards['title'][$i]; ?></h3>
                            <span class="uk-display-block uk-text-muted font"><?php echo $cards['subtitle'][$i]; ?></span>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <hr class="uk-divider-icon uk-margin-large-top uk-margin-bottom">
</div>