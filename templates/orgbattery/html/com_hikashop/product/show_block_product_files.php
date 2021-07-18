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
<?php if(!empty($this->element->files)) { ?>
    <div id="hikashop_product_files_main" class="hikashop_product_files_main">
        <?php $files = array(); ?>
        <?php foreach($this->element->files as $file) { ?>
            <?php if(empty($file->download_link)) { ?>
                <?php continue; ?>
            <?php } ?>
            <?php if(empty($file->file_name)) { ?>
                <?php $file->file_name = $file->file_path; ?>
            <?php } ?>
            <?php $files[] = '<a class="hikashop_product_file_link uk-button uk-button-default uk-button-large uk-border-rounded uk-box-shadow-small uk-width-1-1 uk-width-auto@m font" href="' .  $file->download_link . '">' . $file->file_name . '</a>'; ?>
        <?php } ?>
        <?php if(count($files)) { ?>
            <?php echo implode('<br/>', $files); ?>
        <?php } ?>
        <hr class="uk-divider-icon uk-margin-medium-top uk-margin-medium-bottom">
    </div>
<?php } else { ?>
    <div id="hikashop_product_files_main" class="hikashop_product_files_main uk-text-center">
        <div class="uk-margin-bottom"><img src="<?php echo JURI::base().'images/sprite.svg#download'; ?>" width="128" height="128" alt="<?php echo $sitename; ?>" data-uk-svg></div>
        <p class="uk-margin-remove uk-text-danger uk-text-small uk-text-bold font"><?php echo JTEXT::_('HIKASHOP_NO_FILES_YET'); ?></p>
        <hr class="uk-divider-icon uk-margin-medium-top uk-margin-medium-bottom">
    </div>
<?php } ?>