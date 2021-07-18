<?php
/**
* @package RSSeo!
* @copyright (C) 2019 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
class JFormFieldFont extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Font';
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$path = JPATH_ADMINISTRATOR.'/components/com_rsseo/helpers/dompdf/lib/fonts/';
		
		$options 	= array();
		$options[] = JHTML::_('select.option', 'times', JText::_('COM_RSSEO_PDF_FONT_TIMES'));
		$options[] = JHTML::_('select.option', 'helvetica', JText::_('COM_RSSEO_PDF_FONT_HELVETICA'));
		$options[] = JHTML::_('select.option', 'courier', JText::_('COM_RSSEO_PDF_FONT_COURIER'));
		$options[] = JHTML::_('select.option', 'dejavu sans', JText::_('COM_RSSEO_PDF_FONT_DEJAVU_SANS'), 'value', 'text', !file_exists($path.'DejaVuSans.ufm'));
		$options[] = JHTML::_('select.option', 'fireflysung', JText::_('COM_RSSEO_PDF_FONT_FIREFLYSUNG'), 'value', 'text', !file_exists($path.'fireflysung.ufm'));
		
		return $options;
	}
}