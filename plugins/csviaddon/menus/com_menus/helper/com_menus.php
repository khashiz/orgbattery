<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaMenus
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Joomla Menus helper.
 *
 * @package     CSVI
 * @subpackage  JoomlaMenus
 * @since       6.3.0
 */
class Com_MenusHelperCom_Menus
{
	/**
	 * Template helper
	 *
	 * @var    CsviHelperTemplate
	 * @since  6.0
	 */
	protected $template = null;

	/**
	 * Logger helper
	 *
	 * @var    CsviHelperLog
	 * @since  6.0
	 */
	protected $log = null;

	/**
	 * Fields helper
	 *
	 * @var    CsviHelperFields
	 * @since  6.0
	 */
	protected $fields = null;

	/**
	 * Database connector
	 *
	 * @var    JDatabase
	 * @since  6.0
	 */
	protected $db = null;


	/**
	 * Constructor.
	 *
	 * @param   CsviHelperTemplate  $template  An instance of CsviHelperTemplate.
	 * @param   CsviHelperLog       $log       An instance of CsviHelperLog.
	 * @param   CsviHelperFields    $fields    An instance of CsviHelperFields.
	 * @param   JDatabaseDriver     $db        Database connector.
	 *
	 * @since   6.3.0
	 */
	public function __construct(
		CsviHelperTemplate $template,
		CsviHelperLog $log,
		CsviHelperFields $fields,
		JDatabaseDriver $db)
	{
		$this->template = $template;
		$this->log = $log;
		$this->fields = $fields;
		$this->db = $db;
	}

	/**
	 * Get the menu ID based on it's path.
	 *
	 * @param   string  $path    The path of the menu.
	 * @param   string  $type    The type of menu.
	 * @param   bool    $exists  if exists in menu type or not.
	 *
	 * @return  int  The ID of the menu.
	 *
	 * @since   6.3.0
	 *
	 * @throws  RuntimeException
	 */
	public function getMenuId($path, $type, $exists)
	{
		$query = $this->db->getQuery(true);
		$query->select('id')
			->from($this->db->quoteName('#__menu'))
			->where($this->db->quoteName('path') . ' = ' . $this->db->quote($path));

		if (!$exists)
		{
			$query->where($this->db->quoteName('menutype') . '  != ' . $this->db->quote($type));
		}
		else
		{
			$query->where($this->db->quoteName('menutype') . '  = ' . $this->db->quote($type));
		}

		$this->db->setQuery($query);

		$this->log->add('Get the menu ID');

		return $this->db->loadResult();
	}
}
