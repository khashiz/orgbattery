<?php 
/**
 * @package Module EB Ajax Search for Joomla!
 * @version 1.5: mod_ebajaxsearch.php Sep 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/
defined('_JEXEC') or die;
include_once __DIR__ . '/helper.php';
$search_in_article= $params->get('search_in_article');
$by_ordering      = $params->get('by_ordering','');
$show_title       = $params->get('show_title', 1);
$show_description = $params->get('show_description', 1);
$show_image       = $params->get('show_image', 1);
$catids           = $params->get('catid');
$search_in_k2     = $params->get('search_in_k2');
$k2catid          = $params->get('k2catid');
$search_in_hikashop     = $params->get('search_in_hikashop');
$hikashopcatid          = $params->get('hikashopcatid');

$search_in_sppage = $params->get('search_in_sppage', 0);
$spcatid           = $params->get('spcatid');

$catidss = '';
if(isset($catids)){
  $catidss = implode($catids, ',');
}
$k2catids = '';
if(isset($k2catid)!=''){
    if(count($k2catid)>0){
      $k2catids = implode($k2catid, ',');
    }
}
$hikashopcatids = '';
if(isset($hikashopcatid)!=''){
    if(count($hikashopcatid)>0){
      $hikashopcatids = implode($hikashopcatid, ',');
    }
}

$spcatids = '';
if(isset($spcatid)!=''){
    if(count($spcatid)>0){
      $spcatids = implode($spcatid, ',');
    }
}


$button       = $params->get('button', 0);
$module_id = $module->id;
// $max_results  = (int) $params->get('max_results', 5);

$doc = JFactory::getDocument();
$js='';

$js .= <<<JS
jQuery(document).ready(function(){
    jQuery("#mod-ajaxsearch-searchword_$module_id").on("keyup", function(){
      // console.log(jQuery(this).val());
        jQuery(this)[tog(this.value)]('x');
        var value   = jQuery(this).val();
        if(value.length > 2){  
            request = {
                    'option' : 'com_ajax',
                    'module' : 'ebajaxsearch',
                    'data'   : { search_in_article:"$search_in_article", keyword: value, order: "$by_ordering", title: "$show_title", description: "$show_description", image: "$show_image", catids: "$catidss", search_in_k2: "$search_in_k2", k2catid: "$k2catids", search_in_hikashop: "$search_in_hikashop", hikashopcatid: "$hikashopcatids", search_in_sppage: "$search_in_sppage", spcatid: "$spcatids"},
                    'format' : 'raw'
                };
            jQuery.ajax({
                type   : 'POST',
                data   : request,
                success: function (response) {
                  // console.log(response);
                  var data_response = replaceNbsps(response);
                  jQuery('.is_ajaxsearch_result_$module_id').html(data_response);
                  jQuery('.is_ajaxsearch_result_$module_id').ebajaxsearchhighlight( value );
                  jQuery('#pageCover').fadeIn(500);
                  jQuery('header').addClass('searchActive');
                }
            });
            return false;
        } else {
            jQuery('.is_ajaxsearch_result_$module_id .result_wrap').hide();
            jQuery('#pageCover').fadeOut(500);
            jQuery('header').removeClass('searchActive');
        }
    });
});
    jQuery(document).click(function() {
        var obj = jQuery("header");
        if (!obj.is(event.target) && !obj.has(event.target).length) {
            jQuery('.ajaxSearchForm input').val("");
            jQuery('.is_ajaxsearch_result_$module_id .result_wrap').hide();
            jQuery('#pageCover').fadeOut(500);
            jQuery('header').removeClass('searchActive');
        }
    });
JS;
/* } */
$js .= <<<JS

function tog(v){return v?'addClass':'removeClass';} 
jQuery(document).on('input', '.clearable', function(){
    jQuery(this)[tog(this.value)]('x');
    }).on('mousemove', '.x', function( e ){
        jQuery(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');   
    }).on('click', '.onX', function( ev ){
            ev.preventDefault();
            var form_id = jQuery(this).closest('form').attr('id');
            var div_id = jQuery("#"+form_id).parent('div').attr('id');
            jQuery('#'+div_id+' .is_ajaxsearch_result_$module_id .result_wrap').hide();
            jQuery(this).removeClass('x onX').val('').change();
            var value   = jQuery(this).val();
            request = {
                'option' : 'com_ajax',
                'module' : 'ebajaxsearch',
                'data'   : { search_in_article:"$search_in_article", keyword: value, order: "$by_ordering", title: "$show_title", description: "$show_description", image: "$show_image", catids: "$catidss", search_in_k2: "$search_in_k2", k2catid: "$k2catids", search_in_hikashop: "$search_in_hikashop", hikashopcatid: "$hikashopcatids"},
                'format' : 'raw'
            };
            jQuery.ajax({
                type   : 'POST',
                data   : request,
                success: function (response) {
                    // alert(response);
                    jQuery('#'+div_id+' .is_ajaxsearch_result_$module_id').html(response);
                }
            });
            return false;
    });
JS;

$doc->addScriptDeclaration($js);
require JModuleHelper::getLayoutPath('mod_ebajaxsearch');