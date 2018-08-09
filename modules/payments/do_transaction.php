<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $AppUI, $tab, $search_string, $m;
$df = $AppUI->getPref ( 'SHDATEFORMAT' ).' '.$AppUI->getPref ( 'TIMEFORMAT' );
$object_id = ( int ) apmgetParam ( $_GET, 'payment_id', 0 );
$order_id= (int) apmgetParam ( $_GET, 'order_id', 0 );
$object = new CInvoice ();
$object->setId ( $object_id );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canView = $obj->canView();
$canDelete = $object->canDelete ();
if (! $canView) {
	$AppUI->redirect ( ACCESS_DENIED );
}


$object->getInvoiceById($order_id, true);

if (! $object && $object_id > 0) {
	$AppUI->setMsg ( 'Payment' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}


// setup the title block
$ttl = $object_id ? 'Edit Payment' : 'Add Payment';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete payment', $canDelete, $msg );
}
$titleBlock->show();



// Load the users
$perms = &$AppUI->acl();
$users = $perms->getPermittedUsers ( 'payments' );
$view = new apm_Controllers_View ( $AppUI, $object, 'Payment' );

if(empty($order_id))
{
	if (! $canAddEdit) {
		$AppUI->redirect ( ACCESS_DENIED );
	}
	$paymentCategory=apmgetSysVal ( 'PaymentStatus' );
	include ('style/_common/addedit.php');
}
else
{
	$epayment=true;
	$cp=new CCompany();
	$company=$cp->getCompanyPayment($object->invoice_parties_owner);
//	print_r($company); exit();
	
	if(empty($company[0]['company_paymentid']) || empty($company[0]['company_paymentkey']))
	{
		$epayment=false;
	}
	else 
	{	
		$resumeSchema=array(
				'0'=>array('name'=>'resume_tax_rare', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
				'1'=>array('name'=>'resume_net', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
				'2'=>array('name'=>'resume_tax_val', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
				'3'=>array('name'=>'resume_gross', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right', 'width'=>'25%')
		);
		
		$paymentStatus = apmgetSysVal ('PaymentStatus');
		$paymentType = apmgetSysVal ('PaymentType');
		$projectPriority = apmgetSysVal ('ProjectPriority');
		$invoiceCategory = apmgetSysVal('InvoiceCategory');
		$invoicePaymnetType = apmgetSysVal('InvoicePaymentType');
		
		$pmt=new CPayment();		
		$payed=$pmt->getPaymantCalculate($object->invoice_id);
		$topay=($object->invoice_total_pay-$object->invoice_payed)-$payed;

		$settingsCreate=array(
			'payment_order'=>$object->invoice_id, 
			'payment_amount'=>$topay,
			'payment_category'=>1,
			'payment_owner'=>$AppUI->user_id,
			'payment_type'=>0,
			'payment_date'=>date('Y-m-d H:i:s'),
			'payment_name'=>substr(md5(date('U',time()), false), 0 ,12),
		);
		
		$payment=$pmt->getPaymentsByOrderId($object->invoice_id, 1);
		if($topay>0)
		{	
			if(!empty($payment))
			{
				$pmt->updatePaymentHelper($settingsCreate, $payment[0]['payment_id']);
				$payment_id=$payment[0]['payment_id'];
			}
			else
			{
				$pmt->createPaymentHelper($settingsCreate);
				$payment_id=$AppUI->last_insert_id;
			}
			$crc=$payment_id;
	
			$md5sum=md5($company[0]['company_paymentid'].$topay.$crc.$company[0]['company_paymentkey']);
			$settingsPay=array(
					'crc'=>$payment_id,
					'payment_action'=>apmgetConfig('payment_action'),
					'payment_verif'=>apm_BASE_URL.apmgetConfig('payment_verif'),
					'payment_back'=>apm_BASE_URL.apmgetConfig('payment_back').'&payment_id='.$payment_id.'&tr=true',
					'payment_back_error'=>apm_BASE_URL.apmgetConfig('payment_back').'&payment_id='.$payment_id.'&tr=false',
					'md5sum'=>$md5sum,
					'owner_firstname'=>$AppUI->user_first_name,
					'owner_lastname'=>$AppUI->user_last_name,
					'owner_email'=>$AppUI->user_email,
					'company_paymentid'=>$company[0]['company_paymentid'],
					'topay'=>$topay
					
			);
			
			if($payed==0)
			$topay_label=$paymentStatus[1];
			else
			$topay_label=$paymentStatus[3];
		}
		else 
		{
			if($topay<0)
			$topay_label=$paymentStatus[4];
			else
			$topay_label=$paymentStatus[2];
		}
	}
	include ('style/_common/transaction.php');
}