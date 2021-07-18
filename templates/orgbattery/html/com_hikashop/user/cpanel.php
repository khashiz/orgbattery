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
<?php $legacy = (int)$this->config->get('cpanel_legacy', false); ?>
<div>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small hika_cpanel_main_data">
        <div>
            <div class="uk-padding">
                <?php
                if(!empty($this->extraData->topMain)) { echo implode("\r\n", $this->extraData->topMain); }
                echo $this->loadTemplate('orders');
                if(!empty($this->extraData->bottomMain)) { echo implode("\r\n", $this->extraData->bottomMain); }
                ?>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($this->extraData->topLeft)) { echo implode("\r\n", $this->extraData->topLeft); } ?>
    <div class="hika_cpanel_icons uk-hidden">
        <?php $flag = false; foreach($this->buttons as $name => $btn) { $data = isset($btn['counter']) ? $btn['counter'] : false; ?>
            <a class="hika_cpanel_icon hikashop_cpanel_<?php echo $name; ?>_div" href="<?php echo hikashop_level($btn['level']) ? $btn['link'] : '#'; ?>" title="<?php echo $btn['text'];?>">
                <span class="hikashop_cpanel_button_text"><?php echo $btn['text'];?></span>
                <?php  if (($data != "") && ($sub_menu == true)) { ?>
                    <span class="hikashop_cpanel_data"><?php echo $data; ?></span>
                <?php } ?>
            </a>
        <?php } ?>
    </div>
<?php if(!empty($this->extraData->bottomLeft)) { echo implode("\r\n", $this->extraData->bottomLeft); } ?>