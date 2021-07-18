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

$user = JFactory::getUser();
$canAdd = $user->authorise('core.create', 'com_vdata');
$canEdit = $user->authorise('core.edit', 'com_vdata');
$canEditOwn = $user->authorise('core.edit.own','com_vdata');
$canEditState = $user->authorise('core.edit.state', 'com_vdata');
$canDelete = $user->authorise('core.delete', 'com_vdata');
$canImport = $user->authorise('core.import', 'com_vdata');
$canExport = $user->authorise('core.export', 'com_vdata');

$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
$canViewImport = $user->authorise('core.access.import', 'com_vdata');
$canViewExport = $user->authorise('core.access.export', 'com_vdata');
$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');



$app = JFactory::getApplication();
$context = 'com_vdata.profiles.list.';

$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );

?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'saveAsCopy') {
		
		if (document.adminForm.boxchecked.value==0){
			alert('<?php echo JText::_('COM_VDATA_NO_PROFILE_SELECTED');?>');
			return false;
		}
		// Joomla.submitbutton('saveAsCopy');
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
$hd(function(){
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
	$hd("#icon_desc_toggle").mouseover(function(){
		$hd( "#desc_toggle" ).show( 'slide', 800);
	});	
	$hd( ".sub_desc .close" ).on( "click", function() {
		$hd( "#desc_toggle" ).hide( 'slide', 800);
	});
})
</script>

<div id="vdatapanel">
<div id="toolbar" class="toolbar-btn dash-btn">
<span class="hx_title"><img src="<?php echo JURI::root();?>/media/com_vdata/images/vdata-logo.png" alt="vData"> <span class="hx_main_title"><?php echo JText::_( 'VDATA_TITLE' );?><br><span class="hx_subtitle"><?php echo JText::_( 'VDATA_SUBTITLE_DESC' );?></span></span></span>
<div class="hx_dash_button">
<?php if($canViewDashboard){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=vdata" 
    class="btn btn-small btn-widget">
       <span class="icon-new icon-dashboard"></span><?php echo JText::_( 'VDATA_DASHBOARD' );?>
</a>
<?php }?>
<?php if($canViewImport){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=import" 
    class="btn btn-small btn-import">
       <span class="icon-import icon-white"></span><?php echo JText::_( 'VDATA_IMPORT' );?>
</a>
<?php }?>
<?php if($canViewExport){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=export" 
    class="btn btn-small btn-export">
       <span class="icon-out-2 icon-white"></span><?php echo JText::_( 'VDATA_EXPORT' );?>
</a>
<?php }?>
<?php if($canViewProfiles){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=profiles" 
    class="btn btn-small btn-profile active">
       <span class="icon-stack icon-white"></span><?php echo JText::_( 'VDATA_PROFILE' );?>
</a>
<?php }?>
<?php if($canViewCronFeed){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=schedules" 
    class="btn btn-small btn-feed active">
       <span class="icon-feed icon-white"></span><?php echo JText::_( 'VDATA_SCHEDULES' );?>
</a>
<?php }?>
</div>
</div>
<div id="toolbar" class="toolbar-btn">
<div class="hxd_title">
	<span class="hx_title">
	<?php echo JText::_('PROFILES'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
		<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('PROFILES_DESC_TT'); ?></div>
	</span>
	<?php if($canDelete){?>
	<button class="btn btn-small delete" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('DELETE_ALERT');?>');return false;}else{if (confirm('<?php echo JText::_('DELETE_CONFIRM');?>')){Joomla.submitbutton('remove');}}"><span class="icon-delete"></span><?php echo JText::_('DELETE');?></button>
	<?php }?>
	<?php if($canEdit || $canEditOwn){?>
	<button class="btn btn-small edit" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('EDIT_ALERT');?>');return false;}else{ Joomla.submitbutton('edit')}"><span class="icon-edit"></span><?php echo JText::_('EDIT');?></button>
	<?php }?>
	<?php if($canAdd){?>
	<button class="btn btn-small btn-success" onclick="Joomla.submitbutton('add')">
	<span class="icon-new icon-white"></span><?php echo JText::_('NEW');?></button>
	<?php }?>
	<button class="btn btn-small btn-profile" onclick="Joomla.submitbutton('profile_wizard')">
	<span class="icon-link icon-white"></span><?php echo JText::_('PROFILE_WIZARD');?></button>
	<?php if($canAdd){?>
		<button class="btn btn-small btn-copy" onclick="Joomla.submitbutton('saveAsCopy')">
	<span class="icon-copy icon-white"></span><?php echo JText::_('SAVE_AS_COPY');?></button>
	<?php }?>
</div>
</div>


<form action="<?php echo JRoute::_('index.php?option=com_vdata&view=profiles');?>" method="post" name="adminForm" id="adminForm">

<div class="btn-toolbar" id="filter-bar">
	<div class="filter-search btn-group pull-left">
		<input type="text" id="filter_search" name="search" class="hasTip" title="<?php echo JText::_('SEARCH_TIP');?>" placeholder="<?php echo JText::_('SEARCH_PLACEHOLDER');?>" value="<?php if(!empty($keyword)) echo $keyword;?>"/>
	</div>
	<div class="btn-group">
		<button type="submit" class="btn hasTip" title="<?php echo JText::_('SEARCH');?>"><i class="icon-search"></i></button>
		<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn hasTip" title="<?php echo JText::_('CLEAR');?>"><i class="icon-remove"></i></button>
	</div>
	<div class="btn-group pull-right">
		<?php
		$opt = array(JHTML::_('select.option', -1, JText::_('SELECT_TYPE')), JHTML::_('select.option', 0, JText::_('IMPORT')), JHTML::_('select.option', 1, JText::_('EXPORT')) );
		
		echo JHTML::_('select.genericlist', $opt, 'filter_type', "class='inputbox' size='1' onchange='document.adminForm.submit();'", 'value', 'text', $filter_type , -1);
		?>
		<?php echo $this->pagination->getLimitBox();?>
	</div>
</div>
<div class="clearfix"> </div>

<div id="editcell">
    <table class="adminlist table">
    <thead>
			<tr>
				<th width="10" class="center">
					<?php echo JText::_( '#' ); ?>
				</th>
				<th width="15" class="center">
					<?php //echo JHTML::_('grid.checkall'); ?>
					<input type="checkbox" onclick="Joomla.checkAll(this)" title="<?php echo JText::_( 'CHECK_ALL' );?>" value="" name="checkall-toggle"><!---->
					<!--<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php //echo count( $this->items ); ?>);" />-->
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'PROFILE', 'i.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
                <th class="title">
					<?php echo JHTML::_('grid.sort', 'PLUGIN', 'e.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				
				<?php if($this->config->logging){?>
				<th class="center">
					<?php echo JHTML::_('grid.sort', 'VDATA_LOGS', 'e.logs', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<?php }?>
                
				<th nowrap="nowrap" class="center">
					<?php echo JText::_('ACTIONS'); ?>
				</th>
				<th width="10" nowrap="nowrap" class="center">
					<?php echo JHTML::_('grid.sort', 'ID', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>

	<tfoot>
    <tr>
      <td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  	</tfoot>

    <?php
    $k = 0;
	
	$lang = JFactory::getLanguage();
	
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row = $this->items[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		
		$lang->load($this->items[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$this->items[$i]->element, null, false, false);
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td class="center"><?php echo $checked; ?></td>

            <td>
			<?php if($canEdit || $canEditOwn){?>
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=profiles&task=edit&cid[]=').$row->id;?>">
			<?php	echo $row->title;?></a>
			<?php }else{echo $row->title;}?>
			</td>
            <td><?php echo JText::_($row->plugin); ?></td>
			
			<?php if($this->config->logging){?>
				<td class="center"><?php echo JText::_($row->logs); ?></td>
			<?php }?>
			
            <?php if($row->iotype) { ?>
            <td class="center">
				<?php if($canExport){?>
				<a class="btn btn-small btn-export" href="<?php echo JRoute::_('index.php?option=com_vdata&view=export&profileid=').$row->id;?>"><?php echo JText::_('EXPORT'); ?></a>
				<?php }else{echo JText::_('EXPORT');}?>
			</td>
			<?php }else{ ?>
            <td class="center">
				<?php if($canImport){?>
				<a class="btn btn-small btn-import" href="<?php echo JRoute::_('index.php?option=com_vdata&view=import&profileid=').$row->id;?>"><?php echo JText::_('IMPORT'); ?></a>
				<?php }else{echo JText::_('IMPORT');}?>
			</td>
			<?php } ?>
            <td class="center"><?php echo $row->id; ?></td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>
 <?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="profiles" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

</form>
</div>
