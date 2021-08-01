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
if(!$this->params->get('show_tags', 1))
	return;

if(!empty($this->variant_name))
	return;

$tagHelper = hikashop_get('helper.tags');
if(!$tagHelper->isCompatible())
	return;

?>
<?php
if(!empty($this->element->main)){
    $main_prod =& $this->element->main;
}else{
    $main_prod =& $this->element;
}
if(!empty($main_prod->product_id)) {
    $main_prod->tags = new JHelperTags;
    $main_prod->tags->getItemTags('com_hikashop.product', $main_prod->product_id);
    if(!empty($main_prod->tags)) { $main_prod->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
        <?php if (!empty($main_prod->tags->itemTags)) { ?>
            <?php echo $main_prod->tagLayout->render($main_prod->tags->itemTags); ?>
        <?php } ?>
    <?php } ?>
<?php } ?>