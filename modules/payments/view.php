<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template

$tr = apmgetParam ( $_GET, 'tr', 0 );
$object_id = ( int ) apmgetParam ( $_GET, 'payment_id', 0 );

$object = new CPayment ();
$object->setId ( $object_id );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canDelete = $object->canDelete ();
$canView = $object->canView();

if (!$canView) {
	if(isset( $_GET['tr']))
	{
		if(($tr!='true'))
		{
			$AppUI->setMsg ( 'An unexpected error occurred, the transaction has not been completed', UI_MSG_ERROR, true );
			$AppUI->redirect ('m=payments');
		}
		else
		{
			$AppUI->setMsg ( 'Payment was credited properly', UI_MSG_OK,true );
			$AppUI->redirect ('m=payments');
		}
	}		
	else
	$AppUI->redirect ( ACCESS_DENIED );
}

$obj = $AppUI->restoreObject ();
if ($obj) {
	$object = $obj;
	$object_id = $object->getId ();
} else {
	$object->load ( $object_id );
}

if (! $object && $object_id > 0) {
	$AppUI->setMsg ( 'payment' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}


if(isset( $_GET['tr']))
{
	if(($tr!='true'))
	{
		if($object->payment_category==1)
		$object->updatePaymentStatus(5, $object->payment_id);
			
		$AppUI->setMsg ( 'An unexpected error occurred, the transaction has not been completed', UI_MSG_ERROR, true );
		$AppUI->redirect ( 'm=' . $m );
	}
	else
	{
		if($object->payment_category==1)
		$object->updatePaymentStatus(0, $object->payment_id);
		
		$AppUI->setMsg ( 'Payment was credited properly', UI_MSG_OK,true );
		$AppUI->redirect ( 'm=' . $m );
	}
}

// setup the title block
$ttl = 'View payment';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete payment', $canDelete, $msg );
}
$titleBlock->show ();



$paymentStatus = apmgetSysVal ('PaymentStatus');
$paymentType = apmgetSysVal ('PaymentType');
$invoiceCategory = apmgetSysVal('InvoiceCategory');

// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'payments' );
$view = new apm_Controllers_View ( $AppUI, $object, 'payment' );

$invoice=new CInvoice();
$invoice->getInvoiceById($object->payment_order, true);

$cp=new CCompany();
$invoice_parties_owner=$cp->getCompanyPayment($invoice->invoice_parties_owner);
$payment_owner=new CContact();
$payment_owner->getContactById($object->payment_owner, true);

include ( 'style/_common/view.php' );