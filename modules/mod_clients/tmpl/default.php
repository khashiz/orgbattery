<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$clients = json_decode( $params->get('clients'),true);
$total = count($clients['logo']);
?>
<div class="uk-container">
    <div>
        <div class="uk-card uk-card-default uk-border-rounded uk-box-shadow-small">
            <div data-uk-slider="velocity: 3; autoplay: true; autoplay-interval: 1500;">
                <div class="uk-position-relative uk-padding">
                    <div class="uk-child-width-1-2 uk-child-width-1-6@m uk-slider-items" data-uk-grid>
                        <?php for ($i=0;$i<$total;$i++) { ?>
                            <?php if (!empty($clients['logo'][$i])) { ?>
                                <div>
                                    <a href="<?php echo $clients['url'][$i]; ?>" target="_blank" class="uk-flex uk-flex-center uk-flex-middle uk-height-1-1" data-caption="<?php echo $clients['title'][$i]; ?>" data-uk-tooltip="title: <?php echo $clients['title'][$i]; ?>; offset: 15 ;"><img src="<?php echo $clients['logo'][$i]; ?>" alt="<?php echo $clients['title'][$i]; ?>"></a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-bottom"></ul>
            </div>
        </div>
    </div>
</div>