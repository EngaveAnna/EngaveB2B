<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project_id, $task_id, $showProject, $tab, $search_string, $m;

$type_filter = ($m == 'agreements') ? $tab - 1 : - 1;

if ($task_id && ! $project_id) {
	$task = new CTask ();
	$task->load ( $task_id );
	$project_id = $task->task_project;
}

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );

if (! isset ( $project_id )) {
	$project_id = ( int ) apmgetParam ( $_POST, 'project_id', 0 );
}

$agreement = new CAgreement ();
$items = $agreement->getAgreementsByCategory ( null, $type_filter, $search_string, $AppUI->user_id );
$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'agreements', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
		'agreement_date',
		'agreement_category',
		'agreement_name',
		'agreement_datestart',
		'agreement_dateend',
 		'agreement_options'
	);
	$fieldNames = array (
		'Date',
		'Category',
		'Agreement title',
		'Agreement start',
		'Agreement stop',
		'Options'
	);
	
	$modulePriority = array (1,1,1,1,1,1);
	$module->storeSettings ('agreements', 'index_list', $fieldList, $fieldNames, $modulePriority);
	$fields = array_combine ($fieldList, $fieldNames);
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$agreement_types = apmgetSysVal ( 'AgreementCategory' );

$agreement_payment = apmgetSysVal ( 'AgreementPaymentType' );
$customLookups = array (
		'agreement_payment_types' => $agreement_payment_types,
		'agreement_category' => $agreement_types 
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {
	$item ['agreement_options'] = '';
	if(empty($item['sign_ued']))
	{
		if (canEdit ( 'agreements' )) 
		$item ['agreement_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=agreements&a=addedit&agreement_id=' . $item ['agreement_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	else {
		if (canView ( 'agreements' ))
		$item ['agreement_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=agreements&a=view&agreement_id=' . $item ['agreement_id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a>';
	}
	if (canView ( 'agreements' )) {
	
		$uarray=explode(',', $item['sign_u']);
		$uedarray=explode(',', $item['sign_ued']);
	
		if(in_array($AppUI->user_id, $uarray))
		{
			if(in_array($AppUI->user_id, $uedarray))
				$item ['agreement_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=signatures&mod='.$m.'&id='.$item['agreement_id'].'"><span class="fa fa-key text-success"></span></a>';
			else
			{
				$item ['agreement_options'] .= '<a class="btn btn-xs btn-success" role="button" href="./index.php?m=signatures&a=addedit&mod='.$m.'&id=' . $item['agreement_id']. '"><span class="fa fa-key"></span></a>';
			}
		}
	}
	if (canView ( 'agreements' )) {
		$item ['agreement_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=agreements&a=do_agreement_pdf&id=' . $item ['agreement_id'] . '&typePDF=tosign&suppressHeaders=true"><span class="fa fa-file-text-o text-danger"></span></a>';
	}

	if (canDelete ( 'agreements' )) {
		$item ['agreement_options'] .= '<form name="frm_remove_agreement_' . $item ['agreement_id'] . '" action="?m=agreements" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_agreement_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="agreement_id" value="' . $item ['agreement_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['agreement_options'] .= '<a class="btn btn-xs btn-default" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this agreement?' ) . '\')) {document.frm_remove_agreement_' . $item ['agreement_id'] . '.submit()}"><span class="glyphicon glyphicon-remove text-danger"></span></a>';
	
		// $s .= $hidden_table;
	}	
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;