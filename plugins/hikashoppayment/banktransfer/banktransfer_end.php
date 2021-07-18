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
<div>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
        <div class="uk-padding">
            <div class="hikashop_banktransfer_end" id="hikashop_banktransfer_end">
                <span class="uk-display-block hikashop_banktransfer_end_message" id="hikashop_banktransfer_end_message">
                    <div class="uk-text-center uk-margin-medium-bottom">
                        <img src="<?php echo JURI::base().'images/sprite.svg#box-check'; ?>" width="128" height="128" data-uk-svg>
                    </div>
                    <div class="uk-text-center uk-text-zero thankYou">
                        <p><?php echo JText::sprintf('THANK_YOU_FOR_PURCHASE');?></p>
                        <p><?php echo JText::sprintf('ORDER_IS_COMPLETE'); ?></p>
                        <p><?php echo JText::sprintf('PLEASE_TRANSFERT_MONEY',$this->amount); ?></p>
                    </div>
                    <?php echo $this->information; ?>
                    <?php echo JText::sprintf('INCLUDE_ORDER_NUMBER_TO_TRANSFER',$this->order_number); ?>
                </span>
            </div>
        </div>
    </div>
</div>
<?php
if(!empty($this->return_url)) {
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration("window.hikashop.ready(function(){window.location='".$this->return_url."'});");
}
