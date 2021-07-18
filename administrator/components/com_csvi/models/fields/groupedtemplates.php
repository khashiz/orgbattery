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
 * Renders a field as a button.
 *
 * @package     CSVI
 * @subpackage  Fields
 * @since       6.6.0
 */
class CsviFormFieldGroupedtemplates extends JFormFieldGroupedList
{
	public function getGroups()
	{
		/** @var CsviModelTemplates $templateModel */
		$templateModel = BaseDatabaseModel::getInstance('Templates', 'CsviModel', ['ignore_request' => true]);
		$templates = $templateModel->getItems();
		$groupedTemplates = [];

		foreach ($templates as $template)
		{
			$groupedTemplates[Text::_(
				'COM_CSVI_' . $template->action
			)][$template->csvi_template_id] = $template->template_name;
		}

		return $groupedTemplates;
	}
}
