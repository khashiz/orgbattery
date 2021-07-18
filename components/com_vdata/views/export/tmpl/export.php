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

JHtml::_('behavior.tooltip');
$user = JFactory::getUser();
$mainframe = JFactory::getApplication();

$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
$canViewImport = $user->authorise('core.access.import', 'com_vdata');
$canViewExport = $user->authorise('core.access.export', 'com_vdata');
$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');

?>
<div id="vdatapanel">
<div class="toolbar-btn dash-btn">
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
    class="btn btn-small btn-export active">
       <span class="icon-out-2 icon-white"></span><?php echo JText::_( 'VDATA_EXPORT' );?>
</a>
<?php }?>
<?php if($canViewProfiles){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=profiles" 
    class="btn btn-small btn-profile">
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
<div id="hd_progress"></div>
<div id="toolbar" class="toolbar-btn">
<?php if($this->st){
	$session = JFactory::getSession();
	$exportitem = $session->get('exportitem', null);
	$isNew = (JFactory::getApplication()->input->getInt('id', 0) < 1);
	
?>
	<span class="hx_title"><?php echo $exportitem->title.' '.JText::_( 'SCHEDULE' );?></span>
	<button class="btn btn-small" onclick="Joomla.submitbutton('save_st');"><span class="icon-apply"></span><?php echo JText::_('SAVE_ST');?></button>
	<?php if(!$isNew){?>
		<button class="btn btn-small save-copy" onclick="Joomla.submitbutton('save_st2copy');"><span class="icon-save-copy"></span><?php echo JText::_('SAVE_ST2COPY');?></button>
	<?php }?>
	<button class="btn btn-small cancel" onclick="Joomla.submitbutton('close_st');"><span class="icon-cancel"></span><?php echo JText::_('Cancel');?></button>
<?php } else{
	$user = JFactory::getUser();
	$canExport = $user->authorise('core.export', 'com_vdata');
?>
	<span class="hx_title"><?php echo $this->profile->title.' '.JText::_( 'EXPORT' );?></span>
	<?php if($canExport){?>
	<button class="btn btn-small" onclick="Joomla.submitbutton('export_start');"><span class="icon-out-2"></span><?php echo JText::_('EXPORT_START');?></button>
	<?php }?>
	<button class="btn btn-small cancel" onclick="Joomla.submitbutton('close');"><span class="icon-cancel"></span><?php echo JText::_('Close')?></button>
	<?php if($canViewCronFeed){ if( ($mainframe->input->get('server', 'down', 'string') != 'down') || ($mainframe->input->get('source', '', 'string')=='remote') ){?>
	<button class="btn btn-small" onclick="Joomla.submitbutton('create_st');"></span class="icon-link"></span><?php echo JText::_('CREATE_SCHEDULE');?></button>
	<?php }}?>
<?php }?>
</div>



<form action="index.php?option=com_vdata&view=export" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <div class="exportdatablock">
        <?php echo $this->contents; ?>
    </div>

<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="export" />
<input type="hidden" name="profileid" value="<?php if(!empty($this->profile->id)) echo $this->profile->id; ?>" />
</form>

</div>