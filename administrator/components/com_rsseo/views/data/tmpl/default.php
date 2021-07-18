<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsseo&view=data');?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php echo RSSeoAdapterGrid::sidebar(); ?>			
		<?php foreach ($this->form->getFieldsets() as $fieldset) { ?>
		<?php $this->tabs->addTitle('COM_RSSEO_STRUCTURED_FIELDSET_'.strtoupper($fieldset->name), $fieldset->name); ?>
		<?php $this->tabs->addContent($this->form->renderFieldset($fieldset->name)); ?>
		<?php } ?>
		<?php JFactory::getApplication()->triggerEvent('onrsseo_structuredTabs', array(array('tabs' => $this->tabs))); ?>
		<?php echo $this->tabs->render(); ?>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
</form>