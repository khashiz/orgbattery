<?php


class pasClass{
    
    public static function setPas($Amount,$mid,$terminal,$revertURL)
	{
		$amount = trim($Amount);
		$invoiceNumber = time();
		$redirectAddress = $revertURL; 
		$terminal = trim($terminal);
		$session = JFactory::getSession();
		$session->set('amount',$amount); 
		 
		$params ='terminalID='.$terminal.'&Amount='.$amount.'&callbackURL='.urlencode($redirectAddress).'&invoiceID='.$invoiceNumber;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://sepehr.shaparak.ir:8081/V1/PeymentApi/GetToken');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($ch);
		curl_close($ch);
		if($res)
		{
			$res = json_decode($res,true);
			if($res['Status'] == '0')
			{	  
				$setPayment = '<form id="paymentUTLfrm" action="https://sepehr.shaparak.ir:8080" method="POST">
					<input type="hidden" id="TerminalID" name="TerminalID" value="'.$terminal.'">
					<input type="hidden" id="token" name="token" value="'.$res['Accesstoken'].'">
					<input type="hidden" id="getMethod" name="getMethod" value="1">
					</form><script>
					function submitpas() {
					document.getElementById("paymentUTLfrm").submit();
					}
					window.onload=submitpas; </script>';
			}else
			{
				$setPayment =('خطا در ساخت توکن <br/> کد خطا :'.$res['Status']);
			}
		}
		else
		{
			$setPayment = ('پورت 8081 در هاست شما بسته است !');
		}
	
		return $setPayment;
    }
    
    public static function getPas($POST,$terminal)
	{
        
		$terminal 	= trim($terminal);
		$session = JFactory::getSession();
		if( isset($POST['respcode']) && $POST['respcode'] == '0' )
		{
			$params ='digitalreceipt='.$POST['digitalreceipt'].'&Tid='.$terminal;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://sepehr.shaparak.ir:8081/V1/PeymentApi/Advice');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($res,true);
			 if (strtoupper($result['Status']) == 'OK') 
			 {
				$amount = $session->get('amount');
				if($result['ReturnId'] == $amount)
				{
					$session->set('amount',0);
					$referenceId = $POST['digitalreceipt'];
					return array(1,"با تشکر پرداخت شما با موفقت انجام شد. شماره تراکنش پرداخت: $referenceId",$referenceId);
				}else
				{
					$session->set('amount',0);
					$err = 'مبلغ واریزی با قیمت محصول برابر نیست';
					return array(0,$err);
				}
			} else 
			{
				$session->set('amount',0);
				switch($result['ReturnId'])
					{
						case '-1' : $err = 'تراکنش پیدا نشد';break;
						case '-2' : $err = 'تراکنش قبلا Reverse شده است';break;
						case '-3' : $err = 'خطا عمومی';break;
						case '-4' : $err = 'امکان انجام درخواست برای این تراکنش وجود ندارد';break;
						case '-5' : $err = 'آدرس IP پذیرنده نامعتبر است';break;
						default  : $err = 'خطای ناشناس : '.$result['ReturnId'];break;
						
					}
				return array(0,$err);
			}
				
		}else
		{
			$session->set('amount',0);
			return array(0,$POST['respmsg']);
		}
		
    }
    
}

?>