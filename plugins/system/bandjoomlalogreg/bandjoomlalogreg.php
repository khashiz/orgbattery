<?php
/**
 * @package    Joomla SMS LogRegSms
 * @author     JoominaMarket {@link https://www.joominamarket.com}
 * @author     Created on 11 October 2018
 * @license    GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Plugin class for redirect handling.
 *
 * @since  1.6
 */
class PlgSystemBandJoomlaLogReg extends JPlugin
{
	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onBeforeRender(){
		if (!JComponentHelper::isEnabled('com_logregsms', true)){ 
			JError::raiseWarning( 100, 'کامپوننت لاگین و ثبت نام پیامکی جوملا یافت نشد.' );
		}
		else{
			$app = JFactory::getApplication();
			$option = $app->input->get('option', '');
			$view = $app->input->get('view', '');  
			if($app->isSite()){
				if($option == 'com_users' && ($view == 'registration' || $view == 'login')){
					$app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile'));
				}
			}
		}
		
	}
	
}
