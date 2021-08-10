<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if(!empty($html)){ ?>
<div id="hikashop_module_<?php echo $module->id;?>" class="hikashop_module">
    <div data-uk-grid>
        <?php if ($module->id == 155) { ?>
            <div class="uk-width-1-1 uk-width-1-5@m uk-flex uk-flex-middle">
                <div class="uk-flex-1">
                    <div class="discountBoxWrapper uk-flex-center" data-uk-grid>
                        <div class="uk-width-auto uk-width-1-1@m uk-flex uk-flex-middle uk-flex-center"><h3 class="uk-text-center font uk-text-white"><?php echo str_replace('-', ' ', str_replace(" ","<br>", $module->title)); ?></h3></div>
                        <div class="uk-width-1-3 uk-width-1-1@m">
                            <div class="boxWrapper"><img src="<?php echo JUri::base().'images/percent-box.svg'; ?>" width="" height="" alt=""></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="uk-width-1-1 uk-width-expand@m"><?php echo $html; ?></div>
    </div>
</div>
<?php } ?>