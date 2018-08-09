<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project_id, $task_id, $showProject, $tab, $search_string, $m;

$type_filter = ($m == 'invoices') ? $tab - 1 : - 1;

if ($task_id && ! $project_id) {
	$task = new CTask ();
	$task->load ( $task_id );
	$project_id = $task->task_project;
}

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );

if (! isset ( $project_id )) {
	$project_id = ( int ) apmgetParam ( $_POST, 'project_id', 0 );
}

$invoice = new CInvoice ();
$items = $invoice->getInvoicesByCategory ( null, $type_filter, $search_string, $AppUI->user_id );
/* 
echo "<pre>";
print_r($items);
echo "</pre>"; */

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'invoices', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
		'invoice_name_options',
		'invoice_sale_date',
		'invoice_issue_date',
		'company_name',
		'invoice_total_pay_options',
 		'invoice_options'
	);
	$fieldNames = array (
		'Invoice name',
		'Date sale',			
		'Date issue',
		'Contractor',
		'Total pay',
		'Options'
	);
	
	$modulePriority = array (1,3,1,1,6,1);
	$module->storeSettings ('invoices', 'index_list', $fieldList, $fieldNames, $modulePriority);
	$fields = array_combine ($fieldList, $fieldNames);
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$invoice_types = apmgetSysVal ( 'InvoiceCategory' );
$payment_status = apmgetSysVal ( 'PaymentStatus' );

$customLookups = array (
		'invoice_category' => $invoice_types,
);

$listTable = new apm_Output_ListTable ( $AppUI );
//$listTable->df .= ' ' . $AppUI->getPref ( 'SHDATEFORMAT' );

//APM show invoice payments button for pay statuses;
$show_paystatus=array(2,3,4);

foreach ( $items as $key => $item ) {
	$item ['invoice_options'] = '';
	switch($item['invoice_paystatus'])
	{
			case 1:
			$item ['invoice_total_pay_options'] = '<label class="btn btn-default btn-xs">'.$item ['invoice_total_pay'].'</label><a class="btn btn-xs btn-info" role="button" href="./index.php?m=payments&a=do_transaction&order_id='.$item ['invoice_id'] . '"><span class="glyphicon glyphicon-bell"></span>'.$AppUI->_('Pay invoice').'</a>';
			break;			
			case 2:
			$item ['invoice_name'] = $item ['invoice_name'].'<label class="btn btn-xs btn-success" role="button" href="./index.php?m=payments&a=do_transaction&order_id='.$item ['invoice_id'] . '"><span class="glyphicon glyphicon-ok data-original-title="' . $AppUI->_($payment_status[$item['invoice_paystatus']]) . '" data-container="body" data-toggle="tooltip" data-placement="right"></span></label>';
			$item ['invoice_total_pay_options']=$item ['invoice_total_pay'];
			break;
			default:
			$item ['invoice_total_pay_options'] = '<label class="btn btn-default btn-xs">'.$item ['invoice_total_pay'].'</label><a class="btn btn-xs btn-info" role="button" href="./index.php?m=payments&a=do_transaction&order_id='.$item ['invoice_id'] . '"><span class="glyphicon glyphicon-bell"></span>'.$AppUI->_($payment_status[$item['invoice_paystatus']]).'</a>';
			break;
	}
	
	if(empty($item['sign_ued']))
	{
		if (canEdit ( 'invoices' )) 
		$item ['invoice_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=invoices&a=addedit&invoice_id=' . $item ['invoice_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	else {
		if (canView ( 'invoices' ))
		$item ['invoice_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=invoices&a=view&invoice_id=' . $item ['invoice_id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a>';
	}
	
	if (canView ( 'invoices' )) {
	
		$uarray=explode(',', $item['sign_u']);
		$uedarray=explode(',', $item['sign_ued']);
	
		if(in_array($AppUI->user_id, $uarray))
		{
			if(in_array($AppUI->user_id, $uedarray))
			$item ['invoice_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=signatures&mod='.$m.'&id='.$item['invoice_id'].'"><span class="fa fa-key text-success"></span></a>';
			else
			$item ['invoice_options'] .= '<a class="btn btn-xs btn-success" role="button" href="./index.php?m=signatures&a=addedit&mod='.$m.'&id=' . $item['invoice_id']. '"><span class="fa fa-key"></span></a>';
		}
	}
	
	if (canView ( 'invoices' )&&in_array($item['invoice_paystatus'], $show_paystatus)) {
		$item ['invoice_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=payments&order_id=' . $item ['invoice_id'] . '"><span class="glyphicon glyphicon-credit-card text-info"></span></a>';
	}
		
	if (canView ( 'invoices' )) {
		$item ['invoice_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=invoices&a=do_invoice_pdf&id=' . $item ['invoice_id'] . '&typePDF=tosign&suppressHeaders=true"><span class="fa fa-file-text-o text-danger"></span></a>';
	}

	if (canDelete ( 'invoices' )) {
		$item ['invoice_options'] .= '<form name="frm_remove_invoice_' . $item ['invoice_id'] . '" action="?m=invoices" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_invoice_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="invoice_id" value="' . $item ['invoice_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['invoice_options'] .= '<a class="btn btn-xs btn-default" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this invoice?' ) . '\')) {document.frm_remove_invoice_' . $item ['invoice_id'] . '.submit()}"><span class="glyphicon glyphicon-remove text-danger"></span></a>';
		// $s .= $hidden_table;
	}	
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;