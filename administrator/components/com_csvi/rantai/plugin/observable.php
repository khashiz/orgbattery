<?php
/**
 * @package     CSVI
 * @subpackage  Rantai
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Rantai observable.
 *
 * @package     CSVI
 * @subpackage  Rantai
 * @since       6.0
 */
interface RantaiObservable
{
	/**
	 * Add observers.
	 *
	 * @param   string  $strEventType  The event trigger
	 * @param   object  $listener      An observer to observer
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function addListener($strEventType, $listener);

	/**
	 * Fire an event.
	 *
	 * @param   string  $strEventType  The event to trigger
	 * @param   array   $args          Data to pass to the listener
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function trigger($strEventType, $args=array());
}
