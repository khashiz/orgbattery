<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$applications = json_decode( $params->get('applications'),true);
$total = count($applications['title']);
?>
<div class="uk-container uk-container-expand">
    <div>
        <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid-small" data-uk-grid data-uk-scrollspy="cls: uk-animation-slide-bottom-small; target: > div; delay: 200;">
            <?php for ($i=0;$i<$total;$i++) { ?>
                <?php if (!empty($applications['title'][$i])) { ?>
                    <div>
                        <a href="<?php echo JRoute::_("index.php?Itemid=".$applications['menuitem'][$i]); ?>" class="uk-flex uk-flex-middle uk-flex-center uk-background-secondary uk-position-relative uk-height-medium uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-inline-clip uk-transition-toggle">
                            <img src="<?php echo $applications['bg'][$i]; ?>" alt="<?php echo $applications['title'][$i]; ?>" width="960" height="540" class="uk-transition-scale-up uk-transition-opaque">
                            <div class="uk-overlay-primary uk-position-cover"></div>
                            <div class="uk-overlay uk-position-center uk-light uk-text-center">
                                <span class="uk-text-small uk-text-muted uk-display-block uk-margin-small-bottom font f500"><?php echo JText::sprintf('PRODUCTSTOUSEIN'); ?></span>
                                <h3 class="uk-margin-remove-top font f700"><?php echo $applications['title'][$i]; ?></h3>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>