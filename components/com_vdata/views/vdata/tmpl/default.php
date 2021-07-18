<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation'); 
JHTML::_('behavior.modal');
JHtml::_('behavior.colorpicker');
require_once JPATH_COMPONENT.'/operating/drawchart.php';
$document =  JFactory::getDocument();
$document->addScript("https://www.google.com/jsapi");
  $mem_usage = memory_get_usage(true); 
  $mem_usage = round($mem_usage/1024,2);
  
$user = JFactory::getUser();

$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
$canViewImport = $user->authorise('core.access.import', 'com_vdata');
$canViewExport = $user->authorise('core.access.export', 'com_vdata');
$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');
$canCreateWidget = $user->authorise('core.widget', 'com_vdata');
$canEdit = $user->authorise('core.edit', 'com_vdata');
$canDelete = $user->authorise('core.delete', 'com_vdata');
 ?>

<script type="text/javascript">
var infor_memory_response=<?php echo  $mem_usage; ?>;
var infor_memory_response_text = <?php echo  $mem_usage; ?>;
var infor_cpu_response=0;
var infor_response_time=0;
var infor_cpu_name = '';
var server_cpu_load = '';
var thread_connected = 0;
var thread_running = 0;
var source;
var check_status = '';
var status_action = '';
var status_index = 0;
function eventsourcing()
{
 if(typeof(EventSource) !== "undefined") {
   
   source = new EventSource("<?php echo "index.php?option=com_vdata&view=vdata&task=live_chart_data_test";?>");
	
    source.onmessage = function(event) {
	   
		var res = jQuery.parseJSON(event.data);
	  
		var st = '';
	for(var i=0;i<res[1].length;i++){ 
			 st += '<td>'+res[1][i].Value+'<\/td>';
			 if(i==8){
				thread_connected = parseInt(res[1][i].Value);}
				 if(i==9){
				 thread_running = parseInt(res[1][i].Value);}
        } 	
		//jQuery('table.result2 tr:last').after('<tr class="child">'+st+'</tr>');
		   
			  
		     infor_cpu_name = res[2].cpu_info; 
			 infor_cpu_response = Math.round(res[2].cpu_load);
			 var memory_status = formate_check(res[2].memory_status);//data.info_memory;
			 infor_memory_response = Math.round(memory_status[0],1);
			 infor_memory_response_text = "%";
			 infor_response_time = res[3];
			
	  var single_value_display = res[4];
	  jQuery('.innser_single_trigger').each(function(index, value) { 
	  var st_text =	'';	 //alert(index);  
	  st_text = single_value_display[index];
	  jQuery(this).html(single_value_display[index]);
	  
	  }); 
	  var listing_info_display = res[5];
	  jQuery('table.listing_info').each(function(index, value) {  
	  jQuery('table#'+jQuery(this).attr('id')+' tr').slice(1).remove();
	  jQuery('table#'+jQuery(this).attr('id')+' tbody').prepend(listing_info_display[index]);
	  
	  
	  });	
    };
	
  } 
  }
function livechart()
		{
	   var start_time = new Date().getTime();
	    jQuery.getJSON( "<?php echo JRoute::_("index.php?option=com_vdata&view=vdata&task=live_chart_data",false);?>", function(data){
               
			var end_time = new Date().getTime();
             var request_time = end_time - start_time;
			 infor_response_time = request_time;
			 
			 var json = jQuery.parseJSON(data.json);
			//data.info_memory;
			 infor_memory_response = json.Memory.attributes.Percent;
			 infor_memory_response_text = '%';
			 infor_cpu_name = json.Hardware.CPU.CpuCore[0].attributes.Model; 
			 infor_cpu_response = Math.round(json.Vitals.attributes.CPULoad);
			 var memory_status = formate_check(json.Memory.attributes.Percent);//data.info_memory;
			 infor_memory_response = Math.round(memory_status[0],1);
			 infor_memory_response_text = "%";
			 });
          }
function formate_check(bytes){
	var show='';var show_text='';
	if (bytes > Math.pow(1024, 5)) {
            show += Math.round(bytes / Math.pow(1024, 5), 2);
            show_text += "<?php echo JText::_('PiB');?>";
        }
        else {
            if (bytes > Math.pow(1024, 4)) {
                show += Math.round(bytes / Math.pow(1024, 4), 2);
                show_text += "<?php echo JText::_('TiB');?>";
            }
            else {
                if (bytes > Math.pow(1024, 3)) {
                    show += Math.round(bytes / Math.pow(1024, 3), 2);
                    show_text += "<?php echo JText::_('GiB');?>";
                }
                else {
                    if (bytes > Math.pow(1024, 2)) {
                        show += Math.round(bytes / Math.pow(1024, 2), 2);
                        show_text += "<?php echo JText::_('MiB');?>";
                    }
                    else {
                        if (bytes > Math.pow(1024, 1)) {
                            show += Math.round(bytes / Math.pow(1024, 1), 2);
                            show_text += "<?php echo JText::_('KiB');?>";
                        }
                        else {
                            show += bytes;
                            show_text += "<?php echo JText::_('B');?>";
                        }
                    }
                }
            }
        }
	var show_array = new Array();
	show_array[0] =show; 
	show_array[1] =show_text; 
	return show_array;
}
jQuery(document).ready(function(){ 
	   status_index = parseInt(jQuery('ul.connectedsortable > li').length)+1;
	   var timer = '';
	// jQuery( ".dragable" ).draggable({containment: "parent"});
	   eventsourcing();
	  <?php if($canCreateWidget){?>
      jQuery( "#sortable1, #sortable2" ).sortable({
      connectWith: ".connectedsortable",
	  containment: "parent",
	  cancel : '.widget_chart, .profile_mid_data_inner, .listing_layout_others',
	  start: function(event, ui ){
		  clearInterval(timer);
		  jQuery('li.ui-sortable-placeholder').css("width", (ui.item.width()-1)+"px");
	  if(typeof(EventSource) !== "undefined") { 
	   if(source !== "undefined") { 
	   source.close();}
      }},
	  stop: function( event, ui ) {
         timer = setTimeout(function() {
		 var ordering = new Array();
		 jQuery('.common_profile_main').each(function(index, value) { 
		   ordering.push(jQuery(this).attr('data-ordering-profile'));	 
		 });
		  jQuery.ajax({
			
						url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'vdata', 'task':'update_profile_ordering', 'new_ordering':ordering, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						success: function(res)	{
						eventsourcing();	
						},
						error: function(jqXHR, textStatus, errorThrown)	{
						alert(textStatus);				  
						}
					
	   });},3000);
		  }
    }).disableSelection();
	<?php }?>
	
 
 SqueezeBox.loadModal = function(modalUrl,handler,x,y) {
        this.presets.size.x = 1024;
        this.initialize();      
        var options = {handler: 'iframe', size: {x: "100%", y: "100%"}
		};      
        this.setOptions(this.presets, options);
        this.assignOptions();
        this.setContent(handler,modalUrl);
    }; 

	 
});
function insert()
	 {
	      jQuery.ajax({
				url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'vdata', 'task':'check_login_status', "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{ 
						jQuery('.loading').show();	
						},
						complete: function()	{
						jQuery('.loading').hide();	
						},
						success: function(res)	{
							if(res.result == "success"){
								if(res.state){
								var url = "<?php echo JRoute::_("index.php?option=com_vdata&view=widget&tmpl=component",false);?>";
								SqueezeBox.loadModal(url,"iframe",'95%','95%');
									
								}
								if(!res.state){
								window.location = '<?php echo JRoute::_('index.php?option=com_users&view=login',false);?>';
									
								}
							}
						},
						error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);
							SqueezeBox.close();
							window.location = '<?php echo JRoute::_('index.php?option=com_users&view=login');?>';
						}
						
							
			});
	
			
		
			
		}
SqueezeBox.initialize({
	onOpen:function(){
			
				
		   jQuery( "#system-message-container" ).html('');
            jQuery("html, body").animate({scrollTop : 0}, "slow");
		 },
    onClose: function() {
	jQuery('#system-message-container').html(''); 
	//jQuery('body').scrollTop(jQuery('ul.connectedsortable li:nth-child('+parseInt(status_index)+'n)').position().top)
	if(status_index<parseInt(jQuery('ul.connectedsortable > li').length)+1){
	 jQuery('html,body').animate({ scrollTop: jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+'n)').offset().top-170}, 2000);	
	}
	else if(check_status=='action_saved')
		jQuery('html,body').animate({ scrollTop: jQuery('ul.connectedsortable li:nth-child('+parseInt(jQuery('ul.connectedsortable > li').length)+'n)').offset().top}, 2000); 
	
	if(check_status=='action_saved'){
       jQuery.ajax({        
						url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'vdata', 'task':'update_dashboard_new','status_index':status_index,'status_action':status_action, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+')').children(':first').before('<div class="vdata_overlay" style="display:block;"><img alt="" src="<?php echo JURI::root();?>media/com_vdata/images/loading_second.gif" class="vdata-loading"></div>');
							},
							complete: function()	{
							//jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+')').children(':first').remove();
							},
							success: function(res)	{
							if(res.result == "success"){
							
							if(status_index<parseInt(jQuery('ul.connectedsortable > li').length)+1){
								//jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+')').html();
								jQuery('ul.connectedsortable').children('li:eq('+(parseInt(status_index))+')').html('');
								   var style = res.style.split(":");
									jQuery('ul.connectedsortable').children('li:eq('+(parseInt(status_index))+')').width( style[0]).height(style[1]);
								jQuery('ul.connectedsortable').children('li:eq('+(parseInt(status_index))+')').html(res.html+res.script);
							    /* jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+')').html(res.html);
								jQuery('ul.connectedsortable li:nth-child('+(parseInt(status_index)+1)+')').append(res.script); */
								}
								else{
									
								jQuery('ul.connectedsortable').append(res.li+res.html+res.script+'</li>');
								
								}
								var status_text_action = 'drawchart'+status_action;
							
							jQuery('.widget_chart').each(function(index,value){
								
							if(status_text_action==jQuery(this).attr('data-profile-id')){
							eval(status_text_action+"()");
							
							}
								
							}); 
								
								
								
								
								
								//eval(res.script);
								/* 
							jQuery('#content').find('.span12').first().html(res.html);
							if(jQuery('#j-sidebar-container').length>0){
						    jQuery('#j-sidebar-container').removeClass('span2');
							
							jQuery('#j-sidebar-container').addClass('j-sidebar-container j-toggle-transition j-sidebar-visible');
						    jQuery('#j-main-container').addClass('j-toggle-main j-toggle-transition')
							jQuery('#j-toggle-button-wrapper').addClass('j-toggle-transition j-toggle-visible')
							}*/
							
							
							
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}
			 });
	    }
    }
});  
function edit_widget(e){
	
	       status_action = jQuery(e).attr('data-widget-id');
		   status_index = jQuery(e).parents('li').index();
			var url = "<?php echo JRoute::_("index.php?option=com_vdata&view=widget&tmpl=component",false)?>&cid[]="+jQuery(e).attr('data-widget-id'); 
            SqueezeBox.loadModal(url,"iframe",'95%','95%');

}
function delete_widget(e){
	var to_delete_li = jQuery(e).parents('li');
	status_action = jQuery(e).attr('data-widget-id');
	var to_delete_li_loading = jQuery(e).parents('li :first-child');
	if(jQuery(e).attr('data-widget-id')==''){alert("<?php echo Jtext::_('COM_VDATA_WIDGET_PLEASE_SELECT');?>"); return;}else{
		 var tester = confirm("<?php echo Jtext::_('COM_VDATA_WIDGET_WANT_TO_DELETE');?> !");
		}
	if(tester==true){
	jQuery.ajax({
		                url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'vdata', 'task':'delete_widget','id':jQuery(e).attr('data-widget-id'), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							to_delete_li_loading.before('<div class="vdata_overlay" style="display:block;"><img alt="" src="<?php echo JURI::root();?>media/com_vdata/images/loading_second.gif" class="vdata-loading"></div>');
							},
							complete: function()	{
							to_delete_li.find('div:first').remove(); 
							},
							success: function(res)	{
							if(res.result == "success"){
							to_delete_li.remove();
							
							
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
			
			
		});	
	}
		
	}


/* window.SqueezeBox.initialize({
         onOpen:function(){ 
		
jQuery("html, body").animate({scrollTop : 0}, "slow");
		 }
	}); */
	

</script>

<style>
#sbox-window {
	left: 5% !important;
    padding: 1.5% !important;
    top: 10% !important;
    width: 87% !important;
}
</style>
<div id="vdatapanel">
<div class="vdata_overlay" style="display:none;"> 
<img class="vdata-loading" src="<?php echo JURI::root();?>/media/com_vdata/images/loading_second.gif" alt="">
</div>
<form action="index.php?option=com_vdata" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div id="toolbar" class="toolbar-btn dash-btn">
<span class="hx_title"><img src="<?php echo JURI::root();?>/media/com_vdata/images/vdata-logo.png" alt="vData"> <span class="hx_main_title"><?php echo JText::_( 'VDATA_DASHBOARD' );?><br><span class="hx_subtitle"><?php echo JText::_( 'VDATA_SUBTITLE' );?></span></span></span>
<div class="hx_dash_button">
<?php if($canCreateWidget){?>
<a 
    href="javascript:void(0);" 
    class="btn btn-small btn-widget" onclick="insert();">
       <span class="icon-new icon-white"></span><?php echo JText::_( 'VDATA_ADD_WIDGET' );?>
</a>
<?php }?>
<?php if($canViewImport){?>
<a 
    href="<?php echo JRoute::_('index.php?option=com_vdata&view=import');?>" 
    class="btn btn-small btn-import">
       <span class="icon-import icon-white"></span><?php echo JText::_( 'VDATA_IMPORT' );?>
</a>
<?php }?>
<?php if($canViewExport){?>
<a 
    href="<?php echo JRoute::_('index.php?option=com_vdata&view=export');?>" 
    class="btn btn-small btn-export">
       <span class="icon-out-2 icon-white"></span><?php echo JText::_( 'VDATA_EXPORT' );?>
</a>
<?php }?>
<?php if($canViewProfiles){?>
<a 
    href="<?php echo JRoute::_('index.php?option=com_vdata&view=profiles');?>" 
    class="btn btn-small btn-profile">
       <span class="icon-stack icon-white"></span><?php echo JText::_( 'VDATA_PROFILE' );?>
</a>
<?php }?>
<?php if($canViewCronFeed){?>
<a 
    href="<?php echo JRoute::_('index.php?option=com_vdata&view=schedules');?>" 
    class="btn btn-small btn-feed active">
       <span class="icon-feed icon-white"></span><?php echo JText::_( 'VDATA_SCHEDULES' );?>
</a>
<?php }?>
</div>
</div>

<div class="clr">
</div>
<div class="clr" style="clear:both;">
</div>

<div class="profile-section">
<ul id="sortable1" class="connectedsortable">
<?php
                /* $data = array('dashboard' => 'dashboard');
                $postString = http_build_query($data, '', '&');
				$ch = curl_init(); 

				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,"http://okhlites.com/demo/CustomQuizWebsite/new_stockdata.php");
                 
				curl_setopt($ch,CURLOPT_POST,1);

				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_data = json_decode(curl_exec($ch)); */

$k = 0;
	
	$lang = JFactory::getLanguage();
	$prev_current_width ='';
	$future_width ='';
	$live_data_query = array('Server Response Monitoring','Server CPU Monitoring','Server Monitoring','Thread Status','Queries Status');
	$row_siz = $this->configuration->row_limit;
	$one_column_size = 100/$this->configuration->column_limit;
	
	for($j=0;$j<count($this->profiles);$j++){
		$profile = json_decode($this->profiles[$j]->detail);
		
		$current_width ='';
		$sub_current_width =0;$box_class_name='';
		if(isset($profile->box_layout) && $profile->box_layout=="onebox"){$sub_current_width=1;$box_class_name=' onebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="twobox"){$sub_current_width=2;$box_class_name=' twobox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="threebox"){$sub_current_width=3;$box_class_name=' threebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="fourbox"){$sub_current_width=4;$box_class_name=' fourbox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="fivebox"){$sub_current_width=5;$box_class_name=' fivebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="sixbox"){$sub_current_width=5;$box_class_name=' sixbox';}
			
			$column_widget_width_value = '';
		    $row_widget_height_value = '';
		    $row_widget_height_value_chart = '';
		if(isset($profile->box_column) && $profile->box_column)
		{
		 $column_widget_width_value = (($profile->box_column*$one_column_size)-2).'%';	
		}
		
	    if(isset($profile->box_row) && $profile->box_row)
		{
		$row_widget_height_value =	(($profile->box_row*$row_siz)-20).'px';
		$row_widget_height_value_chart = (($profile->box_row*$row_siz)-20);
		}
			
			if(empty($prev_current_width))
			{
			$prev_current_width = $current_width;	
			}
			$current_width ='';
	  if(isset($profile->style_layout) && $profile->style_layout=='single_formate')
	  { ?>
	  <li class="common_profile_main single_formate num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';} echo $box_class_name;?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
		<div class="panel_header">
		<?php if($canCreateWidget){?>
		<?php if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		echo '<span class="profile_name">'.$this->profiles[$j]->name.'</span>';?>
		<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-delete"></i></span>
		<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-edit"></i></span>
		<?php }?>
		</div>
		<div class="profile_mid_data">
	  <?php 
		
		  $single_data = vchart::widgetvalue($profile);
		if(isset($single_data->result)&&$single_data->result=='error'){
			  echo $single_data->error;
		  }
		  else
		  {
		  $single_data = isset($single_data->data)?$single_data->data:array();
		  $test_v = isset($profile->style_layout_editor)?$profile->style_layout_editor:'';
		    $regex		= '/{(.*?)}/i';
		    preg_match_all($regex, $profile->style_layout_editor, $matches, PREG_SET_ORDER);
			
			 foreach ($matches as $match)
			{
			foreach ($single_data as $key=>$value)
			{ 
			     
				 if(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value!=null&&$match[1]==$key)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profiles[$j]->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
	 
				
				}
				elseif(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value==null)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profiles[$j]->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
				}
            }
		  }			
           echo $test_v;
		  }
		 ?>
		 </div>
		  </li>
		  <?php
	  }
	  elseif(isset($profile->style_layout) && $profile->style_layout=='listing_formate')
	  {
	  if($profile->existing_database_table=='vData Profiles'){
		  ?>
		  <li class="common_profile_main num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
	<div class="panel_header">
	<?php 
	echo '<span class="profile_name">';
	if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		echo $this->profiles[$j]->name.'</span>';
	echo '</span>';
	?>
	<?php if($canCreateWidget){?>
	<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-delete"></i></span>
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-edit"></i></span>
	<?php }?>
	</div>
	<div class="profile_mid_data common_class_listing_format">
	<?php 
	$profile_style = $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-63).'px"':'';
	echo '<ul class="profile_mid_data_inner"'.$profile_style.'>';
	?>
	     <?php $c=0;if(isset($profile->profile_creation_button)&&$profile->profile_creation_button==1) {
			 $c++; 
			 ?>
		  <li class="common_profile num1 profile-section-listing">
		  <a href="index.php?option=com_vdata&view=profiles&layout=wizard"><img src="<?php echo Juri::root();?>media/com_vdata/images/add-new-profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_('COM_VDATA_CREAT_PROFILE');	?></span></a>
		  </li>
		  <?php
		 }
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
			
			$row = $this->items[$i];
				if(in_array(JText::_("COM_VDATA_SELECT_ALL_PROFILES"),$profile->profiles) || in_array($row->id,$profile->profiles)){
				
				?>
				
			   <li class="common_profile num<?php echo (++$c); ?> profile-section-listing" data-ordering-profile="<?php echo $row->id.':'.$row->ordering; ?>" style="<?php echo $sub_current_width;?>">
					
					<?php if($row->iotype) : ?>
					<a href="index.php?option=com_vdata&view=profiles&task=edit&cid[]=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
					<?php else : ?>
					<a href="index.php?option=com_vdata&view=profiles&task=edit&cid[]=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/import_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
					
					<?php endif; ?>
					
				   
				</li>
				<?php
				$k = 1 - $k;
				}
			}
          echo '</ul></li>';			
		}
		elseif($profile->existing_database_table=='vData Plugins'){
			?>
		  <li class="common_profile_main num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
	<div class="panel_header">
	<?php 
	echo '<span class="profile_name">';
	if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		echo $this->profiles[$j]->name;
	echo '</span>';
	?>
	<?php if($canCreateWidget){?>
	<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-delete"></i></span>
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-edit"></i></span>
	<?php }?>
	</div>
	<div class="profile_mid_data common_class_listing_format">
	
	<ul>
		  
		  <?php
				for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
				{

					$row = $this->plugins[$i];
					if(in_array(JText::_("COM_VDATA_SELECT_ALL_PLUGINS"),$profile->plugins) || in_array($row->extension_id,$profile->plugins)){

					//$lang->load($this->items[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$this->items[$i]->element, null, false, false);

					?>

					<li class="common_profile_plugin num<?php echo ($i+1) ?>" data-ordering-profile="<?php echo $row->extension_id.':'.$row->name; ?>" style="<?php echo $sub_current_width;?>">


					<a href="index.php?option=com_vdata&view=profiles&extension_id=<?php echo $row->extension_id;?>&task=edit&cid[]=0"><img src="media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->name);	?></span></a>


					</li>
					<?php
					$k = 1 - $k;
					}
				}
		 echo '</ul></li>';	
		}
	  else{
	  
	  ?>
	   <li class="common_profile_main listing_formate num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
		<div class="panel_header">
		<?php 
		echo '<span class="profile_name">';
		if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		 echo $this->profiles[$j]->name;
	     echo '</span>';
		 ?>
		<?php if($canCreateWidget){?>
		<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-delete"></i></span>
		<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-edit"></i></span>
		 <?php }?>
		</div>
		<div class="profile_mid_data common_class_listing_format">
	  <?php 
		  
		  $single_data = vchart::widgetvalue($profile);
		   if(isset($single_data->result)&&$single_data->result=='error'){ 
			  echo $single_data->error;
		  }
		  else
		  {
	      $single_data = $single_data->data;
		  $object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
		  $object_array = count($object_array)>0?array_keys($object_array):array();
		  echo '<div class="listing_layout_others" style="height:'.($row_widget_height_value_chart-60).'px;">';
		   echo '<table class="adminlist table table-hover listing_info listing_info_'.$this->profiles[$j]->id.'" width="100%"><thead><tr>';
			for ($s = 0;$s<count($object_array);$s++) 
			{
			    
				  echo '<th>'.$object_array[$s].'</th>';
				
			
            }			
           echo '</thead>';
		   $g = 0;
			for ($l=0; $l < count( $single_data ); $l++)
			{
				$listing = $single_data[$l];
				echo '<tr>';
				foreach($listing as $listings){
				echo '<td>'.$listings.'</td>';	
				}
			echo '</tr>';	
			}
		  echo '</table>';
		  echo '</div>';
		  }
		 ?>
		 </div>
		  </li>
		<?php 
        }		
	  }
	 else
	 { 
			
		?>
	<li class="common_profile_main num<?php echo ($j+1);if($this->profiles[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $this->profiles[$j]->id.':'.$this->profiles[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
	<div class="panel_header">
	<?php 
	echo '<span class="profile_name">';
	if(isset($this->profiles[$j]->name)&& $this->profiles[$j]->name!='')
		echo $this->profiles[$j]->name.'';
	echo '</span>';
	?>
	<?php if($canCreateWidget){?>
	<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-delete"></i></span>
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="<?php echo $this->profiles[$j]->id;?>"><i class="icon-edit"></i></span>
	<?php }?>
	</div>
	<div class="profile_mid_data">
	
	<ul>
	
	<?php
	if($this->profiles[$j]->datatype_option=='profile'){
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        
		$row = $this->items[$i];
		if((isset($profile->show_all_profile) && $profile->show_all_profile==1) || in_array($row->id,$profile->existing_database_table)){
		
        ?>
		
       <li class="common_profile num<?php echo ($i+1) ?> profile-section-listing" data-ordering-profile="<?php echo $row->id.':'.$row->ordering; ?>" style="<?php echo $sub_current_width;?>">
	        
            <?php if($row->iotype) : ?>
            <a href="index.php?option=com_vdata&view=profiles&task=export&profileid=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
            <?php else : ?>
			<a href="index.php?option=com_vdata&view=import&profileid=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/import_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
			
            <?php endif; ?>
            
           
        </li>
        <?php
        $k = 1 - $k;
       }
      }
	} 
   elseif($this->profiles[$j]->datatype_option=='custom_plugin')
   {
    for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
    {
        
		$row = $this->plugins[$i];
		if((isset($profile->show_all_plugin) && $profile->show_all_plugin==1) || in_array($row->extension_id,$profile->existing_database_table)){
		
		//$lang->load($this->items[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$this->items[$i]->element, null, false, false);
		
        ?>
		
       <li class="common_profile_plugin num<?php echo ($i+1) ?>" data-ordering-profile="<?php echo $row->extension_id.':'.$row->name; ?>" style="<?php echo $sub_current_width;?>">
	        
            
            <a href="index.php?option=com_vdata&view=profiles&extension_id=<?php echo $row->extension_id;?>&task=edit&cid[]=0"><img src="media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->name);	?></span></a>
            
           
        </li>
        <?php
        $k = 1 - $k;
       }
      }
	}	
   elseif($this->profiles[$j]->datatype_option=='predefined')
   {  
    
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';
	  if(isset($this->profiles[$j]->chart_type) && $this->profiles[$j]->chart_type!=''&& !in_array($profile->existing_database_table,$live_data_query)){
	      
			$script_data = vchart::draw_view_chart($this->profiles[$j]);
			 if($script_data->result=='success'){ 
			  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'"'.$style.'>';
			 echo ' <div id="widget_'.$this->profiles[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profiles[$j]->id.'" '.$style.'></div></li>';
			
			 echo '<script type="text/javascript"> ';
			 echo $script_data->scripts;
			 echo '</script>';
			 
			 
			  } 
			  elseif($script_data->result=='error'){
				 $script_data->error; 
			  }
          }
		elseif(in_array($profile->existing_database_table,$live_data_query)){
		 $script_data = vchart::draw_live_chart($this->profiles[$j]);
		  if($profile->existing_database_table=='Server Monitoring'){?>
		  
<div id="tabs<?php echo $this->profiles[$j]->id;?>">
  <ul>
    <li data-chart-id="<?php echo $this->profiles[$j]->id;?>" data-show-chart="no"><a href="#tabs-1">Processes</a></li>
    <li data-chart-id="<?php echo $this->profiles[$j]->id;?>" data-show-chart="yes" data-chart-for="ram"><a href="#tabs-2">RAM Status</a></li>
    <li data-chart-id="<?php echo $this->profiles[$j]->id;?>" data-show-chart="yes" data-chart-for="cpu"><a href="#tabs-3">CPU Status</a></li>
  </ul>
  <div id="tabs-1">
    <table class="adminlist table table-striped table-hover" width="100%"><tr><th>Process Name</th><th>Process Type</th><th>Time Taken </th></tr></table></div>
  <div id="tabs-2"  style="width: 100%; height: 100%;" data-chart-id="<?php echo $this->profiles[$j]->id;?>" data-show-chart="yes">
   <div id="widget_sever_2<?php echo $this->profiles[$j]->id;?>"  style="width: 100%; height: 100%;"></div>
  </div>
  <div id="tabs-3" data-chart-id="<?php echo $this->profiles[$j]->id;?>" data-show-chart="yes">
    <div id="widget_sever_3<?php echo $this->profiles[$j]->id;?>"  style="width: 100%; height: 100%;"></div>
  </div>
</div>  
		  <?php 
		  echo '<script type="text/javascript"> ';
					 echo $script_data;
					  echo '</script>';
		  }else
		        {
			  
					 if($script_data!='no_chart')
					{ 
					  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'"'.$style.'>';
					  echo ' <div id="widget_'.$this->profiles[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profiles[$j]->id.'"  style="width: 100%; height: 100%;"></div></li>';
					
					  echo '<script type="text/javascript"> ';
					  echo $script_data;
					  echo '</script>';
					}
			 
			  }		
		} 
		
	  else { 
		  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'"'.$style.'>'.$this->log_information($this->profiles[$j],$response_data).'</li>';
	     }	  
      
   }
   elseif($this->profiles[$j]->datatype_option=='writequery'){
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';
	$script_data = vchart::draw_view_chart($this->profiles[$j]);
     if($script_data->result=='success'){ 
	  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$this->profiles[$j]->id.':'.$this->profiles[$j]->ordering.'" '.$style.'>';
	 echo ' <div id="widget_'.$this->profiles[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profiles[$j]->id.'"  '.$style.'></div></li>';
	
	 echo '<script type="text/javascript"> ';
	 echo $script_data->scripts;
	  echo '</script>'; 
	 }	
	 elseif($script_data->result=='error'){
		 echo $script_data->error; 
	 }	
   }
   
   ?></ul></li><?php
     }
	
	}
 ?>
 
 </ul>
</div>
</div>

<div class="clr" style="clear:both;"></div>
<input type="hidden" name="option" value="com_vdata" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="view" value="" />
	</form>
	