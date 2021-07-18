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
if(empty($this->row) || empty($this->row->products))
    return;
$url_itemid = (!empty($this->Itemid) ? '&Itemid=' . $this->Itemid : '');
$order_link = hikashop_completeLink('order&task=show&cid='.$this->row->order_id.$url_itemid);
$show_more = false;
$max_products = (int)$this->config->get('max_products_cpanel', 4);
if($max_products <= 0) $max_products = 4;
if(count($this->row->products) > $max_products) {
    $this->row->products = array_slice($this->row->products, 0, $max_products);
    $show_more = true;
}
?>
<div id="hika_order_<?php echo $this->row->order_id; ?>_details" class="uk-background-muted uk-border-rounded uk-padding-small uk-margin-top">
    <div class="uk-child-width-1-1 uk-child-width-1-4@m uk-grid-divider" data-uk-grid>
    <?php if(!empty($this->row->extraData->beforeProductsListing)) { echo implode("\r\n", $this->row->extraData->beforeProductsListing); } ?>
    <?php
    $group = $this->config->get('group_options',0);
    foreach($this->row->products as $product) {
        if($group && $product->order_product_option_parent_id)
            continue;
        $link = '#';
        if(!empty($product->product_id) && !empty($this->products[$product->product_id]) && !empty($this->products[$product->product_id]->product_published))
            $link = hikashop_contentLink('product&task=show&cid='.$product->product_id.'&name='.@$this->products[$product->product_id]->alias . $url_itemid, $this->products[$product->product_id]);
        ?>
        <div>
            <div class="uk-position-relative">
                <?php $img = $this->imageHelper->getThumbnail(@$product->images[0]->file_path, array(400, 300), array('default' => true, 'forcesize' => true,  'scale' => 'outside')); if(!empty($img) && $img->success) { ?><?php } ?>
                <div data-title="Image" class="hikashop_cart_product_image_value uk-margin-bottom">
                    <div class="hikashop_cart_product_image_thumb uk-border-rounded uk-overflow-hidden uk-box-shadow-small uk-display-inline-block">
                        <a href="<?php echo $link; ?>" class="uk-display-block" title="<?php echo $product->order_product_name; ?>">
                            <img class="hikashop_product_checkout_cart_image" title="<?php echo $product->order_product_name; ?>" alt="<?php echo $product->order_product_name; ?>" src="<?php echo $img->url; ?>">
                        </a>
                    </div>
                </div>
                <div class="hikashop_cart_product_name_value uk-margin-small-bottom">
                    <span class="hikashop_cart_product_name">
                        <a class="uk-text-small uk-text-bold uk-display-block uk-text-center hoverAccent font" href="<?php echo $link; ?>"><?php echo $product->order_product_name; ?></a>
                    </span>
                </div>
                <div class="uk-position-top-left hikashop_cart_product_quantity_value">
                    <div class="checkoutCartQuantity">
                        <span class="uk-text-tiny uk-flex uk-flex-center uk-flex-middle uk-text-white uk-box-shadow-small"><?php echo $product->order_product_quantity; ?></span>
                    </div>
                </div>
                <div data-title="قیمت مجموع" class="hikashop_cart_product_total_value uk-text-small uk-text-muted uk-display-block font uk-text-center checkoutCartPrice">
                    <span class="hikashop_product_price_full fdfdfdsdfsfdsfs">
                        <span class="hikashop_product_price hikashop_product_price_0 hikashop_product_price_with_discount"><?php echo $this->currencyClass->format( $product->order_product_price + $product->order_product_tax, $this->row->order_currency_id ); ?></span>
                    </span>
                </div>
            </div>
            <div class="uk-hidden">
                <?php if($this->config->get('show_code')) { ?>
                    <span class="hikashop_order_product_code"><?php echo $product->order_product_code; ?></span>
                    <?php
                }
                if($group) {
                    foreach($this->row->products as $j => $optionElement) {
                        if($optionElement->order_product_option_parent_id != $product->order_product_id)
                            continue;
                        $product->order_product_price += $optionElement->order_product_price;
                        $product->order_product_tax += $optionElement->order_product_tax;
                        $product->order_product_total_price += $optionElement->order_product_total_price;
                        $product->order_product_total_price_no_vat += $optionElement->order_product_total_price_no_vat;
                    }
                }
                ?>
            </div>
            <?php if(!empty($product->extraData))
                echo '<p class="hikashop_order_product_extra">' . (is_string($product->extraData) ? $product->extraData : implode('<br/>', $product->extraData)) . '</p>';
            ?>
        </div>
        <?php
    } if($show_more) { ?>
        <a href="<?php echo $order_link; ?>" class="hk-list-group-item hika_cpanel_product hika_order_product_more">
            <span><?php echo JText::_('SHOW_MORE_PRODUCTS'); ?></span>
        </a>
    <?php } ?>
        <?php if(!empty($this->row->extraData->afterProductsListing)) { echo implode("\r\n", $this->row->extraData->afterProductsListing); } ?>
    </div>
</div>