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
<?php if(empty($this->ajax)) { ?>
    <div id="hikashop_checkout_coupon_<?php echo $this->step; ?>_<?php echo $this->module_position; ?>" data-checkout-step="<?php echo $this->step; ?>" data-checkout-pos="<?php echo $this->module_position; ?>" class="hikashop_checkout_coupon uk-margin-medium-bottom uk-text-zero">
        <?php } ?>
        <div class="hikashop_checkout_loading_elem"></div>
        <div class="hikashop_checkout_loading_spinner"></div>
        <?php
        $this->checkoutHelper->displayMessages('coupon');
        $cart = $this->checkoutHelper->getCart();
        if(empty($cart->coupon)) {
            ?>
            <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-margin-remove-top uk-margin-bottom uk-text-small font"><?php echo JText::_('HIKASHOP_HAVE_COUPON'); ?></h3>
            <div>
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-expand"><input class="hikashop_checkout_coupon_field uk-width-1-1 uk-border-rounded font uk-input" id="hikashop_checkout_coupon_input_<?php echo $this->step; ?>_<?php echo $this->module_position; ?>" type="text" name="checkout[coupon]" value=""/></div>
                    <div class="uk-width-auto"><button type="submit" onclick="return window.checkout.submitCoupon(<?php echo $this->step.','.$this->module_position; ?>);" class="uk-button uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button-success uk-height-1-1 hikabtn_checkout_coupon_add"><?php echo JText::_('HIKA_APPLY_COUPON'); ?></button></div>
                </div>
            </div>
            <?php
        } else {
            echo '<h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-margin-remove-top uk-margin-bottom uk-text-small font">'.JText::_('HIKASHOP_COUPON').'</h3><div><div class="uk-grid-small uk-grid-divider uk-child-width-auto uk-text-zero" data-uk-grid>';
            echo '<div><span class="uk-text-tiny uk-text-bold uk-text-secondary font">'.JText::sprintf('HIKASHOP_COUPON_LABEL', @$cart->coupon->discount_code).'</span></div>';
            if(empty($cart->cart_params->coupon_autoloaded)) {
                global $Itemid;
                $url_itemid = '';
                if(!empty($Itemid))
                    $url_itemid = '&Itemid=' . $Itemid;
                ?>
                <a href="#removeCoupon" class="uk-text-tiny uk-text-bold uk-text-danger font" onclick="return window.checkout.removeCoupon(<?php echo $this->step; ?>,<?php echo $this->module_position; ?>);" title="<?php echo JText::_('REMOVE_COUPON'); ?>"><?php echo JText::_('REMOVE_COUPON'); ?></a></div></div>
                <?php
            }
        }
        if(empty($this->ajax)) { ?>
    </div>
<script type="text/javascript">
    if(!window.checkout) window.checkout = {};
    window.Oby.registerAjax(['checkout.coupon.updated','cart.updated'], function(params) {
        if(params && (params.cart_empty || (params.resp && params.resp.empty))) return;
        window.checkout.refreshCoupon(<?php echo (int)$this->step; ?>, <?php echo (int)$this->module_position; ?>);
    });
    window.checkout.refreshCoupon = function(step, id) { return window.checkout.refreshBlock('coupon', step, id); };
    window.checkout.submitCoupon = function(step, id) {
        var el = document.getElementById('hikashop_checkout_coupon_input_' + step + '_' + id);
        if(!el)
            return false;
        if(el.value == '') {
            window.Oby.addClass(el, 'hikashop_red_border');
            return false;
        }
        return window.checkout.submitBlock('coupon', step, id);
    };
    window.checkout.removeCoupon = function(step, id) {
        window.checkout.submitBlock('coupon', step, id, {'checkout[removecoupon]':1});
        return false;
    };
</script>
<?php } ?>