<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template

$tr = apmgetParam ( $_GET, 'tr', 0 );
$object_id = ( int ) apmgetParam ( $_GET, 'valuation_id', 0 );

$object = new CValuation ();
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
			$AppUI->redirect ('m=valuations');
		}
		else
		{
			$AppUI->setMsg ( 'Valuation was credited properly', UI_MSG_OK,true );
			$AppUI->redirect ('m=valuations');
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
	$AppUI->setMsg ( 'valuation' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}



// setup the title block
$ttl = 'View valuation';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
}
$titleBlock->show ();

$valuationStatus = apmgetSysVal ('ValuationStatus');
$valuationType = apmgetSysVal ('ValuationType');
$invoiceCategory = apmgetSysVal('ValuationCategory');

// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'valuations' );
$view = new apm_Controllers_View ( $AppUI, $object, 'valuation' );




include ( 'style/_common/view.php' );