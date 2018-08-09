<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $tab, $search_string, $m;
$valuation = new CValuation ();

$type_filter = ($m == 'valuations') ? $tab - 1 : - 1;
$order_id = apmgetParam ( $_GET, 'order_id', 0 );


if(!empty($order_id) && is_numeric($order_id))
{
	$inv=new CInvoice();
	if($inv->getInvoiceById($order_id )!=null)
	$items = $valuation->getValuationsByOrderId($order_id);
	else $items=array();
}
else 
$items = $valuation->getValuationsByCategory ( $type_filter, $search_string );

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );
$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'valuations', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
		'valuation_create_date',
		'valuation_name',
		'valuation_category',
		'valuation_owner',
		'valuation_date',
		'valuation_project',			
		'valuation_days',
		'valuation_amount',
		'valuation_options'
	);
	$fieldNames = array (
		'Valuation create',
		'Valuation title',
		'Valuation type',
		'Valuation owner',
		'Valuation date',
		'Valuation project',
		'Valuation days',
		'Valuation amount',
		'Valuation options'
	);
	$modulePriority = array (6,4,1,1,4,1,1,5,1);
	$module->storeSettings ('valuations', 'index_list', $fieldList, $fieldNames, $modulePriority);
	$fields = array_combine($fieldList, $fieldNames);
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$valuation_type = apmgetSysVal ( 'ValuationType' );

$customLookups = array (
		'valuation_category' => $valuation_type,
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {

	if (canEdit ( 'valuations' )) {
		$item ['valuation_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=valuations&a=addedit&valuation_id=' . $item ['valuation_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	if (canDelete ( 'valuations' )) {
		$item ['valuation_options'] .= '<form name="frm_remove_valuation_' . $item ['valuation_id'] . '" action="?m=valuations" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_valuation_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="valuation_id" value="' . $item ['valuation_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['valuation_options'] .= '<a class="btn btn-xs btn-danger" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this valuation?' ) . '\')) {document.frm_remove_valuation_' . $item ['valuation_id'] . '.submit()}"><span class="glyphicon glyphicon-remove"></span></a>';
		// $s .= $hidden_table;
	}
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;