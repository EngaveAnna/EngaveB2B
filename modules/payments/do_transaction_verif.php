<?php 
// check request IP and POST vars 
$apmURL='http://localhost/apm';
$remoteIP='195.149.229.109';
if(($_SERVER['REMOTE_ADDR']==$remoteIP||apm_BASE_URL==$apmURL) && !empty($_POST))
if(!empty($_POST))
{
	$object=new CPayment();
	$payment=$object->getPaymentById($_POST['tr_crc'],false);
	if(!empty($payment[0]))
	{
		$company=$object->getCompanyPaymentByInviceId($payment[0]['payment_order']);
	
		$payconf=array(
			'crc'=>$payment_id,
			'payment_action'=>apmgetConfig('payment_action'),
			'url_verif'=>apm_BASE_URL.apmgetConfig('payment_verif'),
			'url_powrotu'=>apm_BASE_URL.apmgetConfig('payment_back').'&payment_id='.$payment[0]['payment_id'],
			'owner_firstname'=>$AppUI->user_first_name,
			'owner_lastname'=>$AppUI->user_last_name,
			'owner_email'=>$AppUI->user_email,
			'company_paymentid'=>$company[0]['company_paymentid'],
			'topay'=>$payment[0]['payment_amount'],
		);
		
		
		$id_sprzedawcy		= $_POST['id']; 
		$status_transakcji 	= $_POST['tr_status']; 
		$id_transakcji 		= $_POST['tr_id']; 
		$kwota_transakcji 	= $_POST['tr_amount']; 
		$kwota_zaplacona	= $_POST['tr_paid']; 
		$blad				= $_POST['tr_error']; 
		$ciag_pomocniczy	= $_POST['tr_crc']; 
		$suma_kontrolna		= $_POST['md5sum'];
			
		// check the transaction state 
		if(md5($payconf['company_paymentid'].$id_transakcji.$kwota_transakcji.$ciag_pomocniczy.$company[0]['company_paymentkey']) == $suma_kontrolna)
		{
			if(!empty($payment[0]['payment_result']))
			$result=unserialize($payment[0]['payment_result']);
			
			$result[]=array(
				'id' => $_POST['id'],
				'tr_status' => $_POST['tr_status'],
				'tr_id'	=> $_POST['tr_id'],
				'tr_amount'	=> $_POST['tr_amount'],
				'tr_paid' => $_POST['tr_paid'],
				'tr_error' => $_POST['tr_error'],
				'tr_crc' => $_POST['tr_crc'],
				'md5sum' => $_POST['md5sum'],
			);
					
			$tr_date=date('Y-m-d H:i:s');
			if($status_transakcji!='TRUE' || $blad!='none')
			{
				$status=5;
				$object->updatePaymentByPost($payment[0]['payment_id'], $status, serialize($result),$payment[0]['payment_paid'],$tr_date);
			}
			else 
			{		
				$paidPayment=$payment[0]['payment_paid']+$_POST['tr_paid'];
				if($payment[0]['payment_amount']==$paidPayment)
				{
					$statusPayment=2;
				}
				elseif($payment[0]['payment_amount']>$paidPayment)
				{
					$statusPayment=3;
				}
				else
				{
					$statusPayment=4;
				}
				
				
				$inv=new CInvoice();
				$invoice=$inv->getInvoiceById($payment[0]['payment_order'], false);
				if(!empty($invoice[0]))
				{
					$paidInvoice=$invoice[0]['invoice_payed']+$object->getPaymantCalculate($payment[0]['payment_order'])+$payment[0]['payment_paid']+$_POST['tr_paid'];

					if($invoice[0]['invoice_total_pay']==$paidInvoice)
					{
						$statusInvoice=2;
					}
					elseif($invoice[0]['invoice_total_pay']>$paidInvoice)
					{
						$statusInvoice=3;
					}
					else
					{
						$statusInvoice=4;
					}
		
					$object->updateInvoicePayStatus($payment[0]['payment_order'], $statusInvoice);
				}
				$object->updatePaymentByPost($payment[0]['payment_id'], $statusPayment, serialize($result), $paidPayment, $tr_date);
			}
		}
	}
}
echo 'TRUE'; // response to payment service server
?>