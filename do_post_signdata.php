<?php //header("Content-Type: text/xml");
$obj = new CSignature ();
$signature_mod_name=apmgetParam ( $_GET, 'mod', 0 );
$signature_row =apmgetParam( $_GET, 'id');

$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canDelete = $obj->canDelete ();
if (! $canAddEdit) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$get=$obj->postSignData($signature_mod_name, $signature_row, 'test'.date());

?>