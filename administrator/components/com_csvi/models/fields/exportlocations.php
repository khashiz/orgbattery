<?php
/**
 * @package     CSVI
 * @subpackage  Fields
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

JFormHelper::loadFieldClass('groupedlist');

/**
 * Renders a list of export locations
 *
 * @package     CSVI
 * @subpackage  Fields
 * @since       7.19.0
 */
class CsviFormFieldExportlocations extends JFormFieldGroupedList
{
	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 *
	 * @since   7.19.0
	 * @throws  UnexpectedValueException
	 */
	public function getGroups()
	{
		$exportOptions                          = [];
		$label                                  = Text::_('COM_CSVI_LOCATIONS');
		$exportOptions[$label]['todownload']    = Text::_('COM_CSVI_EXPORT_TO_DOWNLOAD_LABEL');
		$exportOptions[$label]['tofile']        = Text::_('COM_CSVI_EXPORT_TO_LOCAL_LABEL');
		$exportOptions[$label]['toftp']         = Text::_('COM_CSVI_EXPORT_TO_FTP_LABEL');
		$exportOptions[$label]['toemail']       = Text::_('COM_CSVI_EXPORT_EMAIL_FILE_LABEL');
		$exportOptions[$label]['todatabase']    = Text::_('COM_CSVI_EXPORT_ANOTHER_DATABASE_LABEL');
		$exportOptions[$label]['togooglesheet'] = Text::_('COM_CSVI_EXPORT_GOOGLE_SHEET');

		/** @var CsviModelTemplates $templateModel */
		$templateModel = BaseDatabaseModel::getInstance('Templates', 'CsviModel', ['ignore_request' => true]);
		$templates     = $templateModel->getItems();
		$label         = Text::_('COM_CSVI_TITLE_TEMPLATES');

		foreach ($templates as $template)
		{
			if ($template->action !== 'import')
			{
				continue;
			}

			$exportOptions[$label][$template->csvi_template_id] = $template->template_name;
		}

		return $exportOptions;
	}
}
