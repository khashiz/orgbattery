<?php 
/**
 * @package Module EB Ajax Search for Joomla!
 * @version 1.5: mod_ebajaxsearch.php Sep 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/
defined('_JEXEC') or die; 
 

JHtml::stylesheet('modules/' . $module->module . '/assets/css/style.css');
$document = JFactory::getDocument();
$document->addScript("modules/".$module->module."/assets/js/eb_ajaxsearch.js");

$label        = htmlspecialchars($params->get('label', ''));
$width        = (int) $params->get('width', '100');
$text         = htmlspecialchars($params->get('text', ''));
$button       = $params->get('button', 0);
$button_background_color = $params->get('button_background_color');
$button_text_color = $params->get('button_text_color');
$button_text  = htmlspecialchars($params->get('button_text', JText::_('MOD_SEARCHAJAX_SEARCHBUTTON_TEXT')));
// $max_results  = (int) $params->get('max_results', 5);
$text_color =  $params->get('text_color');
$background_color =  $params->get('background_color');
$style_effect =  $params->get('style_effect');
//$box_shadow_color = $params->get('box_shadow_color', 'rgba(140, 131, 131, 0.54'); 

$module_id = $module->id;
?>
<div id="ajaxsearch_<?php echo $module_id; ?>">
  <form id="mod-ajaxsearch-form-<?php echo $module_id; ?>" action="" method="post" class="uk-width-1-1 uk-width-large@m ajaxSearchForm">
    <div class="uk-position-relative">
      <!-- <div class="btn-group pull-left"> -->
        <?php if($label){ ?><label for="mod-search-searchword" class="search-label"><?php echo $label; ?></label><?php } ?>
        <span class="uk-margin-small-right uk-position-center-right" data-uk-search-icon></span>
        <input type="search" name="searchword" id="mod-ajaxsearch-searchword_<?php echo $module_id; ?>" placeholder="<?php echo $label; ?>" class="uk-background-muted uk-input uk-border-rounded font f600" value="<?php echo $text; ?>" autocomplete="off" onblur="if (this.value=='') this.value='<?php echo $text; ?>';" onfocus="if (this.value=='<?php echo $text; ?>') this.value='';" />
        <!-- </div> -->
        <?php if ($button) : ?>
          <div>
            <!-- <input type="submit" name="Search" class="search-submit" value="<?php //echo JHtml::tooltipText($button_text);?>"> -->
            <a  class="search_class" target="_blank" href="<?php echo JUri::base().'index.php?option=com_search&view=search'; ?>"><?php echo JHtml::tooltipText($button_text);?></a>
          </div>
        <?php endif; ?>
      </div>
      <div class="search-results">
          <div class="is_ajaxsearch_result_<?php echo $module_id; ?> ajaxsearch_result" id="is_ajaxsearch_result"></div>
      </div>
    </form>
  </div>
  <script type="text/javascript">
    var width = jQuery('.is_ajaxsearch_result_<?php echo $module_id; ?>').width();
  // alert(width);
  if(width <= 550){
    jQuery('.is_ajaxsearch_result_<?php echo $module_id; ?>').addClass('right-side-desc');
  }
  var label_width = jQuery('.ajaxsearch_<?php echo $module_id; ?> .search-label').width();
  // console.log(label_width);
  if(label_width!=null){
    label_width_total = label_width + 10;
    jQuery('.is_ajaxsearch_result_<?php echo $module_id; ?>').css('margin-left', label_width_total+'px');
  }
</script>

