<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once('items.class.php');
require_once('invoices_tpl.class.php');

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );
$invoice=new CInvoice();

$item=new CItem();

$itemSchema=array(
		'0'=>array('name'=>'item_name', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'30%'),
		'1'=>array('name'=>'item_pkwiu', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left'),
		'2'=>array('name'=>'item_quantity', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right'),
		'3'=>array('name'=>'item_unit', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left'),
		'4'=>array('name'=>'item_unit_price', 'regex'=>'/^[\d]{1,8}$|^[\d]{1,8}[,.]{1}[\d]{1,2}$/', 'type'=>'text', 'align'=>'right'),
		'5'=>array('name'=>'item_tax_rare', 'regex'=>'/^[0-9]{1,9}$/', 'type'=>'select', 'align'=>'right')
);

$itemDocSchema=array(
		'0'=>array('name'=>'item_name', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'30%'),
		'1'=>array('name'=>'item_pkwiu', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left'),
		'2'=>array('name'=>'item_quantity', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right'),
		'3'=>array('name'=>'item_unit', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left'),
		'4'=>array('name'=>'item_unit_price', 'regex'=>'/^[\d]{1,8}$|^[\d]{1,8}[,.]{1}[\d]{1,2}$/', 'type'=>'price', 'align'=>'right'),
		'5'=>array('name'=>'item_net_val', 'regex'=>'/^[0-9]{1,9}$/', 'type'=>'item_net_val', 'align'=>'right'),
		'6'=>array('name'=>'item_tax_rare', 'regex'=>'/^[0-9]{1,9}$/', 'type'=>'select', 'align'=>'right')
);

$resumeSchema=array(
		'0'=>array('name'=>'resume_tax_rare', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
		'1'=>array('name'=>'resume_net', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
		'2'=>array('name'=>'resume_tax_val', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),		
		'3'=>array('name'=>'resume_gross', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right', 'width'=>'25%')	
);


//APM controller remove only invoice
if($delete)
$item->delItemsByInvoice($_REQUEST['invoice_id'], $AppUI->user_id);

if(!$delete)
{
	if (empty($_POST['invoice_template'])) {
		$AppUI->setMsg ( $AppUI->_('Error, data not stored').'. ' );
		$AppUI->setMsg ( $AppUI->_('Select invoice template').'.', UI_MSG_ERROR, true );
		$AppUI->redirect ( 'm=' . $m );
	}
	else
	{
		$ct= new CInvoiceTemplate();
		$tpl=$ct->getTemplatesById($_POST['invoice_template'], false);
		$topay=$invoice->getTopayVal($resumeSchema, $_POST['invoice_items']);
		$_POST['invoice_total_pay']=$topay['resume_gross'];
		$_POST['invoice_topay']=$_POST['invoice_total_pay']-$_POST['invoice_payed'];
		$_POST['invoice_topay_say']=$invoice->slownie(number_format($_POST['invoice_topay'], 2, '.', ''));
		
		//APM must be at last
		$_POST['sign_src']=$_POST['invoice_source']=$invoice->sourceInterpreter($tpl[0]['template_source'], $_POST, $itemDocSchema, $resumeSchema);
		$_POST['sign_u']=trim(join(',', array($_POST['invoice_authorized_issue'],$_POST['invoice_authorized_receive'])), ",");
	
	}
}

$controller = new apm_Controllers_Base ( $invoice, $delete, 'Invoices', 'm=invoices', 'm=invoices&a=addedit' );
$AppUI = $controller->process ( $AppUI, $_POST );


//APM need $controller invoice_id for new invoice
if(!$delete)
{
	if($_REQUEST['invoice_id']!=0) $id=$_REQUEST['invoice_id']; else $id=$controller->object->invoice_id;
	$item->setItemsByInvoice($id, $AppUI->user_id, $itemSchema, $_POST['invoice_items'], $_POST);
}
$AppUI->redirect ( $controller->resultPath );