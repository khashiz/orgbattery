<?php
/**
 * @package     CSVI
 * @subpackage  Table
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * CSVI Tasks table.
 *
 * @package     CSVI
 * @subpackage  Table
 * @since       6.6.0
 */
class TableTask extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  A database connector object.
	 *
	 * @since   6.6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__csvi_tasks', 'csvi_task_id', $db);

		$this->setColumnAlias('published', 'enabled');
	}
}
