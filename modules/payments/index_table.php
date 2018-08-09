<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $tab, $search_string, $m;
$payment = new CPayment ();

$type_filter = ($m == 'payments') ? $tab - 1 : - 1;
$order_id = apmgetParam ( $_GET, 'order_id', 0 );


if(!empty($order_id) && is_numeric($order_id))
{
	$inv=new CInvoice();
	if($inv->getInvoiceById($order_id )!=null)
	$items = $payment->getPaymentsByOrderId($order_id);
	else $items=array();
}
else 
$items = $payment->getPaymentsByCategory ( $type_filter, $search_string );

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );



$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'payments', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
		'payment_date',
		'payment_name',
		'payment_amount',
		'payment_type',
		'payment_category',
		'payment_owner',
		'payment_options'
	);
	$fieldNames = array (
		'Payment date',
		'Payment title',
		'Payment amount',
		'Payment type',
		'Payment category',
		'Payment owner',
		'Options'
	);
	$modulePriority = array (1,1,1,1,1,4,1);
	$module->storeSettings ('payments', 'index_list', $fieldList, $fieldNames, $modulePriority);
	$fields = array_combine ($fieldList, $fieldNames);
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$payment_type = apmgetSysVal ( 'PaymentType' );
$payment_status = apmgetSysVal ( 'PaymentStatus' );
$customLookups = array (
		'payment_type' => $payment_type,
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {
	$res=abs($item['payment_amount']-$item['payment_paid']);
	switch($item ['payment_category'])
	{
		case 3:
			$item ['payment_category']='<a class="btn btn-xs btn-info" role="button" href="./index.php?m=payments&a=do_transaction&order_id='.$item ['payment_order'] . '"><span class="glyphicon glyphicon-bell"></span>'.$AppUI->_($payment_status[$item ['payment_category']]).'</a><label class="btn btn-default btn-xs">'.number_format($res, 2, ',', '').' '.$apmconfig['currency_symbol'].'</label>';
		break; 
		case 4:
			$item ['payment_category']='<a class="btn btn-xs btn-info" role="button" href="./index.php?m=payments&a=do_transaction&order_id='.$item ['payment_order'] . '"><span class="glyphicon glyphicon-bell"></span>'.$AppUI->_($payment_status[$item ['payment_category']]).'</a><label class="btn btn-default btn-xs">'.number_format($res, 2, ',', '').' '.$apmconfig['currency_symbol'].'</label>';
		break;
		default:
			$item ['payment_category']=$AppUI->_($payment_status[$item ['payment_category']]);
		break;
	}
	$item ['payment_options'] = '';
	if (canEdit ( 'payments' )) {
		$item ['payment_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=payments&a=addedit&payment_id=' . $item ['payment_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	if (canDelete ( 'payments' )) {
		$item ['payment_options'] .= '<form name="frm_remove_payment_' . $item ['payment_id'] . '" action="?m=payments" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_payment_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="payment_id" value="' . $item ['payment_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['payment_options'] .= '<a class="btn btn-xs btn-danger" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this payment?' ) . '\')) {document.frm_remove_payment_' . $item ['payment_id'] . '.submit()}"><span class="glyphicon glyphicon-remove"></span></a>';
		// $s .= $hidden_table;
	}
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;