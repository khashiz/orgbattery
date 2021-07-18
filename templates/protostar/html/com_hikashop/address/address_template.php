<?php
 defined('_JEXEC') or die('Restricted access');
?>
<span class="uk-text-bold uk-text-small uk-text-secondary font">{address_title}</span><span class="uk-display-block uk-text-small uk-text-muted font">{address_state} ، {address_city} ، {address_street} ، <?php echo JText::sprintf('HIKA_ADDRESS_PELAK');?> {address_pelak}<?php if(!empty($this->address->address_vahed)) echo JText::sprintf('HIKA_ADDRESS_VAHED','{address_vahed}');?> ، <?php echo JText::sprintf('HIKA_ADDRESS_POSTCODE');?> : {address_post_code}
<?php echo JText::sprintf('HIKA_ADDRESS_PERSON');?> : {address_firstname} {address_lastname}
<?php echo JText::sprintf('HIKA_ADDRESS_PHONE');?> : {address_telephone}</span>