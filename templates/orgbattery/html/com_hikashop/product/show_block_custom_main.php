<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$this->fieldsClass->prefix = '';
$displayTitle = false;
ob_start();
foreach ($this->fields as $fieldName => $oneExtraField) {
	$value = '';
	if(empty($this->element->$fieldName) && !empty($this->element->main->$fieldName))
		$this->element->$fieldName = $this->element->main->$fieldName;
	if(isset($this->element->$fieldName))
		$value = trim($this->element->$fieldName);
	if(!empty($value) || $value === '0' || $oneExtraField->field_type == 'customtext') {
		$displayTitle = true;
	?>
        <?php if ($oneExtraField->field_namekey != 'short_desc' && $oneExtraField->field_namekey != 'aparat_id' && $oneExtraField->field_namekey != 'sell_unit') { ?>
        <div>
            <div class="hikashop_product_custom_<?php echo $oneExtraField->field_namekey;?>_line uk-grid-small" data-uk-grid>
                <div class="uk-width-expand uk-text-small font" data-uk-leader>
                    <span id="hikashop_product_custom_name_<?php echo $oneExtraField->field_id;?>"><?php echo $this->fieldsClass->getFieldName($oneExtraField);?></span>
                </div>
                <div class="uk-text-small uk-text-bold uk-text-secondary font hikashop_cart_total_value">
                    <span class="uk-text-accent" id="hikashop_product_custom_value_<?php echo $oneExtraField->field_id;?>"><?php echo $this->fieldsClass->show($oneExtraField,$value); ?></span>
                </div>
            </div>
        </div>
        <?php } ?>
	<?php
	}
}
$specifFields = ob_get_clean();
if($displayTitle){
?>

<div id="hikashop_product_custom_info_main" class="hikashop_product_custom_info_main uk-margin-medium-bottom">
    <div class="uk-child-width-1-1 uk-child-width-1-1@m uk-grid-small" data-uk-grid><?php echo $specifFields; ?></div>
</div>
<?php } ?>