<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/
defined('JPATH_PLATFORM') or die;

class JFormFieldShort extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Short';
	
	protected function getInput() {
		$html = array();
		
		JText::script('COM_RSSEO_ONLY_ALPHANUM');
		
		$id		= JFactory::getApplication()->input->getInt('id', 0);
		$value 	= $this->value ? JUri::root().$this->value : '';
		$short 	= rsseoHelper::short($id);
		
		$input = '<input type="hidden" id="'.$this->id.'" name="'.$this->name.'" value="'.$this->value.'" /><input readonly type="text" class="'.$this->class.'" id="'.$this->id.'_dummy" value="'.$value.'" />';
		$append = '<button id="editShortBtn" class="btn btn-primary hasTooltip" title="'.JText::_('COM_RSSEO_SHORT_EDIT').'" type="button" onclick="RSSeo.editShort(\''.$short.'\')"><i class="fa fa-edit"></i></button><button id="saveShortBtn" style="display:none;" class="btn btn-primary hasTooltip" title="'.JText::_('COM_RSSEO_SHORT_SAVE').'" type="button" onclick="RSSeo.saveShort(\''.JUri::root().'\')"><i class="fa fa-save"></i></button><button id="cancelShortBtn" style="display:none;" class="btn btn-primary hasTooltip" title="'.JText::_('COM_RSSEO_SHORT_CANCEL').'" type="button" onclick="RSSeo.cancelShort(\''.JUri::root().'\')"><i class="fa fa-times"></i></button><button id="copyShortBtn" class="btn btn-primary hasTooltip" title="'.JText::_('COM_RSSEO_SHORT_COPY').'" type="button" onclick="RSSeo.copyShort();"><i class="fa fa-copy"></i></button>';
		
		return RSSeoAdapterGrid::inputGroup($input, null, $append);
	}
}