<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'signature_id', 0);
$user_id = ( int ) apmgetParam ( $_GET, 'oid', 0);
$signature_mod_name=apmgetParam ( $_GET, 'mod', 0 );
//$signature_mod=$signature->getModuleName($signature_mod);
$signature_row =apmgetParam( $_GET, 'id');


$object = new CSignature ();
$object->setId ( $object_id );

$AppUI->getTheme ()->loadCalendarJS ();

$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canView = $obj->canView ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canDelete = $object->canDelete ();
if (! $canView) {
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
	$AppUI->setMsg ( 'Signature' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

// setup the title block
$ttl = $object_id ? 'Edit Signature' : 'Add Signature';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete signature', $canDelete, $msg );
}
$titleBlock->show ();



// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'signatures' );

$view = new apm_Controllers_View ( $AppUI, $object, 'Signature' );
echo $view->renderDelete ();

include ('style/_common/view.php');