<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class HikashopHelperAssociation {

	public static function getAssociations($id = 0, $view = null, $layout = null) {
		$languages	= JLanguageHelper::getLanguages();
		$jinput = JFactory::getApplication()->input;
		$ctrl = $jinput->get('ctrl');
		$ctrl_var = 'ctrl';
		if(empty($ctrl)) {
			$ctrl = $jinput->get('view');
			if(!empty($ctrl))
				$ctrl_var = 'view';
		}
		$task = $jinput->get('task');
		$task_var = 'task';
		if(empty($ctrl)) {
			$task = $jinput->get('layout');
			if(!empty($task))
				$task_var = 'layout';
		}
		$component = $jinput->getCmd('option');
		$Itemid = $jinput->get('Itemid');

		$result = array();

		if($component != 'com_hikashop')
			return $result;

		$url = 'index.php?option=com_hikashop';
		if(!empty($ctrl))
			$url .= '&'.$ctrl_var.'='.$ctrl;
		if(!empty($task))
			$url .= '&'.$task_var.'='.$task;
		if(!empty($Itemid))
			$url .= '&Itemid='.$Itemid;

		$cid = $jinput->get('cid');
		if(!empty($cid))
			$url .= '&cid='.$cid;

		$step = $jinput->get('step');
		if(!empty($step))
			$url .= '&step='.$step;

		$name = $jinput->get('name');
		if(!empty($name)) {
			$url .= '&name='.$name;
		}

		foreach($languages as $i => &$language) {
			$result[$language->lang_code] = $url;
		}

		return $result;
	}

}
