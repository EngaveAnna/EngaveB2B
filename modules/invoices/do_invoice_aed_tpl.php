<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
require_once 'invoices_tpl.class.php';
$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$controller = new apm_Controllers_Base ( new CInvoiceTemplate(), $delete, 'templates', 'm=invoices&submod=template', 'm=invoices&submod=template&a=addedit_tpl' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );