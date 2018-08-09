<?php
$object_id = ( int ) apmgetParam ( $_GET, 'signature_id', 0);
$object = new CSignature ();
$object->setId ( $object_id );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canView = $obj->canView ();
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
$name = $AppUI->_('Signature_file').'_'.$object->signature_name.'.xml';
header('Content-Disposition: attachment;filename=' . $name);
header("Content-Type: text/xml");
echo $object->signature_source;
?>