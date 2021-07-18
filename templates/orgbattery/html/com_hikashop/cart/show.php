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
        <div>
            <div data-uk-grid>
            <div class="uk-width-1-1 uk-width-expand@m">
                <div class="uk-card uk-card-default uk-padding uk-box-shadow-small uk-border-rounded<?php if ((int)@$this->cart->package['total_items'] == 1) echo ' uk-height-1-1'; ?>">
                    <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                        <?php if (empty($this->print_cart)) { ?>
                        <div data-form-container>
                            <?php if ((int)@$this->cart->package['total_items'] == 0) { ?>
                            <div class="uk-text-center">
                                <div class="uk-margin-medium-bottom"><img src="<?php echo JURI::base().'images/sprite.svg#shopping-cart-duotone'; ?>" width="128" height="128" alt="<?php echo $sitename; ?>" data-uk-svg></div>
                                <p class="uk-margin-remove uk-text-danger uk-text-small uk-text-bold font"><?php echo JTEXT::_('CARTISEMPTY'); ?></p>
                            </div>
                            <?php } ?>
                            <form class="<?php if ((int)@$this->cart->package['total_items'] == 0) {echo 'uk-hidden';} ?>" method="POST" id="hikashop_show_cart_form" name="hikashop_show_cart_form" action="<?php echo hikashop_completeLink('cart&task=show&cid='.(int)$this->cart->cart_id); ?>">
                                <?php if (!empty($this->manage) && $this->cart->cart_type != 'wishlist' && $this->config->get('enable_multicart') && !empty($this->user_carts)) { ?>
                                    <dl class="hika_options">
                                        <dt><label for="cart_name"><?php echo JText::_('HIKASHOP_CART_NAME'); ?></label></dt>
                                        <dd><input type="text" id="cart_name" name="data[cart_name]" class="inputbox" value="<?php echo $this->escape($this->cart->cart_name); ?>"/></dd>
                                    </dl>
                                <?php } ?>
                                <?php if (!empty($this->cart) && $this->cart->cart_type == 'wishlist' && !empty($this->multi_wishlist)) { ?>
                                    <?php if(!empty($this->manage)) { ?>
                                        <dl class="hika_options">
                                            <dt><label for="cart_name"><?php echo JText::_('HIKASHOP_WISHLIST_NAME'); ?></label></dt>
                                            <dd><input type="text" id="cart_name" name="data[cart_name]" class="inputbox" value="<?php echo $this->escape($this->cart->cart_name); ?>"/></dd>
                                            <dt><label for="cart_share"><?php echo JText::_('SHARE'); ?></label></dt>
                                            <dd><?php echo $this->cartShareType->display('data[cart_share]', $this->cart->cart_share); ?></dd>
                                        </dl>
                                    <?php } else { ?>
                                        <dl class="hika_options">
                                            <dt><label><?php echo JText::_('HIKASHOP_WISHLIST_NAME'); ?></label></dt>
                                            <dd><?php if(!empty($this->cart->cart_name)) echo $this->escape($this->cart->cart_name); else echo '<em>'.JText::_('HIKA_NO_NAME').'</em>'; ?></dd>
                                        </dl>
                                    <?php } ?>
                                <?php } ?>
                                <?php } ?>
<table id="hikashop_cart_product_listing" class="adminlist hikashop_cart_products uk-margin-remove uk-table uk-table-divider uk-table-middle uk-table-justify uk-table-responsive">
	<thead>
		<tr>
            <?php if($this->checkbox_column) { ?>
                <th class="uk-text-tiny uk-text-muted uk-table-expand" data-title="<?php echo JText::_('SELECT_ALL'); ?>" ><input type="checkbox" onchange="window.hikashop.checkAll(this);" /></th>
            <?php } ?>
            <th class="uk-text-tiny uk-text-muted font"><?php echo JText::_('CART_PRODUCT_NAME'); ?></th>
            <?php if(hikashop_level(1) && !empty($this->productFields)) {
                foreach($this->productFields as $fieldname => $field) {
                    echo '<th class="uk-text-tiny uk-text-muted font hikashop_cart_product_'.$fieldname.' title">'.$this->fieldsClass->trans($field->field_realname).'</th>';
                }
            } ?>
<!--            <th class="uk-text-tiny uk-text-muted uk-text-center uk-width-small font">--><?php //echo JText::_('HIKASHOP_CHECKOUT_STATUS'); ?><!--</th>-->
			<th class="uk-text-tiny uk-text-muted uk-text-center uk-width-small font"><?php echo JText::_('CART_PRODUCT_UNIT_PRICE'); ?></th>
			<th class="uk-text-tiny uk-text-muted uk-text-center uk-width-small font"><?php echo JText::_('PRODUCT_QUANTITY'); ?></th>
			<th class="uk-text-tiny uk-text-muted uk-text-center uk-width-small font"><?php echo JText::_('CART_PRODUCT_TOTAL_PRICE'); ?></th>
		</tr>
	</thead>

	<tbody>
<?php
	$group = $this->config->get('group_options', 0);
	$width = (int)$this->config->get('cart_thumbnail_x', 100);
	$height = (int)$this->config->get('cart_thumbnail_y', 75);
	$image_options = array(
		'default' => true,
		'forcesize' => $this->config->get('image_force_size', true),
		'scale' => $this->config->get('image_scale_mode','inside')
	);

	$i = 1;
	$k = 1;
	if(!empty($this->cart->products)) {
	foreach($this->cart->products as $k => $product) {
		if($group && !empty($product->cart_product_option_parent_id))
			continue;
		if(empty($product->cart_product_quantity) || substr($k,0,1) === 'p')
			continue;

		if(empty($this->cart->cart_products[$k]))
			continue;

		if (isset($product->bundle_quantity)) {
			if($product->product_quantity == -1 || $product->product_quantity > $product->bundle_quantity)
				$product->product_quantity = $product->bundle_quantity;
		}

		$cart_product = $this->cart->cart_products[$k];
		$status = 'err';
		$text = '';
		if (empty($product) || (!empty($product->product_sale_end) && $product->product_sale_end < time())) {
			$text = JText::_('HIKA_NOT_SALE_ANYMORE');
		} elseif ($product->product_quantity == -1) {
			$text = JText::sprintf('X_ITEMS_IN_STOCK', JText::_('HIKA_UNLIMITED'));
			$status = 'ok';
		} elseif (($product->product_quantity - $product->cart_product_quantity) >= 0) {
			$text = JText::sprintf('X_ITEMS_IN_STOCK', $product->product_quantity);
			$status = 'ok';
		} else {
			$text = JText::_('NOT_ENOUGH_STOCK');
		}

?>
		<tr class="row<?php echo $k; ?>">
            <?php if(!empty($this->manage)) { ?>
            <?php } ?>
<?php
		if($this->checkbox_column) {
?>
			<td class="hikashop_show_cart_form_checkbox">
<?php
			if ($status == 'ok') {
?>
				<input type="checkbox" name="products[]" value="<?php echo (int)$k; ?>" id="cb<?php echo $k; ?>"/>
<?php
			}
?>
			</td>
<?php
		}
?>
			<td data-title="<?php echo JText::_('CART_PRODUCT_NAME'); ?>" >
                <div class="uk-height-1-1 uk-flex-middle uk-grid-small uk-text-center uk-text-right@m" data-uk-grid>
                <?php
		$image_path = (!empty($product->images) ? @$product->images[0]->file_path : '');
		$img = $this->imageHelper->getThumbnail($image_path, array('width' => $width, 'height' => $height), $image_options);
		if($img->success) {
			$attributes = '';
			if($img->external)
				$attributes = ' width="'.$img->req_width.'" height="'.$img->req_height.'"';
			echo '<div class="uk-width-1-1 uk-width-auto@m"><div class="uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-display-inline-block uk-margin-small-left"><img class="" title="'.$this->escape(@$product->images[0]->file_description).'" alt="'.$this->escape(@$product->images[0]->file_name).'" src="'.$img->url.'" '.$attributes.' /></div></div>';
		}

?>
                    <div class="uk-width-1-1 uk-width-expand@m">
				<span class="hikashop_cart_product_name">
<?php
		if(empty($this->print_cart)) {
?>
					<a class="uk-text-small uk-text-bold hoverAccent font" href="<?php echo hikashop_contentLink('product&task=show&cid='.$product->product_id.'&name='.$product->alias, $product); ?>">
<?php
		}
		echo $product->product_name;
		if(empty($this->print_cart)) {
?>
					</a>
<?php
		}
?>
				</span></div>
<?php


		$html = '';
		$edit = false;
		if(!empty($product->product_parent_id))
			$edit = true;

		if(hikashop_level(2) && !empty($this->itemFields)) {
			$html .= '<p class="hikashop_order_product_custom_item_fields">';
			foreach($this->itemFields as $field) {
				$namekey = $field->field_namekey;
				if(!empty($cart_product->$namekey) && strlen($cart_product->$namekey)) {
					$edit = true;
					$html .= '<p class="hikashop_order_item_'.$namekey.'">' .
						$this->fieldsClass->getFieldName($field) . ': ' .
						$this->fieldsClass->show($field, $cart_product->$namekey) .
						'</p>';
				}
			}
			$html .= '</p>';

		}

		if($group) {
			foreach($this->cart->products as $opt_k => $opt_product) {
				if($opt_product->cart_product_option_parent_id != $product->cart_product_id)
					continue;

				$html .= '<p class="hikashop_cart_option_name">' . $opt_product->product_name . '</p>';
				$edit = true;
				if(!empty($opt_product->prices[0])) {
					if(!isset($product->prices[0])) {
						$product->prices[0] = new stdClass();
						$product->prices[0]->price_value = 0;
						$product->prices[0]->price_value_with_tax = 0;
						$product->prices[0]->price_currency_id = !empty($this->cart->cart_currency_id) ? (int)$this->cart->cart_currency_id : hikashop_getCurrency();
						$product->prices[0]->unit_price = new stdClass();
						$product->prices[0]->unit_price->price_value = 0;
						$product->prices[0]->unit_price->price_value_with_tax = 0.0;
						$product->prices[0]->unit_price->price_currency_id = $product->prices[0]->price_currency_id;
					}

					foreach(get_object_vars($product->prices[0]) as $key => $value) {
						if(is_object($value)) {
							foreach(get_object_vars($value) as $key2 => $var2) {
								if(strpos($key2,'price_value') !== false)
									$product->prices[0]->$key->$key2 += @$opt_product->prices[0]->$key->$key2;
							}
						} else {
							if(strpos($key,'price_value') !== false)
								$product->prices[0]->$key += @$opt_product->prices[0]->$key;
						}
					}
				}
			}
		}

		if($edit) {
			$popupHelper = hikashop_get('helper.popup');
			echo ' '.$popupHelper->display(
				'<i class="fas fa-pen"></i>',
				'HIKASHOP_EDIT_CART_PRODUCT',
				hikashop_completeLink('cart&task=product_edit&cart_id='.$this->cart->cart_id.'&cart_product_id='.$cart_product->cart_product_id.'&tmpl=component&'.hikashop_getFormToken().'=1'),
				'edit_cart_product',
				576, 480, 'title="'.JText::_('EDIT_THE_OPTIONS_OF_THE_PRODUCT').'"', '', 'link'
			);
		}

		if($this->config->get('show_code')) {
			echo '<br/>' . '<span class="hikashop_cart_product_code">'.$product->product_code.'</span>';
		}

		echo $html;

		if(!empty($product->extraData) && !empty($product->extraData->cart))
			echo '<div class="hikashop_cart_product_extradata"><p>' . implode('</p><p>', $product->extraData->cart) . '</p></div>';

			?></div></td>
<?php
	if(hikashop_level(1) && !empty($this->productFields)) {
		foreach($this->productFields as $field) {
			$namekey = $field->field_namekey;
?>			<td data-title="<?php echo $this->fieldsClass->trans($field->field_realname); ?>" >
<?php
			if(!empty($product->$namekey)) {
				echo '<p class="hikashop_order_product_'.$namekey.'">' . $this->fieldsClass->show($field, $product->$namekey) . '</p>';
			}
?>
			</td>
<?php
		}
	}
?>
            <?php /*
			<td data-title="<?php echo JText::_('HIKASHOP_CHECKOUT_STATUS'); ?>" style="text-align:center"><?php
	$tooltip_images = array(
		'ok' => '<i class="fa fa-check-circle"></i>',
		'err' => '<i class="fa fa-times-circle"></i>'
	);
	echo hikashop_hktooltip($text, '', $tooltip_images[$status]);
			?></td>
            <?php */ ?>
			<td class="uk-text-secondary uk-text-small uk-text-center uk-visible@m font" data-title="<?php echo JText::_('CART_PRODUCT_UNIT_PRICE'); ?>"><?php
	$this->setLayout('listing_price');
	$this->row =& $product;
	$this->unit = true;
	echo $this->loadTemplate();
			?></td>
			<td class="uk-text-zero" data-title="<?php echo JText::_('PRODUCT_QUANTITY'); ?>">
                <div class="uk-grid-small uk-grid-divider uk-grid-collapse" data-uk-grid>
<?php
	if(!empty($this->manage)) {
		if($this->cart->cart_type == 'wishlist') {
			$this->row->product_min_per_order = 1;
			$this->row->product_max_per_order = 0;
		}
		echo $this->loadHkLayout('quantity', array(
			'quantity_fieldname' => 'data[products]['.$product->cart_product_id.'][quantity]',
			'onchange_script' => 'window.cartMgr.checkQuantity(this);',
			'force_input' => true,
			'extra_data' => 'data-hk-product-name="'.$this->escape(strip_tags($product->product_name)).'" onkeypress="if(event.keyCode==13 && window.cartMgr.checkQuantity(this)){ window.hikashop.submitform(\'apply\',\'hikashop_show_cart_form\'); }"',
		));
	} else {
?>
				<div class="hikashop_product_quantity_div hikashop_product_quantity_input_div_none">
					<span><?php echo $product->cart_product_quantity; ?></span>
				</div>
<?php
	}
?>
<?php if(!empty($this->manage)) { ?>
    <div class="cartActionIcons">
        <div class="uk-height-1-1 uk-flex uk-flex-middle">
            <div><a title="<?php echo JText::_('HIKA_DELETE_CART'); ?>" data-uk-tooltip="offset:15;" class="hikashop_no_print uk-text-secondary" href="#delete" onclick="var qtyField = document.getElementById('<?php echo $this->last_quantity_field_id; ?>'); if(!qtyField) return false; qtyField.value = 0; return window.hikashop.submitform('apply','hikashop_show_cart_form');"><img src="<?php echo JURI::base().'images/sprite.svg#trash'; ?>" alt="" width="16" height="16" data-uk-svg></a></div>
            <div class="uk-hidden"><a id="cartQty" title="<?php echo JText::_('HIKA_REFRESH_CART'); ?>" data-uk-tooltip="offset:15;" class="uk-text-secondary" onclick="return window.hikashop.submitform('apply','hikashop_show_cart_form');"><img src="<?php echo JURI::base().'images/sprite.svg#refresh'; ?>" alt="" width="16" height="16" data-uk-svg></a></div>
        </div>
    </div>
<?php } ?>
                    <?php

	if(!empty($product->bought)) {
?>
				<div class="hikashop_wishlist_product_bought">
					<span><?php
		$desc = '';
		if($this->manage) {
			$buyers = array();
			foreach($product->related_orders as $related_order) {
				if(empty($buyers[(int)$related_order->order_user_id]))
					$buyers[(int)$related_order->order_user_id] = array($related_order->user_email, 0);
				$buyers[(int)$related_order->order_user_id][1] += (int)$related_order->order_product_quantity;
			}
			foreach($buyers as $buyer) {
				$desc .= $buyer[0] . ' ('.$buyer[1].')';
			}
		}

		if(!empty($desc)) {
			echo hikashop_hktooltip($desc, '', JText::sprintf('HIKA_BOUGHT_X_TIMES', (int)$product->bought));
		} else {
			echo JText::sprintf('HIKA_BOUGHT_X_TIMES', (int)$product->bought);
		}
					?></span>
				</div>
<?php
	}
?>
                </div>
			</td>
			<td class="uk-text-secondary uk-text-small uk-text-center uk-text-bold font" data-title="<?php echo JText::_('CART_PRODUCT_TOTAL_PRICE'); ?>">
                <?php
                $this->setLayout('listing_price');
                $this->row =& $product;
                $this->unit = false;
                echo $this->loadTemplate();
                ?>
            </td>
		</tr>
<?php
		$k = 1 - $k;
		$i++;
	}
	}
?>

	</tbody>
</table>
<?php if(empty($this->print_cart)) { ?>
	<input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
	<input type="hidden" name="ctrl" value="cart"/>
	<input type="hidden" name="task" value="show"/>
	<input type="hidden" name="cid" value="<?php echo (int)$this->cart->cart_id; ?>"/>
	<input type="hidden" name="addto_type" value=""/>
	<input type="hidden" name="addto_id" value=""/>
	<?php echo JHTML::_('form.token'); ?>
</form>
                </div>
                </div>
                </div>
    </div>

        <?php if ((int)@$this->cart->package['total_items'] != 0) { ?>
        <div class="uk-width-1-1 uk-width-1-4@m">
            <div>
                <div data-uk-sticky="media: @m; offset: 110;">
                    <div class="uk-card uk-card-default uk-box-shadow-small uk-border-rounded">
                        <div class="uk-visible@m blockHeader">
                            <h3 class="uk-h5 uk-margin-remove uk-padding-small uk-text-bold uk-text-center font"><img src="http://localhost/steelarvin/images/sprite.svg#shopping-cart" width="16" height="16" alt="" class="uk-margin-small-left" data-uk-svg><?php echo JText::sprintf('CART_SUMMERY'); ?></h3>
                        </div>
                        <div class="uk-padding-small">
                            <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
                                <div>
                                    <div>
                                        <div class="uk-text-zero">
                                            <div class="uk-grid-small" data-uk-grid>
                                                <div class="uk-width-expand uk-text-tiny font" data-uk-leader><?php echo JText::_('HIKASHOP_TOTAL_PRODUCTS'); ?></div>
                                                <div class="uk-text-small uk-text-bold uk-text-secondary font"><?php echo (int)@$this->cart->package['total_items']; ?></div>
                                            </div>
                                            <div class="uk-grid-small" data-uk-grid>
                                                <div class="uk-width-expand uk-text-tiny font" data-uk-leader><?php echo JText::_('HIKASHOP_TOTAL_AMOUNT'); ?></div>
                                                <div class="uk-text-small uk-text-bold uk-text-secondary font">
                                                    <?php if(!empty($this->cart->total->prices)) {
                                                        if($this->config->get('price_with_tax')) {
                                                            echo $this->currencyClass->format($this->cart->total->prices[0]->price_value_with_tax, $this->cart->total->prices[0]->price_currency_id);
                                                        }
                                                        if($this->config->get('price_with_tax') == 2) {
                                                            echo JText::_('PRICE_BEFORE_TAX');
                                                        }
                                                        if($this->config->get('price_with_tax') == 2 || !$this->config->get('price_with_tax')) {
                                                            echo $this->currencyClass->format($this->cart->total->prices[0]->price_value, $this->cart->total->prices[0]->price_currency_id);
                                                        }
                                                        if($this->config->get('price_with_tax') == 2) {
                                                            echo JText::_('PRICE_AFTER_TAX');
                                                        }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ((int)@$this->cart->package['total_items'] != 0) { ?>
                                    <div data-toolbar-container>
                                        <?php echo $this->toolbarHelper->process($this->toolbar, $this->title); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    </div>
    </div>
<script type="text/javascript">
if(!window.checkout) window.checkout = {};
window.Oby.registerAjax(['checkout.cart.updated','cart.updated'], function(params){
	window.location.reload();
});

window.hikashop.ready(function(){
	setTimeout(function(){window.hikashop.dlTitle('hikashop_show_cart_form')},1000);
});
if(!window.cartMgr) window.cartMgr = {};
window.cartMgr.moveProductsTo = function(id, type) {
	var d = document, form = d.getElementById('hikashop_show_cart_form');
	if(!form)
		form = d.forms['hikashop_show_cart_form'];
	if(!form)
		return false;
	form.task.value = 'addtocart';
	form.addto_type.value = type;
	form.addto_id.value = parseInt(id);
	if(typeof form.onsubmit == 'function')
		form.onsubmit();
	form.submit();
	return false;
};
window.cartMgr.checkQuantity = function(el) {
	var value = parseInt(el.value), old = el.getAttribute('data-hk-qty-old'),
		min = parseInt(el.getAttribute('data-hk-qty-min')),
		max = parseInt(el.getAttribute('data-hk-qty-max'));
	if(old)
		old = parseInt(old);
	if(isNaN(value)) {
		el.value = old || (isNaN(min) ? 1 : min);
		return false;
	}
	if(isNaN(min) || isNaN(max))
		return false;
	if((value <= max || max == 0) && value >= min)
		return true;

	if(max > 0 && value > max) {
		msg = '<?php echo JText::_('TOO_MUCH_QTY_FOR_PRODUCT', true); ?>';
		el.value = max;
	} else if(value < min) {
		msg = '<?php echo JText::_('NOT_ENOUGH_QTY_FOR_PRODUCT', true); ?>';
		el.value = min;
	}
	name = el.getAttribute('data-hk-product-name');
	if(msg && name)
		alert(msg.replace('%s', name));
	return true;
};
window.cartMgr.moveProductsToCart = function(id) { return window.cartMgr.moveProductsTo(id, 'cart'); };
window.cartMgr.moveProductsToWishlist = function(id) { return window.cartMgr.moveProductsTo(id, 'wishlist'); };
</script>
<?php }else{ ?>
<script type="text/javascript">
window.hikashop.ready( function() {window.focus();if(document.all){document.execCommand('print', false, null);}else{window.print();}setTimeout(function(){window.top.hikashop.closeBox();}, 2000);});
</script>
<?php } ?>
