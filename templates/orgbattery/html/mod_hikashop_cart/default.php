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
if(!empty($html)){
?>
<div class="uk-flex uk-flex-middle uk-height-1-1 hikashop_cart_module <?php echo @$module->params['moduleclass_sfx']; ?>" id="hikashop_cart_module">
<?php echo $html; ?>
</div>
<?php }