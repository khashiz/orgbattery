<?php

defined('_JEXEC') or die('Restricted access');
require_once JPATH_PLUGINS.'/hikashoppayment/pas/paslibs/paslibs.php';
class plgHikashoppaymentPas extends hikashopPaymentPlugin
{


    var $multiple = true;
    var $name = 'pas';
    var $doc_form = 'pas';

    function onBeforeOrderCreate(&$order,&$do)
    {
        if(parent::onBeforeOrderCreate($order, $do) === true)
            return true;

        if(empty($this->payment_params->merchant))
        {
            $this->app->enqueueMessage('لطفا تنظیمات درگاه خود را بررسی نمایید اطلاعات بدرستی وارد نشده است');
            $do = false;
        }
    }

    ### send data to Bank
    function onAfterOrderConfirm(&$order, &$methods, $method_id){
        
        parent::onAfterOrderConfirm($order, $methods, $method_id);
        $app = JFactory::getApplication();
        $MerchantID = $this->payment_params->merchant;
        $Amount = round($order->cart->full_total->prices[0]->price_value_with_tax,(int)$this->currency->currency_locale['int_frac_digits'])*$this->currency->currency_rate;
        $CallbackURL = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment='.$this->name.'&tmpl=component&lang='.$this->locale . $this->url_itemid;
        $CallbackURL = $CallbackURL.'&order_id='.$order->order_id.'&amount='.$Amount; 
        $setPayment = pasClass::setPas($Amount,'',$MerchantID,$CallbackURL);
        echo $setPayment;

   
}

    function onPaymentNotification(&$statuses)
    {
        $orderid = JRequest::getVar('order_id');
        $pluginsClass = hikashop_get('class.plugins');
        $elements = $pluginsClass->getMethods('payment','pas');
		
        $dbOrder = $this->getOrder($orderid);
       $this->loadPaymentParams($dbOrder);
        if (empty($this->payment_params)) {
            return false;
        }
        $MerchantID = $this->payment_params->merchant;
      
        $orderClass = hikashop_get('class.order');
        $dbOrder = $orderClass->get((int)@$orderid);
       
        if(empty($dbOrder)){
            echo "فاکتور مورد تائید نمیباشد ".@$orderid;
            return false;
        }
		if ($_SERVER["REQUEST_METHOD"] != "POST") 
		{
			$_POST +=$_GET;
		}
		if( isset($_POST['respcode']) && $_POST['respcode'] == '0' )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			 $query->select(array('COUNT(*)'))->from($db->quoteName('#__hikashop_history'))
			 ->where($db->quoteName('history_data') . ' LIKE '. $db->quote('%'.$_POST['digitalreceipt'].'%'));
			$db->setQuery($query);
			$results = $db->loadResult();
			if($results == 0)
			{
				 $payResult = pasClass::getPas($_POST,$MerchantID); 
			}else
			{
				$payResult[0] = 0;
				$payResult[1] = 'رسید قبلا استفاده شده است';
			}
		}
		else
		{
			$payResult[0] = 0;
			$payResult[1] = $_POST['respmsg'];
		}
		
		
        if($payResult[0])
		{
				$referenceId = $payResult[2];
                    $order = new stdClass();
                    $order->order_id = $dbOrder->order_id;
                    $order->old_status->order_status=$dbOrder->order_status;
                    $url = HIKASHOP_LIVE.'administrator/index.php?option=com_hikashop&ctrl=order&task=edit&order_id='.$order->order_id;
                    $order_text = "\r\n".JText::sprintf('NOTIFICATION_OF_ORDER_ON_WEBSITE',$dbOrder->order_number,HIKASHOP_LIVE);
                    $order_text .= "\r\n".str_replace('<br/>',"\r\n",JText::sprintf('ACCESS_ORDER_WITH_LINK',$url));
               
                    
                    
                    echo 'Transation success. Transid:'. $referenceId . "\r\n\r\n";
                    
                    $mailer = JFactory::getMailer();
                    $config =& hikashop_config();
                    $sender = array(
                    $config->get('from_email'),
                    $config->get('from_name')
                    );
                    $mailer->setSender($sender);
                    $mailer->addRecipient(explode(',',$config->get('payment_notification_email')));
                    $order->order_status = 'confirmed';
                    $order->history->history_data = $referenceId;
                    $order->history->history_reason = JText::_('پرداخت سفارش با موفقیت تایید شد');
                    $order->history->history_notified=1;
                    $order->history->history_payment_id = $element->payment_id;
                    $order->history->history_payment_method =$element->payment_type;
                    $order->history->history_type = 'payment';

                    $order_status =  $order->order_status;
                    $order->mail_status=$statuses[$order->order_status];
					$orderClass->save($order);
					try
					{
                    $mailer->setSubject(JText::sprintf('PAYMENT_NOTIFICATION_FOR_ORDER','Pas',$order->mail_status,$dbOrder->order_number));
    
                    $body = str_replace('<br/>',"\r\n",JText::sprintf('PAYMENT_NOTIFICATION_STATUS','Pas',$order->mail_status)).' '.JText::sprintf('ORDER_STATUS_CHANGED',$order->mail_status).' '.JText::sprintf('شما پیگیری :',$referenceId)."\r\n\r\n".$order_text;
                    $mailer->setBody($body);
                    $mailer->Send();
					}catch(Exception $e)
					{
						echo $e;
					}
                    $order_num = $dbOrder->order_number;
                    $app =& JFactory::getApplication();
                    $httpsHikashop = HIKASHOP_LIVE;
                    $return_url = $httpsHikashop.'index.php?option=com_hikashop&ctrl=checkout&task=after_end&order_id='.$orderid.$this->url_itemid;
                    $app->enqueueMessage("<p style='color:green; font-weight:bold'>پرداخت شما با موفقیت انجام شد شماره تراکنش پرداخت: <span style='color:blue'>$referenceId</span></p>");
                    $app->enqueueMessage("<p style='color:green; font-weight:bold'>شماره فاکتور: <span style='color:blue'>$order_num</span></p>");
					
                    $app->redirect($return_url);
                    exit; 
                
                
                
            
            
               }else{
                    $order_status = $this->payment_params->pending_status;
                    $order_text = JText::sprintf('CHECK_DOCUMENTATION',HIKASHOP_HELPURL.'payment-pas-error#verify')."\r\n\r\n".$order_text;
                    $app =& JFactory::getApplication();
                    $httpsHikashop = HIKASHOP_LIVE;
                    $return_url = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=order&task=cancel_order&order_id='.$orderid.$this->url_itemid;
                    $app->enqueueMessage($payResult[1], 'error');
                    $app->redirect($return_url);
                    exit; 
                
               }

}

    function onPaymentConfiguration(&$element)
    {
          
          $subtask = JRequest::getCmd('subtask', '');

           parent::onPaymentConfiguration($element);
    }
    
  function getPaymentDefaultValues(&$element)
    {
        $element->payment_name = 'Pas';
        $element->payment_description='You can pay by pas using this payment method';
        $element->payment_images = 'Pas';

        $element->payment_params->url =  JURI::root();
        $element->payment_params->invalid_status = 'cancelled';
        $element->payment_params->pending_status = 'created';
        $element->payment_params->verified_status = 'confirmed';
    }  

}
