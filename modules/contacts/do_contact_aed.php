<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$contact = new CContact ();
// TODO: I don't like this particular hack but it's better than using the raw POST within the class
$contact->_contact_methods = empty ( $_POST ['contact_methods'] ) ? array () : $_POST ['contact_methods'];

$controller = new apm_Controllers_Base ( $contact, $delete, 'Contact', 'm=contacts', 'm=contacts&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );
