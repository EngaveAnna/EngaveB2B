<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );
$object=new CPayment ();
$invoice=new CInvoice();

if($delete)
{

	$object->getPaymentById($_POST['payment_id'],true);
	$_POST['payment_order']=$object->payment_order;
	$paidPayment=($object->payment_type==0)? $object->payment_paid: 0-$object->payment_paid;
	$invoice->getInvoiceById($_POST['payment_order'], true);
	
	$paidInvoice=$invoice->invoice_payed+$object->getPaymantCalculate($_POST['payment_order'])-$paidPayment;
	if($invoice->invoice_total_pay==$paidInvoice)
	{
		$statusInvoice=2;
	}
	elseif($invoice->invoice_total_pay>$paidInvoice)
	{
		$statusInvoice=3;
	}
	else
	$statusInvoice=4;
	
	if($paidInvoice==0)
	$statusInvoice=1;
}	
else 
{
	$paidPayment=($_POST['payment_type']==0)? $_POST['payment_paid']: 0-$_POST['payment_paid'];
	$invoice->getInvoiceById($_POST['payment_order'], true);
	if($_POST['payment_amount']==$paidPayment)
	{
		$_POST['payment_category']=2;
	}
	elseif($_POST['payment_amount']>$paidPayment)
	{
		$_POST['payment_category']=3;
	}
	else
	$_POST['payment_category']=4;
		
		
	$paidInvoice=$invoice->invoice_payed+$object->getPaymantCalculate($_POST['payment_order'])+$paidPayment;
	if($invoice->invoice_total_pay==$paidInvoice)
	{
		$statusInvoice=2;
	}
	elseif($invoice->invoice_total_pay>$paidInvoice)
	{
		$statusInvoice=3;
	}
	else
	$statusInvoice=4;
	
	if($paidInvoice==0)
	$statusInvoice=1;
	
}
$object->updateInvoicePayStatus($_POST['payment_order'], $statusInvoice);



$controller = new apm_Controllers_Base ( $object, $delete, 'Payments', 'm=payments', 'm=payments&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );
