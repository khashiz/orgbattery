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
$labelclass = '';
if($label!='' || $button!='0'){
  $labelclass = "is_btn_search";
}
$btn_width = '';
if($width){
  $btn_width = $width;
  // echo $btn_width;
} 
if($label!=''){
  $btn_width +=  15;
} 
if($button!='0'){
  $btn_width += 20;
}
$css_class = "";
if($label!='' || $button!='0'){
  // $css_class .= '.is_btn_search.ajaxsearch_'.$module_id.'{width:'.$btn_width.'%;}';
  $css_class .= '.is_btn_search.ajaxsearch_'.$module_id.' .btn-toolbar input[type="search"]{width:'.$width.'%;}';
  $css_class .= '.is_btn_search.ajaxsearch_'.$module_id.' .ajaxsearch_result .result-element, .is_btn_search.ajaxsearch_'.$module_id.' .search-results .ajaxsearch_result{width:'.$width.'%;}';
} else {
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result .result-element, .ajaxsearch_'.$module_id.' .search-results .ajaxsearch_result{width:100%;}';
  $css_class .= '.ajaxsearch_'.$module_id.'{width:'.$width.'%; }';
}
if($label!=''){
  $css_class .= '.is_btn_search.ajaxsearch_'.$module_id.' .search-label{max-width: 15%;
    vertical-align: middle;}';
}
if($button!='0'){
  // $css_class .= '.is_btn_search.ajaxsearch_'.$module_id.' .btn-group{width:20%;}';
}
if($label != ''){
  $css_class .= '#is_ajaxsearch_result{ margin-left:17%; }';
}
/*if($style_effect == 'shadow'){
  $css_class .= '.ajaxsearch_'.$module_id.' .search-results .ajaxsearch_result .result_wrap{ box-shadow: 0 0 3px 0px '.$box_shadow_color.'; box-shadow: 0 0px 20px 5px '.$box_shadow_color.';}';
}*/

if($button_background_color != ''){
  $css_class .= '.ajaxsearch_'.$module_id.' .search_class{ background : '.$button_background_color.';}';
} else {
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result .search_class{ background: #f6f6f6;}';
}

if($button_text_color != ''){
  $css_class .= '.ajaxsearch_'.$module_id.' .search_class{ color : '.$button_text_color.';}';
} else {
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result .search_class{ color: #ffffff;}';
}

if($text_color != ''){
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result span{ color : '.$text_color.';}';
} else {
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result span{ color: #4e6170;}';
}
if($background_color != ''){
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result .result_wrap{ background : '.$background_color.';}';
} else {
  $css_class .= '.ajaxsearch_'.$module_id.' .ajaxsearch_result .result_wrap{ background: #ffffff;}';
}

$css_class .= '.ajaxsearch_'.$module_id.' .is_ajaxsearch_result_'.$module_id.'.right-side-desc#is_ajaxsearch_result .result-element span.small-desc{ width: 100%; }';
$css_class .= '.ajaxsearch_'.$module_id.' .is_ajaxsearch_result_'.$module_id.'#is_ajaxsearch_result .result-element.desc_fullwidth span.small-desc{ width: 100%; }';
?>
<style type="text/css">
  <?php echo $css_class; ?>
</style>
<div class="ajaxsearch_<?php echo $module_id; ?> is_ajaxsearch <?php echo $labelclass; ?>" id="ajaxsearch_<?php echo $module_id; ?>">
  <form id="mod-ajaxsearch-form-<?php echo $module_id; ?>" action="" method="post" class="form-inline">
    <div class="btn-toolbar">
      <!-- <div class="btn-group pull-left"> -->
        <?php if($label){ ?><label for="mod-search-searchword" class="search-label"><?php echo $label; ?></label><?php } ?>
        <input type="search" name="searchword" id="mod-ajaxsearch-searchword_<?php echo $module_id; ?>" placeholder="<?php echo $label; ?>" class="inputbox clearable" value="<?php echo $text; ?>" autocomplete="off" onblur="if (this.value=='') this.value='<?php echo $text; ?>';" onfocus="if (this.value=='<?php echo $text; ?>') this.value='';" />
        <!-- </div> -->
        <?php if ($button) : ?>
          <div class="btn-group">
            <!-- <input type="submit" name="Search" class="search-submit" value="<?php //echo JHtml::tooltipText($button_text);?>"> -->
            <a  class="search_class" target="_blank" href="<?php echo JUri::base().'index.php?option=com_search&view=search'; ?>"><?php echo JHtml::tooltipText($button_text);?></a>
          </div>
        <?php endif; ?>
        <div class="clearfix"></div>
      </div>
    </form>
    <div class="search-results">
      <div class="is_ajaxsearch_result_<?php echo $module_id; ?> ajaxsearch_result" id="is_ajaxsearch_result"></div>
    </div>
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

