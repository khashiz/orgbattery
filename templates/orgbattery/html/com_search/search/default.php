<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<main class="login <?php echo $this->pageclass_sfx; ?>" data-uk-height-viewport="expand: true">
    <div class="uk-padding uk-padding-remove-horizontal">
        <div>
            <div class="uk-container uk-container-xsmall">
                <div>
                    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
                        <div class="uk-padding">
                            <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                                <div><?php echo $this->loadTemplate('form'); ?></div>
                                <div>
                                    <?php if ($this->error == null && count($this->results) > 0) : ?>
                                        <?php echo $this->loadTemplate('results'); ?>
                                    <?php else : ?>
                                        <?php echo $this->loadTemplate('error'); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>