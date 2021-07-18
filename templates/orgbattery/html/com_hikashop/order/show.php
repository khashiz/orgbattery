<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

$weight = bccomp((float)$this->order->order_weight, 0, 3);
$unit_weight = false;
if($weight && $this->config->get('show_invoice_unit_weight',0))
    $unit_weight = true;
$total_weight = false;
if($weight && $this->config->get('show_invoice_total_weight',0))
    $total_weight = true;
$order_weight = false;
if($weight && $this->config->get('show_invoice_weight',0))
    $order_weight = true;

?>
<div id="hikashop_order_main" class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
    <div class="uk-padding">
        <?php $colspan = 4; if($this->invoice_type == 'order') { ?>
        <form action="<?php echo hikashop_completeLink('order'.$this->url_itemid); ?>" method="post" name="adminForm" id="adminForm">
            <div>
            <?php } ?>
            <div id="hikashop_order_right_part">
                <h3 class="uk-margin-medium-bottom uk-text-center uk-text-accent uk-text-bold uk-h3 font"><?php echo JText::sprintf('ORDER_DETAILS', @$this->element->order_number); ?></h3>
                <div class="uk-grid-divider uk-grid-small uk-text-center uk-child-width-1-2 uk-child-width-1-<?php echo (!empty($this->shipping)) ? 4 : 3; ?>@m" data-uk-grid>
                    <?php if ($this->invoice_type == 'order' || empty($this->element->order_invoice_number)) { ?>
                        <div>
                            <span class="uk-display-block uk-margin-bottom uk-text-<?php echo $this->element->order_status; ?>"><img src="<?php echo JURI::base().'images/sprite.svg#status-'.$this->element->order_status; ?>" width="48" height="48" data-uk-svg></span>
                            <span class="uk-display-block uk-text-muted uk-text-tiny uk-margin-small-bottom font"><?php echo JText::sprintf('HIKASHOP_ORDER_STATUS'); ?></span>
                            <p class="hikashop_order_payment_method uk-margin-remove uk-text-<?php echo $this->element->order_status; ?> uk-text-small uk-text-bold font"><?php echo JText::sprintf('ORDER_STATUS_'.$this->element->order_status); ?></p>
                        </div>
                    <?php } else {?>
                        <div>
                            <span class="uk-display-block uk-margin-bottom"><img src="<?php echo JURI::base().'images/sprite.svg#home'; ?>" width="48" height="48" data-uk-svg></span>
                            <span class="uk-display-block uk-text-muted uk-text-tiny uk-margin-small-bottom font"><?php echo JText::_(strtoupper($this->invoice_type)); ?></span>
                            <p class="hikashop_order_payment_method uk-margin-remove uk-text-secondary uk-text-small uk-text-bold font"><?php echo @$this->element->order_invoice_number; ?></p>
                        </div>
                    <?php } ?>

                    <?php if ($this->invoice_type == 'order' || empty($this->element->order_invoice_created)) { ?>
                        <div>
                            <span class="uk-display-block uk-margin-bottom uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#calendar-alt'; ?>" width="48" height="48" data-uk-svg></span>
                            <span class="uk-display-block uk-text-muted uk-text-tiny uk-margin-small-bottom font"><?php echo JText::sprintf('ORDER_DATE'); ?></span>
                            <p class="hikashop_order_payment_method uk-margin-remove uk-text-secondary uk-text-small uk-text-bold font"><?php echo hikashop_getDate($this->element->order_created, 'D ، j M Y'); ?></p>
                        </div>
                    <?php } else {?>
                        <div>
                            <span class="uk-display-block uk-margin-bottom uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#calendar'; ?>" width="48" height="48" data-uk-svg></span>
                            <span class="uk-display-block uk-text-muted uk-text-tiny uk-margin-small-bottom font"><?php echo JText::sprintf('ORDER_DATE'); ?></span>
                            <p class="hikashop_order_payment_method uk-margin-remove uk-text-secondary uk-text-small uk-text-bold font"><?php echo hikashop_getDate($this->element->order_invoice_created, 'D ، j M Y'); ?></p>
                        </div>
                    <?php } ?>


                    <?php if(!empty($this->shipping)) { ?>
                        <div>
                            <span class="uk-display-block uk-margin-bottom uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#shipping-'.$this->order->order_shipping_id; ?>" width="48" height="48" data-uk-svg></span>
                            <span class="uk-display-block uk-text-muted uk-text-tiny uk-margin-small-bottom font"><?php echo JText::sprintf('HIKASHOP_SHIPPING_METHOD'); ?></span>
                            <p class="hikashop_order_payment_method uk-margin-remove uk-text-secondary uk-text-small uk-text-bold font">
                                <?php if(is_string($this->order->order_shipping_method)) { ?>
                                    <?php if(strpos($this->order->order_shipping_id, '-') !== false) { ?>
                                        <?php echo $this->shippingClass->getShippingName($this->order->order_shipping_method, $this->order->order_shipping_id); ?>
                                    <?php } else { ?>
                                        <?php echo $this->shipping->getName($this->order->order_shipping_method, $this->order->order_shipping_id); ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php echo implode(', ', $this->order->order_shipping_method); ?>
                                <?php } ?>
                            </p>
                        </div>
                    <?php } ?>
                    <?php if(!empty($this->payment)) { ?>
                        <div>
                            <span class="uk-display-block uk-margin-bottom uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#shippingmethod'.$this->order->order_payment_method; ?>" width="48" height="48" data-uk-svg></span>
                            <span class="uk-display-block uk-text-muted uk-text-tiny uk-margin-small-bottom font"><?php echo JText::sprintf('HIKASHOP_PAYMENT_METHOD'); ?></span>
                            <p class="hikashop_order_payment_method uk-margin-remove uk-text-secondary uk-text-small uk-text-bold font"><?php echo $this->payment->getName($this->order->order_payment_method, $this->order->order_payment_id); ?></p>
                        </div>
                    <?php } ?>
                    <?php
                    if($order_weight) {
                        echo '<p class="hikashpo_order_total_weight">'.JText::_('HIKASHOP_TOTAL_ORDER_WEIGHT') . ' : ' . rtrim(rtrim($this->order->order_weight,'0'),',.').' '.JText::_($this->order->order_weight_unit).'</p>';
                    }
                    ?>
                </div>
                <hr class="uk-margin-medium-top uk-margin-medium-bottom">
            </div>
            <?php /* ?>
            <div id="hikashop_order_left_part" class="hikashop_order_left_part"><?php echo $this->store_address; ?></div>
            <?php */ ?>
            <?php
            $params = null;
            $js = '';
            ?>
            <?php if(!empty($this->element->billing_address)) { ?>
                <div>
                    <div id="htmlfieldset_billing">
                        <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h5 font"><?php echo JText::_('HIKASHOP_BILLING_ADDRESS'); ?></h3>
                        <?php $addressClass = hikashop_get('class.address'); echo $addressClass->displayAddress($this->element->fields,$this->element->billing_address,'address'); ?>
                    </div>
                    <hr class="uk-margin-medium-top uk-margin-medium-bottom">
                </div>
            <?php } ?>
            <div>
                <div>
                    <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h5 font"><?php echo JText::_('ORDERITEMS'); ?></h3>
                    <div class="uk-text-zero">
                        <?php $files = false; ?>
                        <div>
                            <div class="itemsWrapper uk-margin-medium-bottom wasTable uk-grid-divider uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid>
                            <?php
                            $k=0;
                            $group = $this->config->get('group_options',0);
                            $imageHelper = hikashop_get('helper.image');
                            $width = (int)$this->config->get('cart_thumbnail_x', 400);
                            $height = (int)$this->config->get('cart_thumbnail_y', 300);
                            $image_options = array(
                                'default' => true,
                                'forcesize' => $this->config->get('image_force_size', true),
                                'scale' => $this->config->get('image_scale_mode','inside')
                            );
                            foreach($this->order->products as $product) {
                                $productData = null;
                                if(!empty($product->product_id) && !empty($this->products[ (int)$product->product_id ]))
                                    $productData = $this->products[ (int)$product->product_id ];
                                if($group && $product->order_product_option_parent_id)
                                    continue;
                                ?>
                                <div class="checkoutCartItemWrapper">
                                    <div class="uk-position-relative">
                                        <div data-title="<?php echo JText::_('HIKA_IMAGE'); ?>" class="hikashop_cart_product_image_value uk-margin-bottom">
                                            <div class="hikashop_cart_product_image_thumb uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-display-inline-block">
                                                <?php
                                                $image_path = (!empty($product->images) ? @$product->images[0]->file_path : '');
                                                $img = $imageHelper->getThumbnail($image_path, array('width' => $width, 'height' => $height), $image_options);
                                                if($img->success) {
                                                    echo '<img class="hikashop_order_item_image" title="'.$this->escape(@$product->images[0]->file_description).'" alt="'.$this->escape(@$product->images[0]->file_name).'" src="'.$img->url.'"/>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div data-title="<?php echo JText::_('PRODUCT'); ?>" class="hikashop_order_item_name_value hikashop_cart_product_name_value uk-margin-small-bottom">
                                            <?php if($this->invoice_type == 'order' && !empty($product->product_id)) { ?>
                                            <a class="uk-text-small uk-text-bold uk-display-block uk-text-center hoverAccent font" href="<?php echo hikashop_contentLink('product&task=show&cid='.$product->product_id.$this->url_itemid, $productData); ?>">
                                                <?php } ?>
                                                <div class="hikashop_order_product_name">
                                                    <?php echo '<div>'.$product->order_product_name.'</div>'; ?>
                                                    <?php if($this->config->get('show_code')) { ?>
                                                        <div class="hikashop_product_code_order"><?php echo $product->order_product_code; ?></div>
                                                    <?php } ?>
                                                </div>
                                                <?php
                                                if($group) {
                                                    $display_item_price = false;
                                                    foreach($this->order->products as $j => $optionElement) {
                                                        if($optionElement->order_product_option_parent_id != $product->order_product_id)
                                                            continue;
                                                        if($optionElement->order_product_price > 0) {
                                                            $display_item_price = true;
                                                        }
                                                    }
                                                    if($display_item_price) {
                                                        if($this->config->get('price_with_tax')) {
                                                            echo '<div>'.$this->currencyHelper->format($product->order_product_price + $product->order_product_tax, $this->order->order_currency_id).'</div>';
                                                        } else {
                                                            echo '<div>'.$this->currencyHelper->format($product->order_product_price, $this->order->order_currency_id).'</div>';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <?php if($this->invoice_type == 'order' && !empty($product->product_id)) { ?>
                                            </a>
                                            <div  class="hikashop_order_product_extra">
                                                <?php }
                                                if(hikashop_level(2)){
                                                    $item_type = 'display:back_invoice=1';
                                                    if($this->invoice_type == 'order'){
                                                        $item_type = 'display:front_order=1';
                                                    }
                                                    $itemFields = $this->fieldsClass->getFields($item_type,$product,'item');
                                                    if(!empty($itemFields)) {
                                                        foreach($itemFields as $field) {
                                                            $namekey = $field->field_namekey;
                                                            if(!empty($product->$namekey) && strlen($product->$namekey)) {
                                                                echo '<p class="hikashop_order_item_'.$namekey.'">' . $this->fieldsClass->getFieldName($field) . ': <span>' . $this->fieldsClass->show($field,$product->$namekey).'</span>' . '</p>';
                                                            }
                                                        }
                                                    }
                                                }
                                                if($group) {
                                                    foreach($this->order->products as $j => $optionElement) {
                                                        if($optionElement->order_product_option_parent_id != $product->order_product_id)
                                                            continue;
                                                        $product->order_product_weight += $optionElement->order_product_weight;
                                                        $product->order_product_price += $optionElement->order_product_price;
                                                        $product->order_product_tax += $optionElement->order_product_tax;
                                                        $product->order_product_total_price += $optionElement->order_product_total_price;
                                                        $product->order_product_total_price_no_vat += $optionElement->order_product_total_price_no_vat;
                                                        ?>
                                                        <p class="hikashop_order_option_name">
                                                            <?php
                                                            echo $optionElement->order_product_name;
                                                            if($optionElement->order_product_price > 0) {
                                                                if($this->config->get('price_with_tax')) {
                                                                    echo ' ( + '.$this->currencyHelper->format($optionElement->order_product_price + $optionElement->order_product_tax, $this->order->order_currency_id).' )';
                                                                } else {
                                                                    echo ' ( + '.$this->currencyHelper->format($optionElement->order_product_price, $this->order->order_currency_id).' )';
                                                                }
                                                            }
                                                            ?>
                                                        </p>
                                                        <?php
                                                    }
                                                }
                                                if(!empty($product->extraData))
                                                    echo '<p class="hikashop_order_product_extra">' . (is_string($product->extraData) ? $product->extraData : implode('<br/>', $product->extraData)) . '</p>';
                                                ?>
                                            </div>
                                        </div>
                                        <?php if(hikashop_level(1)){
                                            if(!empty($productFields)) {
                                                foreach($productFields as $field){
                                                    $namekey = $field->field_namekey;
                                                    $productData = @$this->products[$product->product_id];
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if(!empty($productData->$namekey))
                                                            echo  '<p class="hikashop_order_product_'.$namekey.'">'.$this->fieldsClass->show($field,$productData->$namekey).'</p>';
                                                        ?>
                                                    </td>
                                                    <?php
                                                }
                                            }
                                        }
                                        if($this->invoice_type == 'order' && $files) { ?>
                                            <td data-title="<?php echo JText::_('HIKA_FILES'); ?>" class="hikashop_order_item_files_value">
                                                <?php
                                                if(!empty($product->files) && ($this->order_status_download_ok || bccomp($product->order_product_price, 0, 5) == 0)) {
                                                    $html = array();
                                                    foreach($product->files as $file) {
                                                        $fileHtml = '';
                                                        if(!empty($this->download_time_limit) && ($this->download_time_limit + (!empty($this->order->order_invoice_created) ? $this->order->order_invoice_created : $this->order->order_created)) < time()) {
                                                            $fileHtml = JText::_('TOO_LATE_NO_DOWNLOAD');
                                                        }
                                                        if(!empty($file->file_limit) && (int)$file->file_limit != 0) {
                                                            $download_number_limit = $file->file_limit;
                                                            if($download_number_limit < 0)
                                                                $download_number_limit = 0;
                                                        } else {
                                                            $download_number_limit = $this->download_number_limit;
                                                        }
                                                        if(!empty($download_number_limit) && $download_number_limit<=$file->download_number) {
                                                            $fileHtml = JText::_('MAX_REACHED_NO_DOWNLOAD');
                                                        }
                                                        if(empty($fileHtml)) {
                                                            if(empty($file->file_name)) {
                                                                $file->file_name = JText::_('DOWNLOAD_NOW');
                                                            }
                                                            $file_pos = '';
                                                            if(!empty($file->file_pos)) {
                                                                $file_pos = '&file_pos='.$file->file_pos;
                                                            }
                                                            $token = hikaInput::get()->getVar('order_token');
                                                            if(!empty($token))
                                                                $file_pos .= '&order_token='.urlencode($token);
                                                            $fileHtml = '<a href="'.hikashop_completeLink('order&task=download&file_id='.$file->file_id.'&order_id='.$this->order->order_id.$file_pos.$this->url_itemid).'">'.$file->file_name.'</a>';
                                                            $order_created = (empty($this->order->order_invoice_created) ? $this->order->order_created : $this->order->order_invoice_created);
                                                            if(!empty($this->download_time_limit))
                                                                $fileHtml .= '<div>/ ' . JText::sprintf('UNTIL_THE_DATE', hikashop_getDate($order_created + $this->download_time_limit)).'</div>';
                                                            if(!empty($download_number_limit))
                                                                $fileHtml .= '<div>/ '. JText::sprintf('X_DOWNLOADS_LEFT', $download_number_limit - $file->download_number).'</div>';
                                                        } else {
                                                            if(empty($file->file_name)) {
                                                                $file->file_name = JText::_('EMPTY_FILENAME');
                                                            }
                                                            $fileHtml = $file->file_name . ' ' . $fileHtml;
                                                        }
                                                        $html[] = $fileHtml;
                                                    }
                                                    echo implode('<br/>', $html);
                                                }
                                                ?>
                                            </td>
                                            <?php if(!empty($product->files) && ($this->order_status_download_ok || bccomp($product->order_product_price, 0, 5) == 0)) { ?>
                                                <td data-title="<?php echo JText::_('HIKA_FILES'); ?>" class="hikashop_order_item_files_value_resp">
                                            <span>
                                                <?php echo implode('<br/>', $html); ?>
                                            </span>
                                                </td>
                                            <?php }
                                        }
                                        if($this->invoice_type == 'order' && !empty($this->action_column)) {
                                            echo '<td data-title="' . JText::_('HIKASHOP_ACTIONS') . '" class="hikashop_order_item_actions_value">';
                                            if(!empty($product->actions)) {
                                                if(count($product->actions) == 1) {
                                                    $d = reset($product->actions);
                                                    $link = '#';
                                                    $extra = '';
                                                    if(!empty($d['link']))
                                                        $link = $d['link'];
                                                    if(!empty($d['extra']))
                                                        $extra .= ' '.trim($d['extra']);
                                                    if(!empty($d['click']))
                                                        $extra .= ' onclick="'.trim($d['click']).'"';
                                                    ?>
                                                    <a href="<?php echo $link; ?>" class="<?php echo $this->config->get('css_button','hikabtn'); ?> hikabtn_order_action" <?php echo $extra; ?>><?php echo $d['name']; ?></a>
                                                    <?php
                                                } else {
                                                    echo $this->dropdownHelper->display( JText::_('HIKA_MORE'), $product->actions, array('type' => 'btn', 'right' => true, 'up' => false) );
                                                }
                                            }
                                            echo '</td>';
                                        }
                                        if($this->invoice_type != 'order' && $unit_weight) {
                                            echo '<td data-title="'.JText::_('PRODUCT_WEIGHT').'" class="hikashop_order_item_weight_value">' . rtrim(rtrim($product->order_product_weight,'0'),',.').' '.JText::_($product->order_product_weight_unit) . '</td>';
                                        }
                                        ?>
                                        <div data-title="<?php echo JText::_('UNIT_PRICE'); ?>" class="uk-hidden hikashop_order_item_price_value">
                                            <?php
                                            if($this->config->get('price_with_tax')) {
                                                echo '<span>'.$this->currencyHelper->format($product->order_product_price + $product->order_product_tax, $this->order->order_currency_id).'</span>';
                                            } else {
                                                echo '<span>'.$this->currencyHelper->format($product->order_product_price, $this->order->order_currency_id).'</span>';
                                            }
                                            ?>
                                        </div>
                                        <div data-title="<?php echo JText::_('PRODUCT_QUANTITY'); ?>" class="uk-position-top-left hikashop_cart_product_quantity_value">
                                            <div class="checkoutCartQuantity">
                                                <?php echo '<span class="uk-text-tiny uk-flex uk-flex-center uk-flex-middle uk-text-white uk-box-shadow-small">'.$product->order_product_quantity.'</span>'; ?>
                                            </div>
                                        </div>
                                        <?php
                                        if($this->invoice_type != 'order' && $total_weight) {
                                            echo '<td data-title="'.JText::_('TOTAL_WEIGHT').'" class="hikashop_order_item_total_weight_value">' . rtrim(rtrim($product->order_product_weight*$product->order_product_quantity,'0'),',.').' '.JText::_($product->order_product_weight_unit) . '</td>';
                                        }
                                        ?>
                                        <div data-title="<?php echo JText::_('PRICE'); ?>" class="hikashop_order_item_total_value hikashop_cart_product_total_value uk-text-small uk-text-muted uk-display-block font uk-text-center checkoutCartPrice">
                                            <?php
                                            if($this->config->get('price_with_tax')) {
                                                echo '<span>'.$this->currencyHelper->format($product->order_product_total_price,$this->order->order_currency_id).'</span>';
                                            } else {
                                                echo '<span>'.$this->currencyHelper->format($product->order_product_total_price_no_vat,$this->order->order_currency_id).'</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $k = 1 - $k; } ?>
                            </div>
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_( 'SUBTOTAL' ); ?></div>
                                <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                    <span class="uk-text-accent">
                                    <?php
                                    if($this->config->get('price_with_tax')) {
                                        echo $this->currencyHelper->format($this->order->order_subtotal,$this->order->order_currency_id);
                                    } else {
                                        echo $this->currencyHelper->format($this->order->order_subtotal_no_vat,$this->order->order_currency_id);
                                    }
                                    ?>
                                    </span>
                                </div>
                            </div>
                            <?php $taxes = $this->currencyHelper->round($this->order->order_subtotal - $this->order->order_subtotal_no_vat + $this->order->order_shipping_tax + $this->order->order_payment_tax - $this->order->order_discount_tax, $this->currencyHelper->getRounding($this->order->order_currency_id, true)); if(!empty($this->order->order_discount_code)) { ?>
                                <div class="uk-grid-small" data-uk-grid>
                                    <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_( 'HIKASHOP_COUPON' ); ?></div>
                                    <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                        <span class="uk-text-success">
                                        <?php
                                        if($this->config->get('price_with_tax')) {
                                            echo $this->currencyHelper->format($this->order->order_discount_price*-1.0,$this->order->order_currency_id);
                                        } else {
                                            echo $this->currencyHelper->format(($this->order->order_discount_price-@$this->order->order_discount_tax)*-1.0,$this->order->order_currency_id);
                                        }
                                        ?>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($this->order->order_shipping_method)) { ?>
                                <div class="uk-grid-small" data-uk-grid>
                                    <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_( 'SHIPPING' ); ?></div>
                                    <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                        <span class="uk-text-accent">
                                            <?php if($this->config->get('price_with_tax')) { ?>
                                                <?php echo $this->currencyHelper->format($this->order->order_shipping_price,$this->order->order_currency_id); ?>
                                            <?php } else { ?>
                                                <?php echo $this->currencyHelper->format($this->order->order_shipping_price-@$this->order->order_shipping_tax,$this->order->order_currency_id); ?>
                                            <?php } ?>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($this->order->additional)) { ?>
                                <?php $exclude_additionnal = explode(',', $this->config->get('order_additional_hide', '')); ?>
                                <?php foreach($this->order->additional as $additional) {
                                    if(in_array($additional->order_product_name, $exclude_additionnal))
                                        continue;
                                    ?>
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_($additional->order_product_name); ?></div>
                                        <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                            <span class="uk-text-accent">
                                            <?php
                                            if(!empty($additional->order_product_price)) {
                                                $additional->order_product_price = (float)$additional->order_product_price;
                                            }
                                            if(!empty($additional->order_product_price) || empty($additional->order_product_options)) {
                                                if($this->config->get('price_with_tax')) {
                                                    echo $this->currencyHelper->format($additional->order_product_price + @$additional->order_product_tax, $this->order->order_currency_id);
                                                }else{
                                                    echo $this->currencyHelper->format($additional->order_product_price, $this->order->order_currency_id);
                                                }
                                            } else {
                                                echo $additional->order_product_options;
                                            }
                                            ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php if(!empty($this->order->order_payment_method) && $this->order->order_payment_price != 0) { ?>
                                <div class="uk-grid-small" data-uk-grid>
                                    <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_( 'HIKASHOP_PAYMENT' ); ?></div>
                                    <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                        <span class="uk-text-accent">
                                        <?php
                                        if($this->config->get('price_with_tax')) {
                                            echo $this->currencyHelper->format($this->order->order_payment_price, $this->order->order_currency_id);
                                        } else {
                                            echo $this->currencyHelper->format($this->order->order_payment_price - @$this->order->order_payment_tax, $this->order->order_currency_id);
                                        }
                                        ?>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if($taxes != 0) { ?>
                                <?php if($this->config->get('detailed_tax_display') && !empty($this->order->order_tax_info)) { ?>
                                    <?php foreach($this->order->order_tax_info as $tax) { ?>
                                        <div class="uk-grid-small" data-uk-grid>
                                            <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo hikashop_translate($tax->tax_namekey); ?></div>
                                            <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                                <span class="uk-text-accent"><?php echo $this->currencyHelper->format($tax->tax_amount, $this->order->order_currency_id); ?></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_( 'VAT' ); ?></div>
                                        <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                            <span class="uk-text-accent"><?php echo $this->currencyHelper->format($taxes,$this->order->order_currency_id); ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-expand uk-text-tiny font hikashop_cart_total_title" data-uk-leader><?php echo JText::_( 'HIKASHOP_TOTAL' ); ?></div>
                                <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                                    <span class="uk-text-accent"><?php echo $this->currencyHelper->format($this->order->order_full_price, $this->order->order_currency_id); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                        JPluginHelper::importPlugin('hikashop');
                        JPluginHelper::importPlugin('hikashopshipping');
                        JPluginHelper::importPlugin('hikashoppayment');
                        $app = JFactory::getApplication();
                        $app->triggerEvent('onAfterOrderProductsListingDisplay', array(&$this->order, 'order_front_show'));
                        ?>
                        <?php if(hikashop_level(2) && !empty($this->fields['order'])) { ?>
                            <tr>
                                <td>
                                    <fieldset class="hikashop_order_custom_fields_fieldset">
                                        <legend><?php echo JText::_('ADDITIONAL_INFORMATION'); ?></legend>
                                        <table class="hikashop_order_custom_fields_table adminlist">
                                            <?php
                                            foreach($this->fields['order'] as $fieldName => $oneExtraField) {
                                                if(empty($this->order->$fieldName))
                                                    continue;
                                                ?>
                                                <tr class="hikashop_order_custom_field_<?php echo $fieldName;?>_line">
                                                    <td class="key">
                                                        <?php echo $this->fieldsClass->getFieldName($oneExtraField); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $this->fieldsClass->show($oneExtraField,$this->order->$fieldName); ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if(hikashop_level(2) && !empty($this->order->entries)) { ?>
                            <tr>
                                <td>
                                    <fieldset class="htmlfieldset_entries">
                                        <legend><?php echo JText::_('HIKASHOP_ENTRIES'); ?></legend>
                                        <table class="hikashop_entries_table adminlist" cellpadding="1" width="100%">
                                            <thead>
                                            <tr>
                                                <th class="title titlenum"><?php echo JText::_( 'HIKA_NUM' ); ?></th>
                                                <?php
                                                if(!empty($this->fields['entry'])) {
                                                    foreach($this->fields['entry'] as $field) {
                                                        echo '<th class="title">' . $this->fieldsClass->trans($field->field_realname) . '</th>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $k = 0;
                                            $i = 1;
                                            foreach($this->order->entries as $entry) {
                                                ?>
                                                <tr class="row<?php echo $k;?>">
                                                    <td><?php echo $i; ?></td>
                                                    <?php
                                                    if(!empty($this->fields['entry'])) {
                                                        foreach($this->fields['entry'] as $field) {
                                                            $namekey = $field->field_namekey;
                                                            if(!empty($entry->$namekey))
                                                                echo '<td>'.$this->fieldsClass->show($field, $entry->$namekey).'</td>';
                                                        }
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                $k = 1 - $k;
                                                $i++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        <?php } ?>
                    </div>
                </div>
            </div>

                <input type="hidden" name="cid" value="<?php echo (int)$this->element->order_id; ?>" />
                <input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="ctrl" value="<?php echo hikaInput::get()->getCmd('ctrl'); ?>" />
                <input type="hidden" name="cancel_redirect" value="<?php echo hikaInput::get()->getString('cancel_redirect'); ?>" />
                <input type="hidden" name="cancel_url" value="<?php echo hikaInput::get()->getString('cancel_url'); ?>" />
                <?php echo JHTML::_( 'form.token' ); ?>
                <?php if($this->invoice_type == 'order') { ?>
                <div>
                    <hr class="uk-margin-medium-top uk-margin-medium-bottom">
                    <?php echo $this->toolbarHelper->process($this->toolbar, $this->title); ?>
                </div>
            </div>
        </form>
    </div>
    <?php } ?>
</div>
<div style="page-break-after:always"></div>
