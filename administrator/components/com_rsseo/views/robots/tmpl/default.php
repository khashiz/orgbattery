<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsseo&view=robots');?>" method="post" name="adminForm" id="adminForm">
	
	<?php echo RSSeoAdapterGrid::sidebar(); ?>
		<?php if ($this->check) { ?>
		<div class="alert alert-<?php echo $this->writtable ? 'info' : 'danger'; ?>"><?php echo JText::_('COM_RSSEO_ROBOTS_FILE_EXISTS_INFO'.($this->writtable ? '_OK' : '_NOT_OK')); ?></div>
		<textarea name="robots" id="robots" class="span5 form-control" rows="30"><?php echo $this->contents; ?></textarea>
		<?php } else { ?>
		<div class="alert alert-danger"><?php echo JText::_('COM_RSSEO_ROBOTS_FILE_DOES_NOT_EXIST'); ?></div>
		<button type="button" onclick="Joomla.submitbutton('createrobots');" class="btn btn-primary btn-large"><?php echo JText::_('COM_RSSEO_CREATE_ROBOTS_FILE'); ?></button>
		<?php } ?>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
</form>