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
<?php
/*
$pluginsClass = hikashop_get('class.plugins');
$plugin = $pluginsClass->getByName('system', 'hikashopsocial');
if (@ $plugin->published || @ $plugin->enabled) {
	echo '{hikashop_social}';
}else{ //backward compatibility added on 31/07/2014
	$plugin = $pluginsClass->getByName('content', 'hikashopsocial');
	if (@ $plugin->published || @ $plugin->enabled) {
		echo '{hikashop_social}';
	}
}
*/
?>
<span class="uk-display-inline-block uk-text-success">
    <img src="<?php echo JURI::base().'images/sprite.svg#share' ?>" width="24" height="24" data-uk-svg>
</span>
<div data-uk-drop="pos: bottom-left" class="shareDrop">
    <div>
        <div class="uk-card uk-card-default uk-border-rounded uk-box-shadow-small">
            <div class="uk-padding-small">
                <span class="uk-display-block uk-text-center uk-margin-small-bottom uk-text-secondary uk-text-tiny uk-text-nowrap font"><?php echo JText::sprintf('SOCIALSHARE'); ?></span>
                <ul class="uk-grid-small" data-uk-grid>
                    <li><a href="https://www.facebook.com/sharer.php?u=<?php echo JURI::current(); ?>" target="_blank" class="uk-width-1-1 uk-padding-small uk-button uk-border-rounded uk-text-zero uk-lineheight-zero uk-button-facebook"><img src="<?php echo JURI::base().'images/sprite.svg#facebook'; ?>" width="16" height="16" data-uk-svg></a></li>
                    <li><a href="https://twitter.com/share?url=<?php echo JURI::current(); ?>&text=<?php echo $this->element->product_name; ?>" target="_blank" class="uk-width-1-1 uk-padding-small uk-button uk-border-rounded uk-text-zero uk-lineheight-zero uk-button-twitter"><img src="<?php echo JURI::base().'images/sprite.svg#twitter'; ?>" width="16" height="16" data-uk-svg></a></li>
                    <li><a href="tg://msg_url?url=<?php echo JURI::current(); ?>&text=<?php echo $this->element->product_name; ?>" target="_blank" class="uk-width-1-1 uk-padding-small uk-button uk-border-rounded uk-text-zero uk-lineheight-zero uk-button-telegram"><img src="<?php echo JURI::base().'images/sprite.svg#telegram'; ?>" width="16" height="16" data-uk-svg></a></li>
                    <li><a href="https://wa.me/?text=<?php echo JURI::current(); ?>" target="_blank" class="uk-width-1-1 uk-padding-small uk-button uk-border-rounded uk-text-zero uk-lineheight-zero uk-button-whatsapp"><img src="<?php echo JURI::base().'images/sprite.svg#whatsapp'; ?>" width="16" height="16" data-uk-svg></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>