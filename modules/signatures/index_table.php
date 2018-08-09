<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project_id, $task_id, $showProject, $tab, $search_string, $m;

$type_filter = ($m == 'signatures') ? $tab - 1 : - 1;
$page = ( int ) apmgetParam ( $_GET, 'page', 1 );

$signature = new CSignature ();
$signature_mod_name=apmgetParam ( $_GET, 'mod', 0 );
$signature_row =apmgetParam( $_GET, 'id');
$object=new CSignature();

if($signature_mod_name&&$signature_row)
{
	$items=$object->getSignatureBySpecData($signature_mod_name,$signature_row,$AppUI->user_id);
}
else
{
	$items = $signature->getSignaturesByMod('', $search_string);
}

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'signatures', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'signature_date',
			'signature_owner',
			'signature_mod',
			'signature_name',			
			'signature_options',
	);
	$fieldNames = array (
			'Signature date',
			'Owner',
			'Module',
			'Signature id',
			'Options',
	);
	
	$modulePriority = array (1,1,1,3,1);
	
	$module->storeSettings ('signatures', 'index_list', $fieldList, $fieldNames, $modulePriority);
	$fields = array_combine ( $fieldList, $fieldNames );
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$signature_mods= $signature->getModuleName();
//APM translate
foreach ($signature_mods as $key=>$val)
$signature_mods[$key] = $AppUI->_($val);

$customLookups = array (
	'signature_mod' => $signature_mods,
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {
	$item ['signature_options'] = '';
	if (canView ( 'signatures' )) {
		$item ['signature_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=signatures&a=view&signature_id=' . $item ['signature_id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a>';
		$item ['signature_options'] .= '<a class="btn btn-xs btn-success" role="button" href="./index.php?m=signatures&a=do_verification_file&signature_id='.$item ['signature_id'].'&suppressHeaders=true"><span class="glyphicon glyphicon-file"></span></a>';
	}
	if (canDelete ( 'signatures' )) {
		$item ['signature_options'] .= '<form name="frm_remove_signature_' . $item ['signature_id'] . '" action="?m=signatures" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_signature_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="signature_id" value="' . $item ['signature_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['signature_options'] .= '<a class="btn btn-xs btn-default" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this signature?' ) . '\')) {document.frm_remove_signature_' . $item ['signature_id'] . '.submit()}"><span class="glyphicon glyphicon-remove text-danger"></span></a>';
		// $s .= $hidden_table;
	}
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;