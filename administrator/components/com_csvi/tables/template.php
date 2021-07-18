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
 * CSVI Templates table.
 *
 * @package     CSVI
 * @subpackage  Table
 * @since       6.6.0
 */
class TableTemplate extends JTable
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
		parent::__construct('#__csvi_templates', 'csvi_template_id', $db);

		$this->setColumnAlias('published', 'enabled');
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to ensure they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   1.7.0
	 */
	public function check()
	{
		// Generate a valid alias
		$this->generateAlias();

		return parent::check();
	}

	/**
	 * Generate a valid alias from template name.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @return  string
	 */
	private function generateAlias()
	{
		if (empty($this->template_alias))
		{
			$this->template_alias = $this->template_name;
		}

		$this->template_alias = JApplicationHelper::stringURLSafe($this->template_alias);

		if (trim(str_replace('-', '', $this->template_alias)) == '')
		{
			$this->template_alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
	}

	/**
	 * Overridden Table::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('Template', 'Table', array('dbo' => $this->getDbo()));

		if ($table->load(array('template_alias' => $this->template_alias))
			&& ($table->csvi_template_id != $this->csvi_template_id || $this->csvi_template_id == 0))
		{
			$this->setError(\JText::_('COM_CSVI_TEMPLATE_ALIAS_EXISTS'));

			return false;
		}

		return parent::store($updateNulls);
	}
}
