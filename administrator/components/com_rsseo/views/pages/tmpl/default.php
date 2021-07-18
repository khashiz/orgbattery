<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo RSSeoAdapterGrid::sidebar(); ?>
			
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			
		<?php echo $this->loadTemplate($this->simple ? 'simple' : 'standard'); ?>
	</div>
	
	<?php $footer = '<a href="javascript:void(0)" onclick="Joomla.submitbutton(\'pages.batch\');" class="btn btn-primary">'.JText::_('COM_RSSEO_APPLY').'</a><a href="javascript:void(0)" data-dismiss="modal" class="btn">'.JText::_('COM_RSSEO_GLOBAL_CLOSE').'</a>'; ?>
	<?php $selector = rsseoHelper::isJ4() ? 'batchpages' : 'modal-batchpages'; ?>
	<?php echo JHtml::_('bootstrap.renderModal', $selector, array('title' => JText::_('COM_RSSEO_BATCH_OPTIONS'), 'footer' => $footer, 'bodyHeight' => 70), $this->loadTemplate('batch')); ?>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hash" value="<?php echo $this->escape(JFactory::getApplication()->input->getString('hash')); ?>" />
</form>