<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
require_once 'invoices_tpl.class.php';
// @todo convert to template
global $AppUI, $showProject, $tab, $search_string, $m;

$type_filter = ($m == 'invoices') ? $tab - 1 : - 1;

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );

$invoice = new CInvoiceTemplate ();
$items = $invoice->getTemplatesByCategory ( null, $type_filter, $search_string );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'invoices_templates', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
		'template_name', 
		'template_date',
		'template_owner',
		'template_options',
	);
	$fieldNames = array (
		'Template name',
		'Template date',
		'Template owner',
		'Options',
	);
	
	$modulePriority = array(1,1,1,1);
	$module->storeSettings ('invoices_templates', 'index_list', $fieldList, $fieldNames, $modulePriority);
	$fields = array_combine ($fieldList, $fieldNames);
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$invoice_types = apmgetSysVal ( 'InvoiceTemplateCategory' );
$customLookups = array (
		'template_category' => $invoice_types 
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {
	$item ['template_options'] = '';
	if (canEdit ( 'invoices' )) {
		$item ['template_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=invoices&a=addedit_tpl&template_id=' . $item ['template_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	if (canDelete ( 'invoices' )) {
		$item ['template_options'] .= '<form name="frm_remove_template_' . $item ['template_id'] . '" action="?m=invoices" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_invoice_aed_tpl" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="template_id" value="' . $item ['template_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['template_options'] .= '<a class="btn btn-xs btn-danger" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this template?' ) . '\')) {document.frm_remove_template_' . $item ['template_id'] . '.submit()}"><span class="glyphicon glyphicon-remove"></span></a>';
		
		// $s .= $hidden_table;
	}
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;