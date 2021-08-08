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
        <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-margin-small-bottom">
            <div class="uk-padding">
                <div class="hikashop_collectondelivery_end" id="hikashop_collectondelivery_end">
                <span class="uk-display-block hikashop_collectondelivery_end_message" id="hikashop_collectondelivery_end_message">
                    <div class="uk-text-center uk-margin-medium-bottom">
                        <img src="<?php echo JURI::base().'images/sprite.svg#box-check'; ?>" width="128" height="128" data-uk-svg>
                    </div>
                    <div class="uk-text-center thankYou">
                        <p><?php echo JText::sprintf('THANK_YOU_FOR_PURCHASE');?></p>
                        <p><?php echo JText::sprintf('ORDER_IS_COMPLETE', '<span class="uk-text-secondary f600">'.$this->order_number.'</span>'); ?></p>
                        <p><?php echo JText::sprintf('AMOUNT_COLLECTED_ON_DELIVERY', '<span class="uk-text-secondary f600">'.$this->amount.'</span>'); ?></p>
                        <p><?php echo JText::sprintf('WE_WILL_COME_TO_YOU'); ?></p>
                    </div>
                </span>
                </div>
                <div class="uk-margin-medium-top">
                    <div class="uk-margin-medium-bottom">
                        <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid-medium" data-uk-grid>
                            <?php if(!empty($this->url)) { ?>
                                <div><a class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-width-1-1 font" href="<?php echo $this->url; ?>"><?php echo JText::sprintf('ORDERDETAILS'); ?></a></div>
                            <?php } ?>
                            <div><a class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-width-1-1 font" href="<?php echo JRoute::_("index.php?Itemid=204"); ?>"><?php echo JText::sprintf('MYORDERS'); ?></a></div>
                        </div>
                    </div>
                    <div class="uk-text-center">
                        <a class="uk-text-muted uk-text-small hoverAccent font f500" href="<?php echo JUri::base(); ?>" title="<?php echo JText::sprintf('BACKTOHOME'); ?>"><?php echo JText::sprintf('BACKTOHOME'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if(!empty($this->payment_params->return_url)){
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration("window.hikashop.ready( function() {window.location='".$this->payment_params->return_url."'});");
}