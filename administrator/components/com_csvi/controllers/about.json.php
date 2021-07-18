<?php
/**
 * @package     CSVI
 * @subpackage  Tasks
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Tasks controller.
 *
 * @package     CSVI
 * @subpackage  About
 * @since       6.0
 */
class CsviControllerAbout extends JControllerForm
{
	/**
	 * Create the missing folder.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	public function createFolder()
	{
		/** @var CsviModelAbout $model */
		$model = $this->getModel();
		$result = $model->fixFolder();
		echo json_encode($result);

		JFactory::getApplication()->close();
	}
}
