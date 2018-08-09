<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );
if($delete)
{
	$tcs=new CSignature ();
	$row=$tcs->getSignatureById($_POST['signature_id']);
	$tr=$tcs->getModuleName(null, $row[0]['signature_mod']);

	$tcs->signUpdateRelations($tr, $row[0]['signature_row'], $AppUI->user_id, 'category', 'del');
}

$controller = new apm_Controllers_Base ( new CSignature (), $delete, 'Signatures', 'm=signatures', 'm=signatures&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );
