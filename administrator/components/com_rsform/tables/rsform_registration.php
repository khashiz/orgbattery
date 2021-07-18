<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Registration extends JTable
{
	public $form_id 				= null;
	public $action					= 1;
	public $action_field			= null;
	public $groups 					= 2;
	public $vars 					= '';
	public $joomla_fields 			= '';
	public $profile_fields 			= '';
	public $activation 				= 1;
	public $cbactivation 			= 1;
	public $defer_admin_email 		= 0;
	public $user_activation_action 	= null;
	public $admin_activation_action = null;
	public $user_activation_url 	= null;
	public $admin_activation_url 	= null;
	public $user_activation_text 	= null;
	public $admin_activation_text 	= null;
	public $itemid					= null;
	public $published 				= 0;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_registration', 'form_id', $db);
	}
	
	public function load($keys = null, $reset = true) {
		$result = parent::load($keys, $reset);
		
		if ($result) {
			$this->groups = explode(',', $this->groups);
			
			$this->vars = unserialize($this->vars);
			if ($this->vars === false || !is_array($this->vars)) {
				$this->vars = array();
			}
			
			if (!isset($this->vars['password'])) {
				$this->vars['password'] = isset($this->vars['password1']) ? $this->vars['password1'] : '';
			}
			
			if (!isset($this->vars['email'])) {
				$this->vars['email'] = isset($this->vars['email1']) ? $this->vars['email1'] : '';
			}

			$this->joomla_fields = unserialize($this->joomla_fields);
			if ($this->joomla_fields === false || !is_array($this->joomla_fields))
			{
				$this->joomla_fields = array();
			}

			$this->profile_fields = unserialize($this->profile_fields);
			if ($this->profile_fields === false || !is_array($this->profile_fields))
			{
				$this->profile_fields = array();
			}
		}
		
		return $result;
	}
	
	// Validate data before save
	public function check() {
		if (is_array($this->groups)) {
			$this->groups = implode(',', $this->groups);
		}
		
		if (is_array($this->vars)) {
			$this->vars	= serialize($this->vars);
		}

		if (is_array($this->joomla_fields))
		{
			$this->joomla_fields = serialize($this->joomla_fields);
		}

		if (is_array($this->profile_fields))
		{
			$this->profile_fields = serialize($this->profile_fields);
		}
		
		// Check if we need to add the empty record to the database
		$row = self::getInstance('RSForm_Registration', 'Table');
		if (!$row->load($this->form_id)) {
			$db 	= JFactory::getDbo();
			$query	= $db->getQuery(true)
						 ->insert($db->qn($this->getTableName()))
						 ->set($db->qn('form_id').'='.$db->q($this->form_id));
			$db->setQuery($query)->execute();
		}
		
		return true;
	}
}