<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'payment_id', 0 );
$object = new CPayment ();
$object->setId ( $object_id );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canDelete = $object->canDelete ();
if (! $canAddEdit) {
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
	$AppUI->setMsg ( 'Payment' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

global $AppUI, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();
$df = $AppUI->getPref ( 'SHDATEFORMAT' ).' ' . $AppUI->getPref ( 'TIMEFORMAT' );

// setup the title block
$ttl = $object_id ? 'Edit Payment' : 'Add Payment';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete payment', $canDelete, $msg );
}
$titleBlock->show ();


$types = apmgetSysVal ( 'PaymentType' );

// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'payments' );
$view = new apm_Controllers_View ( $AppUI, $object, 'Payment' );

$paymentStatus = apmgetSysVal ('PaymentStatus');
$paymentType = apmgetSysVal ('PaymentType');


// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'payments' );
$view = new apm_Controllers_View ( $AppUI, $object, 'payment' );

?>
<script language="javascript" type="text/javascript">

function submitIt() {
	var f = document.editFrm;
	f.submit();
}
</script>

<?php
echo $view->renderDelete();
include ( 'style/_common/addedit.php' );
?>
