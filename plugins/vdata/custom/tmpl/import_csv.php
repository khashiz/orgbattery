<?php
/*------------------------------------------------------------------------
# vData - custom Plugin
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2015 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
$operation = isset($this->profile->params->operation)?$this->profile->params->operation:0;
$keys = isset($this->profile->params->unqkey)?$this->profile->params->unqkey:array();
$images = (!empty($schedule_columns) && property_exists($schedule_columns, 'images'))?$schedule_columns->images:null;
?>

<script type="text/javascript">

Joomla.submitbutton = function(task) {
	if (task == 'close' || task == 'close_st') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else if(task == 'import_start'){
		var form = document.adminForm;
		
		function addOption(aname,avalue,values){
			var index;
			// Find and replace option if there
			for (index = 0; index < values.length; ++index) {
				if (values[index].name == aname) {
					values[index].value = avalue;
					break;
				}
			}
			// Add it if it wasn't there
			if (index >= values.length) {
				values.push({
					name: aname,
					value: avalue
				});
			}
		}
		
		function batchImport(offset){
				var values, index,pkvalue;
				var pkvalue='';
				$hd('tr.primaryKey').find('.field_type').each(function(){
				var primaryKey=$hd('tr.primaryKey').find(this).val();
				if(primaryKey=='')
				{
					pkvalue='blankPK';
					return false;
				}
				});
				if(pkvalue=='blankPK'){
				alert("<?php echo JText::_('VDATA_PROFILE_SELECT_PRIMARY_KEY_FIELD'); ?>");
				return false;		
				}
				values = $hd("#adminForm").serializeArray();
				addOption('task', 'import_start', values);
				addOption('limit', 1, values);
				addOption('offset', offset, values);
				values = jQuery.param(values);
				
				return $hd.ajax({
				url: "index.php",
				type: "POST",
				dataType: "json",
				async: true,
				cache:false,
				data: values,
				beforeSend: function()	{
					if(offset == 0){
						console.log('import start...');
					}
					else{
						console.log('import continue...');
					}
					$hd('div#hd_progress').show();
				},
				complete: function()	{
				},
				error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);				  
				} 
			}).done(function(resp){
				if(resp.result == 'success'){
					console.log(resp.offset);
					$hd('div#hd_progress').show();
					// var percentage = Math.round((resp.offset/resp.size)*100);
					var percentage = Math.round((resp.progress/resp.size)*100);
					var prog = percentage>100 ? 100 : percentage;
					// $hd('div#hd_progress').width(prog+"%");
					// setTimeOut(function(){$hd('div#hd_progress').width(prog+"%")},500);
					$hd('div#hd_progress').animate({width: prog+"%"}, 500, 'swing');
					
					batchImport(resp.offset);
				}
				else if(resp.result == 'error' && resp.hasOwnProperty("qty")) {
					<?php $session->clear('qty');
						$session->clear('totalRecordInFile');
						?>
					// var percentage = Math.round((resp.offset/resp.size)*100);
					var percentage = Math.round((resp.progress/resp.size)*100);
					var prog = percentage>100 ? 100 : percentage;
					// $hd('div#hd_progress').width(prog+"%");
					// setTimeOut(function(){$hd('div#hd_progress').width(prog+"%")},500);
					$hd('div#hd_progress').animate({width: prog+"%"}, 500, 'swing');
					//console.log(resp.totalRecordsInFile);
					console.log(resp.error);
					console.log('import complete...');
					$hd('div#hd_progress').hide();
					console.log(resp);
					var jmsgs = [resp.success_msg];
					Joomla.renderMessages({'message': jmsgs });//message,info
				}
				else {
					<?php $session->clear('qty');
						$session->clear('totalRecordInFile');
						?>
					console.log('import abort.');
					var jmsgs = [resp.error_msg,resp.error];
					Joomla.renderMessages({'error': jmsgs });
				}
			});
		}
		batchImport(0);
		return false;
	}
	else {
		var form = document.adminForm;
		Joomla.submitform(task, document.getElementById('adminForm'));
		return false;
					
	}
}
$hd(function()	{
		<?php if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1) ){echo "custom_quick();";}?>
		
		$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		
		$hd('input[name="quick"]').off("change").on('change', function(event)	{
			custom_quick();
		});
});

function custom_quick(){
	var ioquick = $hd('input[name="quick"]:checked').length;
	if(ioquick == 1){
		$hd('.col_block').html('<tr><td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td><tr>');
	}
	else{
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_csv_columns', 'profileid' : <?php echo $this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res)	{
				if(res.result == "success")  {
					$hd('.col_block').html(res.html);
					$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
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

function showValue(val,col){
	
	if(val=='directory'){
		$hd("#image_source_"+col).val("folder1/folder2/filepath/");
	}else if(val=='ftp'){
		var ftp = "ftp_host:FTP HOST|ftp_port:21|ftp_user:username|ftp_pass:password|ftp_directory:filepath/";
		$hd("#image_source_"+col).val(ftp);
	} else{
		$hd("#image_source_"+col).val("http://fileurl/");
	}
}
</script>

<?php  ?>

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'PROFILE_IMPORT_DETAILS' ); ?></legend>
        <table class="adminform table table-striped adminformlist">
		 <?php if($this->profile->quick && $operation!=3){?>
            <tr>
            	<td width="200"><label class="hasTip" title="<?php echo JText::_('QUICK_IMPORT_DESC');?>"><?php echo JText::_('QUICK_IMPORT'); ?></td>
                <td>
                	<input type="checkbox" value="1" class="inputbox required" id="quick" name="quick" <?php if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==1) ){echo "checked='checked'";} elseif(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->id==$profile_id)){} elseif( $this->profile->quick ){echo "checked='checked'";}?> />
                </td>
            </tr>
			
        <?php }?>
		<?php if($operation==3){
			foreach($keys as $key){
		?>
			<tr>
				<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
				<td>
					<select name="csvfield[<?php echo $key;?>]" class="">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php for($i=0;$i<count($csvfields[0]);$i++) : ?>
						<option value="<?php echo $i; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $key) && !is_object($schedule_columns->fields->{$key}) && ($schedule_columns->fields->{$key}!="") && ($schedule_columns->fields->{$key}==$i) ){echo 'selected="selected"';} elseif( empty($schedule_columns) && (strtolower($key) == strtolower($csvfields[0][$i])) ) {echo 'selected="selected"';} ?>><?php echo $csvfields[0][$i]; ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
		<?php }}?>
        <?php foreach($this->profile->params->fields as $column=>$params) : ?>
        <?php
			if($params->data == "file"){
		?>
          <tr <?php if(in_array($column,$keys) && ($operation==2 || $operation==0) ){ echo 'class="primaryKey"';}?>>
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
            <td>
				<select class="field_type" name="csvfield[<?php echo $column;?>]">
					<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
					<?php for($i=0;$i<count($csvfields[0]);$i++) : ?>
						<option value="<?php echo $i; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && !is_object($schedule_columns->fields->$column) && ($schedule_columns->fields->$column!="") && ($schedule_columns->fields->$column==$i) ){echo 'selected="selected"';} elseif( empty($schedule_columns) && (strtolower($column) == strtolower($csvfields[0][$i])) ) {echo 'selected="selected"';} ?>><?php echo $csvfields[0][$i]; ?></option>
					<?php endfor; ?>
				</select>
				
				
				<?php if($params->format == "image"){
					$image_source= 'http://fileurl/';
					$path_type='';
					if(!empty($images) && property_exists($images,'root') && property_exists($images->root,$column)){
						$image_value = $images->root->{$column};
						if(property_exists($image_value,'image_source')){
							$image_source = $image_value->image_source;
						}
						if(property_exists($image_value,'path_type')){
							$path_type = $image_value->path_type;
						}
					}
					?>
				<select name="images[root][<?php echo $column; ?>][path_type]" id="path_type" onchange="showValue(this.value,'<?php echo $column?>')">
					<option value='' selected="selected"><?php echo JText::_('SELECT_FILE_PATH');?></option>
					<option value='url' <?php if($path_type=='url')echo 'selected="selected"'; ?>>URL</option>
					<option value='ftp' <?php if($path_type=='ftp')echo 'selected="selected"'; ?>>FTP</option>
					<option value='directory' <?php if($path_type=='directory')echo 'selected="selected"'; ?>>Directory</option>
				</select>
				<input name="images[root][<?php echo $column; ?>][image_source]" id="image_source_<?php echo $column?>" type="text" value="<?php echo $image_source; ?>">
			<?php }?>
				
			</td>
          </tr>
		  
        <?php } elseif($params->data == "reference"  && count($params->reftext)){?>
        	<tr>
                <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
                <td>
                	<?php foreach($params->reftext as $ref){?>
                	<div class = "field_option">
                    	<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
                        <span class="field">
                        	<select name="csvfield[<?php echo $column;?>][<?php echo $ref;?>]">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
                            	<?php for($i=0;$i<count($csvfields[0]);$i++) : ?>
                                    <option value="<?php echo $i; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && is_object($schedule_columns->fields->$column) && property_exists($schedule_columns->fields->$column, $ref) && ($schedule_columns->fields->$column->$ref!="") && ($schedule_columns->fields->$column->$ref == $i) ){echo 'selected="selected"';} elseif( empty($schedule_columns) && (strtolower($params->table.'_'.$ref) == strtolower($csvfields[0][$i])) ) echo 'selected="selected"'; ?>><?php echo $csvfields[0][$i]; ?></option>
                                <?php endfor; ?>
                            </select>
                        </span>
                    </div>
                     <?php }?>
                </td>
            </tr>
        <?php }elseif($params->data == "asset_reference"){ ?>
			<tr>
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
				<td>
                	<?php 
					$db  = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('name');
					$query->from($db->quoteName('#__extensions'));  
					$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
					$query->where($db->quoteName('name') . ' = ' . $db->quote($params->table));
					$db->setQuery($query);
					$components = $db->loadResult();  
					
					if (strpos(strtolower($components), "com_") ===false){
					$components = "com_".strtolower($components); 	
					}
					?>
					
                	<div class = "field_option">
                    	<label class="hasTip" title="<?php echo JText::_('Component Name');?>"><?php echo JText::_('Component Name');?></label>
                        <span class="field">
                        	<select name="csvfield[<?php echo $column;?>][component]">
								<option value="<?php echo $params->table?>"><?php echo JText::_($components); ?></option>
								</select>
                            
                        </span>
                    </div>
					<div class = "field_option"> 
                    	<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
                        <span class="field">
                        	<select name="csvfield[<?php echo $column;?>][on]">
								<option value="<?php echo $params->on?>"><?php echo JText::_($params->on); ?></option>
								</select>
                            
                        </span>
                    </div>
					<div class = "field_option"> 
                    	<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
                        <span class="field">
                        	<select name="csvfield[<?php echo $column;?>][rules]">
								<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
								<?php for($i=0;$i<count($csvfields[0]);$i++) : ?>
                                    <option value="<?php echo $i; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && is_object($schedule_columns->fields->$column) && property_exists($schedule_columns->fields->$column, 'rules') && ($schedule_columns->fields->$column->rules!="") && ($schedule_columns->fields->$column->rules == $i) ){echo 'selected="selected"';} ?>><?php echo $csvfields[0][$i]; ?></option>
                                <?php endfor; ?>
								</select>
                            
                        </span>
                    </div>
                </td>
			</tr>
			
		<?php } ?>
        <?php endforeach; ?>
        
		<?php 	$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
				if(!empty($joins)){
				$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
				for($i=0; $i<count($joins->table1); $i++) {
		?>
					
					<?php if( isset($joins->columns[$i]) && !empty($joins->columns[$i]) ){ ?>
					
					<tr>
						<td colspan="2">
							<label class="hasTip" title="<?php echo JText::sprintf('CHILD_TABLE_TO_IMPORT', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
						</td>
					</tr>
					
					<?php foreach($joins->columns[$i] as $key=>$column){ ?>
						
						<?php if($column->data == "file"){?>
						<tr>
							<td>
							<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key;?></label>
							</td>
							<td>
							<select name="csvfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>]" id="" class="remote_column">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
								<?php
								if($csv_child->csv_child==1){
									foreach($csvfields[0] as $j=>$field){
								?>
									<option value="<?php echo $j;?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && is_object($schedule_columns->fields->{$joins->table2[$i]}) && property_exists($schedule_columns->fields->{$joins->table2[$i]}, $key) && ($schedule_columns->fields->{$joins->table2[$i]}->$key!="") && ($schedule_columns->fields->{$joins->table2[$i]}->$key == $j) ){echo 'selected="selected"';} //elseif( empty($schedule_columns) && (strtolower($key) == strtolower($csvfields[0][$j])) ) echo 'selected="selected"';?>><?php echo $field;?></option>
									
								<?php 
									}
								}
								else{
								?>
								<?php if(array_key_exists(($i+1), $csvfields)){?>
							   <?php for($j=0;$j<count($csvfields[$i+1]);$j++) : ?>
									<option value="<?php echo $j; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && is_object($schedule_columns->fields->{$joins->table2[$i]}) && property_exists($schedule_columns->fields->{$joins->table2[$i]}, $column) && ($schedule_columns->fields->{$joins->table2[$i]}->$column!="") && ($schedule_columns->fields->{$joins->table2[$i]}->$column == $j) ){echo 'selected="selected"';} elseif( empty($schedule_columns) && (strtolower($column) == strtolower($csvfields[$i+1][$j])) ) echo 'selected="selected"';  ?>><?php echo $csvfields[$i+1][$j]; /* if(strtolower($column) == strtolower($csvfields[$i+1][$j])) echo 'selected="selected"'; */?></option>
								<?php endfor; ?>
								<?php }
								}
								?>
							</select>
							</td>
						</tr>
						<?php } elseif($column->data == "reference"  && count($column->reftext)){?>
							<tr>
							<td><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key;?></label></td>
							<td>
							<?php foreach($column->reftext as $ref){?>
								<div class = "field_option">
									<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
									<span class="field">
										<select name="csvfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>]">
										<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
											<?php 
											if($csv_child->csv_child==1){
											foreach($csvfields[0] as $j=>$field) { ?>
											<option value="<?php echo $j; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields,$joins->table2[$i]) && property_exists($schedule_columns->fields->{$joins->table2[$i]}, $key) && is_object($schedule_columns->fields->{$joins->table2[$i]}->$key) && property_exists($schedule_columns->fields->{$joins->table2[$i]}->$key, $ref) && ($schedule_columns->fields->{$joins->table2[$i]}->$key->$ref!="") && ($schedule_columns->fields->{$joins->table2[$i]}->$key->$ref == $j) ){echo 'selected="selected"';} elseif( empty($schedule_columns) && (strtolower($column->table.'_'.$ref) == strtolower($csvfields[0][$i])) ) echo 'selected="selected"'; ?>><?php echo $field; ?></option>
											<?php }}else{ ?>
											
											<?php }?>
										</select>
									</span>
								</div>
							<?php }?>
							</td>
							</tr>
						<?php }elseif($column->data == "asset_reference"){?>
						<tr>
							<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
							<td>
							<?php 
								$db  = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select('name');
								$query->from($db->quoteName('#__extensions'));  
								$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
								$query->where($db->quoteName('name') . ' = ' . $db->quote($params->table));
								$db->setQuery($query);
								$components = $db->loadResult();  
								
								if (strpos(strtolower($components), "com_") ===false){
								$components = "com_".strtolower($components); 	
								}
							?>
								
								<div class = "field_option">
									<label class="hasTip" title="<?php echo JText::_('Component Name');?>"><?php echo JText::_('Component Name');?></label>
									<span class="field">
										<select name="csvfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][component]">
											<option value="<?php echo $column->table?>"><?php echo JText::_($components); ?></option>
										</select>
										
									</span>
								</div>
								<div class = "field_option"> 
									<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
									<span class="field">
										<select name="csvfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][on]">
											<option value="<?php echo $column->on?>"><?php echo JText::_($column->on); ?></option>
										</select>
									</span>
								</div>
								<div class = "field_option"> 
									<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
									<span class="field">
										<select name="csvfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules]">
											<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
											<?php 
												if($csv_child->csv_child==1){
												foreach($csvfields[0] as $j=>$field) { ?>
												<option value="<?php echo $j; ?>" <?php if( !empty($schedule_columns) && is_object($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && property_exists($schedule_columns->fields->{$joins->table2[$i]}, $key) && is_object($schedule_columns->fields->{$joins->table2[$i]}->$key) && property_exists($schedule_columns->fields->{$joins->table2[$i]}->$key, 'rules') && ($schedule_columns->fields->{$joins->table2[$i]}->$key->rules!="") && ($schedule_columns->fields->{$joins->table2[$i]}->$key->rules == $j) ){echo 'selected="selected"';} elseif( empty($schedule_columns) && (strtolower($column->table.'_'.$ref) == strtolower($csvfields[0][$i])) ) echo 'selected="selected"'; ?>><?php echo $csvfields[0][$i]; ?></option>
												<?php }}else{ ?>
												
												<?php }?>
											</select>
										
									</span>
								</div>
							</td>
						</tr>
						
						<?php }?>
					<?php }?>
					
					<?php }?>
			
		<?php }}?>
		<?php if($this->profile->quick && $operation!=3){ ?>
			<table class="adminform table table-striped col_block">
				<tr style="display:none;">
				<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td>
				</tr>
			</table>
		<?php } ?>
        </table>
	</fieldset>
</div>