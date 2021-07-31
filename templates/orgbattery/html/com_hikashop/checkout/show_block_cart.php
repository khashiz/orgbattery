<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(empty($this->ajax)) { ?>
<div id="hikashop_checkout_cart_<?php echo $this->step; ?>_<?php echo $this->module_position; ?>" data-checkout-step="<?php echo $this->step; ?>" data-checkout-pos="<?php echo $this->module_position; ?>" class="hikashop_checkout_cart uk-margin-medium-bottom">
<?php } ?>
	<div class="hikashop_checkout_loading_elem"></div>
	<div class="hikashop_checkout_loading_spinner"></div>
<?php
	$this->checkoutHelper->displayMessages('cart');
?>
    <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-margin-remove-top uk-margin-bottom uk-text-small font"><?php echo JText::_('HIKASHOP_ORDER_ITEMS'); ?></h3>
    <div class="uk-margin-medium-bottom">
<div class="itemsWrapper wasTable uk-grid-divider uk-child-width-1-1 uk-child-width-1-3@m uk-text-zero" data-uk-grid>


<?php
	$k = 0;
	$group = $this->config->get('group_options', 0);
	$thumbnail_x = $this->config->get('thumbnail_x', 100);
	$thumbnail_y = $this->config->get('thumbnail_y', 100);

	$cart = $this->checkoutHelper->getCart();

	$displayingPrices = new stdClass();
	$displayingPrices->price_currency_id = $cart->full_total->prices[0]->price_currency_id;

	$displayingPrices->total = new stdClass();
	$displayingPrices->total->price_value = $cart->full_total->prices[0]->price_value;
	$displayingPrices->total->price_value_with_tax = $cart->full_total->prices[0]->price_value_with_tax;

	$displayingPrices->taxes = array();
	if(isset($cart->full_total->prices[0]->taxes))
		$displayingPrices->taxes = $cart->full_total->prices[0]->taxes;

	if(empty($this->options['show_payment'])){
		if(isset($cart->payment->payment_price) && $cart->payment->payment_price > 0 && $cart->payment->payment_price < $displayingPrices->total->price_value)
			$displayingPrices->total->price_value -= $cart->payment->payment_price;

		if(isset($cart->payment->payment_price_with_tax) && $cart->payment->payment_price_with_tax > 0 && $cart->payment->payment_price_with_tax < $displayingPrices->total->price_value_with_tax)
			$displayingPrices->total->price_value_with_tax -= $cart->payment->payment_price_with_tax;
		if(isset($cart->payment->taxes)){
			foreach($cart->payment->taxes as $payment_tax){
				if(array_key_exists($payment_tax->tax_namekey, $displayingPrices->taxes)){
					$displayingPrices->taxes[$payment_tax->tax_namekey]->tax_amount -= $payment_tax->tax_amount;
				}
			}
		}
	}
	if(empty($this->options['show_shipping']) && !empty($cart->shipping)){
		$shipping_price = 0;
		$shipping_price_with_tax = 0;
		foreach($cart->shipping as $shipping) {
			if(isset($shipping->shipping_price) && $shipping->shipping_price > 0 && $shipping->shipping_price < $displayingPrices->total->price_value)
				$shipping_price += $shipping->shipping_price;
			if(isset($shipping->shipping_price_with_tax) && $shipping->shipping_price_with_tax > 0 && $shipping->shipping_price_with_tax < $displayingPrices->total->price_value_with_tax)
				$shipping_price_with_tax += $shipping->shipping_price_with_tax;

			if(isset($shipping->taxes)){
				foreach($shipping->taxes as $shipping_tax){
					if(array_key_exists($shipping_tax->tax_namekey, $displayingPrices->taxes)){
						$displayingPrices->taxes[$shipping_tax->tax_namekey]->tax_amount -= $shipping_tax->tax_amount;
					}
				}
			}

			if(!empty($this->options['show_coupon']) && isset($cart->coupon->taxes) && isset($cart->coupon->discount_shipping_percent) && $cart->coupon->discount_shipping_percent > 0){
				foreach($cart->coupon->taxes as $coupon_tax){
					if(array_key_exists($coupon_tax->tax_namekey, $displayingPrices->taxes)){
						$displayingPrices->taxes[$coupon_tax->tax_namekey]->tax_amount -= $coupon_tax->tax_amount;
					}
				}
			}
		}
		$displayingPrices->total->price_value -= $shipping_price;
		$displayingPrices->total->price_value_with_tax -= $shipping_price_with_tax;
	}
	if(empty($this->options['show_coupon'])){
		if(isset($cart->coupon->discount_value_without_tax) && $cart->coupon->discount_value_without_tax > 0 && $cart->coupon->discount_value_without_tax < $displayingPrices->total->price_value)
			$displayingPrices->total->price_value += $cart->coupon->discount_value_without_tax;

		if(isset($cart->coupon->discount_value) && $cart->coupon->discount_value > 0 && $cart->coupon->discount_value < $displayingPrices->total->price_value_with_tax)
			$displayingPrices->total->price_value_with_tax += $cart->coupon->discount_value;
	}

	if(empty($this->productClass))
		$this->productClass = hikashop_get('class.product');

	if(!empty($this->options['show_cart_image']) && empty($this->imageHelper))
		$this->imageHelper = hikashop_get('helper.image');

	if(empty($this->currencyClass)) {
		$this->currencyClass = hikashop_get('class.currency');
		$this->currencyHelper =& $this->currencyClass;
	}

	global $Itemid;
	$checkout_itemid = (int)$this->config->get('checkout_itemid');
	if(!empty($checkout_itemid))
		$Itemid = $checkout_itemid;

	$url_itemid='';
	if(!empty($Itemid))
		$url_itemid = '&Itemid=' . $Itemid;

	foreach($cart->products as $i => $product) {
		if(empty($product->cart_product_quantity))
			continue;
		if($group && !empty($product->cart_product_option_parent_id))
			continue;
		$this->productClass->addAlias($product);
?>
        <div class="checkoutCartItemWrapper">
		<div class="uk-position-relative row<?php echo $k; ?>">
<?php
		if(!empty($this->options['show_cart_image'])) {
?>
			<div data-title="<?php echo JText::_('CART_PRODUCT_IMAGE'); ?>" class="hikashop_cart_product_image_value uk-margin-bottom">
<?php
			$image = null;
			if(!empty($product->images)) {
				$image = reset($product->images);
				$this->imageHelper->checkSize($thumbnail_x, $thumbnail_y, $image);
			}

			if($image && !$this->config->get('thumbnail')) {
				echo '<img src="'.$this->imageHelper->uploadFolder_url . $image->file_path.'" alt="' . $image->file_name . '" />';
			} else {
?>
				<div class="hikashop_cart_product_image_thumb uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-display-inline-block" ><?php
			$img = $this->imageHelper->getThumbnail(
				@$image->file_path,
				array(
					'width' => $thumbnail_x,
					'height' => $thumbnail_y
				),
				array(
					'default' => true,
					'forcesize' => $this->config->get('image_force_size', true),
					'scale' => $this->config->get('image_scale_mode', 'inside')
				)
			);
			if($img->success) {
				$attributes = '';
				if($img->external)
					$attributes = ' width="'.$img->req_width.'" height="'.$img->req_height.'"';
				echo '<img class="hikashop_product_checkout_cart_image" title="'.$this->escape(@$image->file_description).'" alt="'.$this->escape(@$image->file_name).'" src="'.$img->url.'"'.$attributes.'/>';
			}
				?></div>
<?php
		}
?>
			</div>
<?php } ?>
			<div data-title="<?php echo JText::_('CART_PRODUCT_NAME'); ?>" class="hikashop_cart_product_name_value uk-margin-small-bottom">
				<span class="hikashop_cart_product_name"><?php

		if(!empty($this->options['link_to_product_page'])) {
			?><a class="uk-text-small uk-text-bold uk-display-block uk-text-center hoverAccent font" href="<?php echo hikashop_contentLink('product&task=show&cid=' . $product->product_id . '&name=' . $product->alias . $url_itemid, $product);?>" ><?php
		}

		echo $product->product_name;

		if(!empty($this->options['show_product_code'])) {
			?><span class="hikashop_product_code_checkout"><?php
				echo $product->product_code;
			?></span><?php
		}

		if(!empty($this->options['link_to_product_page'])) {
			?></a><?php
		}

		if($group && !empty($this->options['show_price'])){
			$display_item_price = false;
			foreach($cart->products as $j => $optionElement) {
				if(empty($optionElement->cart_product_option_parent_id) || (int)$optionElement->cart_product_option_parent_id != (int)$product->cart_product_id)
					continue;
				if(!empty($optionElement->prices[0])) {
					$display_item_price = true;
					break;
				}
			}

			if($display_item_price)
				echo ' <span class="hikashop_product_base_price">' . strip_tags($this->getDisplayProductPrice($product, true)) . '</span>';
		}

?>
<?php
		$input = '';
		$html = '';
		$edit = false;
		if(!empty($product->product_parent_id))
			$edit = true;

		if(hikashop_level(2) && !empty($this->extraFields['item'])) {
			$item = $cart->cart_products[$i];
			foreach($this->extraFields['item'] as $field) {
				$namekey = $field->field_namekey;
				if(empty($item->$namekey) || !strlen($item->$namekey))
					continue;
				$edit = true;
				$html .= '<p class="hikashop_cart_item_'.$namekey.'">'.$this->fieldClass->getFieldName($field).': '.$this->fieldClass->show($field, $item->$namekey).'</p>';
			}
		}

		if($group) {
			if(!isset($product->prices[0])) {
				$product->prices[0] = new stdClass();
				$product->prices[0]->price_value = 0;
				$product->prices[0]->price_value_with_tax = 0.0;
				$product->prices[0]->price_currency_id = hikashop_getCurrency();
				$product->prices[0]->unit_price = new stdClass();
				$product->prices[0]->unit_price->price_value = 0;
				$product->prices[0]->unit_price->price_value_with_tax = 0.0;
				$product->prices[0]->unit_price->price_currency_id = hikashop_getCurrency();
			}

			foreach($cart->products as $j => $optionElement) {
				if(empty($optionElement->cart_product_option_parent_id) || $optionElement->cart_product_option_parent_id != $product->cart_product_id)
					continue;
				if(!empty($optionElement->prices[0]))
					$this->addOptionPriceToProduct($product->prices[0],$optionElement->prices[0]);

				$html .= '<p class="hikashop_cart_option_name">' . $optionElement->product_name;
				if(!empty($this->options['show_price']) && @$optionElement->prices[0]->price_value_with_tax > 0)
					$html .= ' ( + ' . strip_tags($this->getDisplayProductPrice($optionElement, true)) . ' )';
				if($optionElement->cart_product_quantity != $product->cart_product_quantity) {
					$html .= ' x'.round($optionElement->cart_product_quantity / $product->cart_product_quantity, 2);
				}
				$html .= '</p>';
				$edit = true;
			}
		}

		if(empty($this->options['status']) && $edit) {
			$popupHelper = hikashop_get('helper.popup');
			echo ' '.$popupHelper->display(
				'<i class="fas fa-pen"></i>',
				'HIKASHOP_EDIT_CART_PRODUCT',
				hikashop_completeLink('cart&task=product_edit&cart_id='.$cart->cart_id.'&cart_product_id='.$product->cart_product_id.'&tmpl=component&'.hikashop_getFormToken().'=1'),
				'edit_cart_product',
				576, 480, 'title="'.JText::_('EDIT_THE_OPTIONS_OF_THE_PRODUCT').'"', '', 'link'
			);
		}
?>
				</span>
<?php
		if(!empty($html))
			echo '<div class="hikashop_cart_product_custom_item_fields">'.$html.'</div>';

		if(!empty($product->extraData) && !empty($product->extraData->checkout))
			echo '<div class="hikashop_cart_product_extradata"><p>' . implode('</p><p>', $product->extraData->checkout) . '</p></div>';
?>
			</div>
<?php
	if(hikashop_level(1) && !empty($this->extraFields['product'])) {
		foreach($this->extraFields['product'] as $field) {
			$namekey = $field->field_namekey;
?>			<td data-title="<?php echo $this->fieldClass->trans($field->field_realname); ?>" class="hikashop_cart_product_field_<?php echo $namekey; ?>">
<?php
			if(!empty($product->$namekey)) {
				echo '<p class="hikashop_checkout_cart_product_'.$namekey.'">' . $this->fieldClass->show($field, $product->$namekey) . '</p>';
			}
?>
			</td>
<?php
		}
	}
	if(!empty($this->options['show_price'])) {
?>
			<div class="uk-hidden" data-title="<?php echo JText::_('CART_PRODUCT_UNIT_PRICE'); ?>" class="hikashop_cart_product_price_value">
                <?php
                echo $this->getDisplayProductPrice($product, true);
				if(HIKASHOP_RESPONSIVE) {
					?><span class="visible-phone"><?php echo JText::_('PER_UNIT'); ?></span><?php
				}
			?></div>
<?php
	}
?>
			<div data-title="<?php echo JText::_('PRODUCT_QUANTITY'); ?>" class="uk-position-top-left hikashop_cart_product_quantity_value"><?php

		if(empty($this->options['status'])) {
			$this->row =& $product;
			$cartHelper = hikashop_get('helper.cart');
			$this->quantityLayout = $cartHelper->getProductQuantityLayout($this->row);

			$onchange = 'window.hikashop.checkQuantity(this); if (this.value != \''.$product->cart_product_quantity.'\'){'.$input.'return window.checkout.submitCart('.$this->step.','.$this->module_position.'); } return false;';
			echo $this->loadHkLayout('quantity', array(
				'id_prefix' => 'hikashop_checkout_'.(int)$this->module_position.'_quantity_field',
				'quantity_fieldname' => 'checkout[cart][item]['.$product->cart_product_id.']',
				'onchange_script' => $onchange,
			));

			if(!empty($this->options['show_delete'])) {
				$url = hikashop_currentURL();
				$delete_url = hikashop_completeLink('product&task=updatecart&product_id='.$product->product_id.'&quantity=0');
				$delete_url .= ((strpos($delete_url, '?') === false) ? '?' : '&') . 'return_url='.urlencode(base64_encode(urldecode($url)));

?>
				<div class="hikashop_cart_product_quantity_delete">
					<a class="hikashop_no_print" href="<?php echo $delete_url; ?>" onclick="var qty_field = document.getElementById('<?php echo $this->last_quantity_field_id;?>'); if(qty_field){qty_field.value=0; return window.checkout.submitCart(<?php echo $this->step; ?>,<?php echo $this->module_position; ?>); } return false;" title="<?php echo JText::_('HIKA_DELETE'); ?>">
						<i class="fa fa-times-circle"></i>
					</a>
				</div>
<?php
			}
		}else{
			echo '<div class="checkoutCartQuantity"><span class="uk-text-tiny uk-flex uk-flex-center uk-flex-middle uk-text-white uk-box-shadow-small">'.$product->cart_product_quantity.'</span></div>';
		}
?>
			</div>
<?php
	if(!empty($this->options['show_price'])) {
?>
			<div data-title="<?php echo JText::_('CART_PRODUCT_TOTAL_PRICE'); ?>" class="hikashop_cart_product_total_value uk-text-small uk-text-muted uk-display-block font uk-text-center checkoutCartPrice"><?php
				echo $this->getDisplayProductPrice($product, false);
			?></div>
<?php
	}
?>
		</div>
        </div>
<?php
		$k = 1-$k;
	}
?>

</div>
    </div>
    <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-margin-remove-top uk-margin-bottom uk-text-small font"><?php echo JText::_('HIKASHOP_ORDER_SUMMERY'); ?></h3>
<div class="uk-text-zero summeryWrapper">

<?php
	$taxes = round($displayingPrices->total->price_value_with_tax - $displayingPrices->total->price_value, $this->currencyClass->getRounding($cart->full_total->prices[0]->price_currency_id));
	if(!empty($this->options['show_price']) && (!empty($cart->coupon) || !empty($cart->shipping) || !empty($cart->additional) || $taxes > 0)) {
?>
        <div class="uk-grid-small" data-uk-grid>
            <div class="uk-width-expand uk-text-small font f500 hikashop_cart_subtotal_title" id="hikashop_checkout_cart_total2_title" data-uk-leader><?php echo JText::_('SUBTOTAL'); ?></div>
            <div class="uk-text-small uk-text-bold uk-text-dark font">
                <span class="hikashop_checkout_cart_subtotal">
                    <?php
                    if(!empty($this->options['price_with_tax']))
                        echo $this->currencyClass->format(@$cart->total->prices[0]->price_value_with_tax,@$cart->total->prices[0]->price_currency_id);
                    else
                        echo $this->currencyClass->format(@$cart->total->prices[0]->price_value,@$cart->total->prices[0]->price_currency_id);
                    ?>
                </span>
            </div>
        </div>
    <?php } ?>

<?php if(!empty($this->options['show_price']) && !empty($cart->coupon) && !empty($this->options['show_coupon'])) { ?>
    <div class="uk-grid-small" data-uk-grid>
        <div class="uk-width-expand uk-text-small font f500 hikashop_cart_coupon_title" id="hikashop_checkout_cart_coupon_title" data-uk-leader><?php echo JText::_('HIKASHOP_CART_COUPON'); ?></div>
        <div class="uk-text-small uk-text-bold uk-text-dark font hikashop_cart_coupon_value">
            <span class="hikashop_checkout_cart_coupon uk-text-success"><?php
                if(empty($this->options['price_with_tax']))
                    echo str_replace('-', '', $this->currencyClass->format(@$cart->coupon->discount_value_without_tax * -1, @$cart->coupon->discount_currency_id));
                else
                    echo str_replace('-', '', $this->currencyClass->format(@$cart->coupon->discount_value * -1, @$cart->coupon->discount_currency_id));
                ?></span>
        </div>
    </div>
<?php } ?>

<?php if(!empty($this->options['show_price']) && !empty($cart->shipping) && !empty($this->options['show_shipping'])) { ?>
    <div class="uk-grid-small" data-uk-grid>
        <div class="uk-width-expand uk-text-small font f500 hikashop_cart_shipping_title" id="hikashop_checkout_cart_shipping_title" data-uk-leader><?php echo JText::_('HIKASHOP_SHIPPING_COST'); ?></div>
        <div class="uk-text-small uk-text-bold uk-text-dark font">
				<span class="hikashop_checkout_cart_shipping">
<?php
if(isset($this->value)) {
    echo $this->value;
} else {
    $shipping_price = null;
    foreach($cart->shipping as $shipping) {
        if(!isset($shipping->shipping_price) && isset($shipping->shipping_price_with_tax) ) {
            $shipping->shipping_price = $shipping->shipping_price_with_tax;
        }
        if(isset($shipping->shipping_price)) {
            if($shipping_price === null)
                $shipping_price = 0.0;
            if(empty($this->options['price_with_tax']) || !isset($shipping->shipping_price_with_tax))
                $shipping_price += $shipping->shipping_price;
            else
                $shipping_price += $shipping->shipping_price_with_tax;
        }
    }
    if($shipping_price !== null)
        echo $this->currencyClass->format($shipping_price, $cart->full_total->prices[0]->price_currency_id);
}
?>
				</span>
        </div>
    </div>

    <?php
		}

		if(!empty($cart->additional)) {
			$exclude_additionnal = explode(',', $this->config->get('order_additional_hide', ''));
			foreach($cart->additional as $k => $additional) {
				if(in_array($additional->name, $exclude_additionnal))
					continue;
				if(empty($this->options['show_price']) && !empty($additional->price_value))
					continue;
?>
		<div id="hikashop_checkout_cart_additional_<?php echo str_replace(' ','_',$k); ?>_line" >
			<div class="hikashop_cart_empty_footer"></div>
			<div id="hikashop_checkout_cart_additional_<?php echo str_replace(' ','_',$k); ?>_title" class="hikashop_cart_additional_title hikashop_cart_title"><?php
				echo JText::_($additional->name);
			?></div>
			<div class="hikashop_cart_additional_value" data-title="<?php echo JText::_($additional->name); ?>">
				<span class="hikashop_checkout_cart_additional">
<?php
				if(!empty($additional->price_value) || empty($additional->value)) {
					if($taxes == 0 || empty($this->options['price_with_tax']))
						echo $this->currencyClass->format(@$additional->price_value,$additional->price_currency_id);
					else
						echo $this->currencyClass->format(@$additional->price_value_with_tax,$additional->price_currency_id);
				} else
					echo $additional->value;
?>
				</span>
			</div>
		</div>
<?php
			}
		}

		if(!empty($this->options['show_price']) && $taxes > 0){
			if($this->config->get('detailed_tax_display') && isset($displayingPrices->taxes)) {
				foreach($displayingPrices->taxes as $tax) {
?>
		<div>
			<div colspan="<?php echo $row_count - 2; ?>" class="hikashop_cart_empty_footer"></div>
			<div id="hikashop_checkout_cart_tax_title" class="hikashop_cart_tax_title hikashop_cart_title"><?php
				echo hikashop_translate($tax->tax_namekey);
			?></div>
			<div class="hikashop_cart_tax_value" data-title="<?php echo $tax->tax_namekey; ?>">
				<span class="hikashop_checkout_cart_taxes"><?php
					echo $this->currencyClass->format($tax->tax_amount, $cart->full_total->prices[0]->price_currency_id);
				?></span>
			</div>
		</div>
<?php
				}
			} else {
?>
		<div>
			<div colspan="<?php echo $row_count - 2; ?>" class="hikashop_cart_empty_footer"></div>
			<div id="hikashop_checkout_cart_tax_title" class="hikashop_cart_tax_title hikashop_cart_title"><?php
				echo JText::_('TAXES');
			?></div>
			<div class="hikashop_cart_tax_value" data-title="<?php echo Jtext::_('TAXES'); ?>">
				<span class="hikashop_checkout_cart_taxes"><?php
					echo $this->currencyClass->format($taxes, $cart->full_total->prices[0]->price_currency_id);
				?></span>
			</div>
		</div>
            <?php } ?>
		<?php } ?>
		<?php if(!empty($this->options['show_price']) && !empty($cart->payment) && $cart->payment->payment_price != 0 && !empty($this->options['show_payment'])) { ?>
            <div class="uk-grid-small" data-uk-grid>
                <div class="uk-width-expand uk-text-small font f500 hikashop_cart_payment_title" id="hikashop_checkout_cart_payment_title" data-uk-leader><?php echo JText::_('HIKASHOP_PAYMENT_COST'); ?></div>
                <div class="uk-text-small uk-text-bold uk-text-dark font hikashop_cart_payment_value">
                    <span class="hikashop_checkout_cart_payment">
                    <?php
                    if(!isset($cart->payment->payment_price) && isset($cart->payment->payment_price_with_tax) ) {
                        if(isset($this->value)) {
                            echo $this->value;
                        } else {
                            $cart->payment->payment_price = 0.0;
                            $cart->payment->payment_price_with_tax = 0.0;
                        }
                    }
                    if(isset($cart->payment->payment_price)) {
                        if($taxes == 0 || empty($this->options['price_with_tax']) || !isset($cart->payment->payment_price_with_tax) )
                            echo $this->currencyClass->format(@$cart->payment->payment_price, $cart->full_total->prices[0]->price_currency_id);
                        else
                            echo $this->currencyClass->format(@$cart->payment->payment_price_with_tax, $cart->full_total->prices[0]->price_currency_id);
                    }
                    ?>
                </span>
                </div>
            </div>
        <?php } ?>

		<?php if(!empty($this->options['show_price'])) { ?>
            <div class="uk-grid-small" data-uk-grid>
                <div class="uk-width-expand uk-text-small font f500 hikashop_cart_total_title" id="hikashop_checkout_cart_final_total_title" data-uk-leader><?php echo JText::_('HIKASHOP_TOTAL'); ?></div>
                <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                    <span class="hikashop_checkout_cart_final_total uk-text-secondary"><?php echo $this->currencyClass->format($displayingPrices->total->price_value_with_tax, $displayingPrices->price_currency_id); ?></span>
                </div>
            </div>
        <?php } ?>

</div>


<?php

	if(!empty($this->extraData[$this->module_position]) && !empty($this->extraData[$this->module_position]->bottom)) { echo implode("\r\n", $this->extraData[$this->module_position]->bottom); }

	if(false) {
?>
	<noscript>
		<input id="hikashop_checkout_cart_quantity_button" class="btn button" type="submit" name="refresh" value="<?php echo JText::_('REFRESH_CART');?>"/>
	</noscript>
<?php
	}

	if(empty($this->ajax)) {
?>
</div>
<script type="text/javascript">
if(!window.checkout) window.checkout = {};
window.Oby.registerAjax(['checkout.cart.updated','cart.updated'], function(params){
	if(window.checkout.isSource(params, <?php echo (int)$this->step; ?>, <?php echo (int)$this->module_position; ?>))
		return;
	window.checkout.refreshCart(<?php echo (int)$this->step; ?>, <?php echo (int)$this->module_position; ?>);
});
window.checkout.refreshCart = function(step, id) { return window.checkout.refreshBlock('cart', step, id); };
window.checkout.submitCart = function(step, id) { return window.checkout.submitBlock('cart', step, id); };
</script>
<?php
	}
