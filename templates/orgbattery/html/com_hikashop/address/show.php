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
$tmpl = hikaInput::get()->getCmd('tmpl', '');
if(isset($this->params->address_id) || $tmpl == 'component') {
	echo $this->loadTemplate('legacy');
	return;
}

$show_url = 'address&task=listing';
$save_url = 'address&task=save&cid='.(int)@$this->address->address_id;
$update_url = 'address&task=edit&cid='.(int)@$this->address->address_id.'&address_type='.$this->address->address_type;
$delete_url = 'address&task=delete&cid='.(int)@$this->address->address_id;
$dest = 'hikashop_user_addresses_show';

if(!isset($this->edit) || $this->edit !== true ) {
?>
		<div class="hika_edit uk-position-top-left">
            <div class="uk-text-muted cursorPointer"><img src="<?php echo JURI::base().'images/sprite.svg#ellipsis-v'; ?>" width="20" height="20" alt="" data-uk-svg></div>
            <div data-uk-drop="pos: right-center" class="ellipsisDrop">
                <div class="uk-card uk-card-default uk-box-shadow-small uk-border-rounded uk-padding-small">
                    <ul class="uk-list uk-margin-remove uk-padding-remove">
                        <li>
                            <a class="uk-button uk-button-small uk-text-tiny uk-button-default uk-border-rounded uk-display-block uk-width-1-1 font" href="<?php echo hikashop_completeLink($update_url, 'ajax');?>" onclick="return window.addressMgr.get(this,'<?php echo $dest; ?>');"><?php echo JText::_('HIKA_EDIT'); ?></a>
                        </li>
                        <li>
                            <a class="uk-button uk-button-small uk-text-tiny uk-button-danger uk-border-rounded uk-display-block uk-width-1-1 font" href="<?php echo hikashop_completeLink($delete_url, 'ajax');?>" onclick="return window.addressMgr.delete(this,<?php echo (int)@$this->address->address_id; ?>);"><?php echo JText::_('HIKA_DELETE'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
		</div>
<?php
} else {
	if(!empty($this->ajax)) {
?>
	<div class="hikashop_checkout_loading_elem"></div>
	<div class="hikashop_checkout_loading_spinner"></div>
<?php }
}

if(isset($this->edit) && $this->edit === true) {
	if(empty($this->address->address_id)) {
		$title = $this->type == 'billing' ? 'HIKASHOP_NEW_BILLING_ADDRESS': 'HIKASHOP_NEW_SHIPPING_ADDRESS';
	} else {
		$title = in_array($this->address->address_type, array('billing', 'shipping')) ? 'HIKASHOP_EDIT_'.strtoupper($this->address->address_type).'_ADDRESS' : 'HIKASHOP_EDIT_ADDRESS';
	}
?>
<div class="hikashop_address_edition">
	<h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h4 font"><?php echo JText::_($title); ?></h3>
<?php
	$error_messages = hikaRegistry::get('address.error');
	if(!empty($error_messages)) {
		foreach($error_messages as $msg) {
			hikashop_display($msg[0], $msg[1]);
		}
	}

	if(!empty($this->extraData->address_top)) { echo implode("\r\n", $this->extraData->address_top); }
echo '<div class="uk-child-width-1-1 uk-child-width-1-3@m uk-grid-medium uk-margin-medium-bottom" data-uk-grid>';
	foreach($this->fields as $fieldname => $field) {
?>
	<div id="hikashop_address_<?php echo $fieldname; ?>" class="hika_options <?php if ($fieldname == 'address_pelak' || $fieldname == 'address_vahed') echo 'uk-width-1-2 uk-width-1-6@m'; ?>">
		<div class="hikashop_user_address_<?php echo $fieldname;?>"><label class="uk-form-label"><?php
			echo $this->fieldsClass->trans($field->field_realname);
			if($field->field_required)
				echo '<strong>&ensp;<span class="uk-text-danger">*</span></strong>';
		?></label></div>
		<div class="hikashop_user_address_<?php echo $fieldname;?>"><?php
			$onWhat = 'onchange';
			if($field->field_type == 'radio')
				$onWhat = 'onclick';

			$field->field_required = false;
			echo $this->fieldsClass->display(
					$field,
					@$this->address->$fieldname,
					'data[address]['.$fieldname.']',
					false,
					' ' . $onWhat . '="window.hikashop.toggleField(this.value,\''.$fieldname.'\',\'address\',0);"',
					false,
					$this->fields,
					$this->address
			);
		?></div>
	</div>
<?php
	} echo '</div>';
	if(!empty($this->extraData) && !empty($this->extraData->address_bottom)) { echo implode("\r\n", $this->extraData->address_bottom); }

	if(empty($this->address->address_id)) {
?>
	<input type="hidden" name="data[address][address_type]" value="<?php echo @$this->address->address_type; ?>"/>
<?php
	}
?>
	<input type="hidden" name="data[address][address_id]" value="<?php echo @$this->address->address_id; ?>"/>
	<input type="hidden" name="data[address][address_user_id]" value="<?php echo @$this->address->address_user_id; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
    <div class="uk-text-zero">
        <div class="uk-grid-medium" data-uk-grid>
            <div class="uk-width-1-1 uk-width-1-3@m">
                <a href="<?php echo hikashop_completeLink($save_url, 'ajax');?>" onclick="return window.addressMgr.form(this,'<?php echo $dest; ?>');" class="<?php echo $this->config->get('css_button','hikabtn'); ?> hikashop_checkout_address_ok_button uk-button uk-width-1-1 uk-button-success uk-border-rounded uk-box-shadow-small font"><?php echo JText::_('HIKA_SABT_ADDRESS'); ;?></a>
            </div>
            <div class="uk-width-1-1 uk-width-1-6@m">
                <a href="<?php echo hikashop_completeLink($show_url, 'ajax');?>" onclick="return window.addressMgr.get(this,'<?php echo $dest; ?>');" class="<?php echo $this->config->get('css_button','hikabtn'); ?> hikashop_checkout_address_cancel_button uk-button uk-width-1-1 uk-button-danger uk-border-rounded uk-box-shadow-small font"><?php echo JText::_('HIKA_CANCEL'); ;?></a>
            </div>
        </div>
    </div>
</div>
<?php
} else {
	if($this->config->get('address_show_details', 0)) {
		foreach($this->fields as $fieldname => $field) {
?>
	<dl class="hika_options">
		<dt class="hikashop_user_address_<?php echo $fieldname;?>"><label><?php echo $this->fieldsClass->trans($field->field_realname);?></label></dt>
		<dd class="hikashop_user_address_<?php echo $fieldname;?>"><span><?php echo $this->fieldsClass->show($field, @$this->address->$fieldname);?></span></dd>
	</dl>
<?php
		}
	} else {
		echo $this->addressClass->maxiFormat($this->address, $this->fields, true);
	}

	if(!empty($this->display_badge)) {
?>
		<div class="" style="float:right"><?php
			if(in_array($this->address->address_type, array('billing', '', 'both')))
				echo '<span class="hk-label hk-label-blue">'.JText::_('HIKASHOP_BILLING_ADDRESS').'</span>';
			if(in_array($this->address->address_type, array('shipping', '', 'both')))
				echo '<span class="hk-label hk-label-orange">'.JText::_('HIKASHOP_SHIPPING_ADDRESS').'</span>';
		?></div>
<?php
	}
}

if(!empty($this->init_js)) {
?>
<script type="text/javascript">
<?php echo $this->init_js; ?>
</script>
<?php
}
