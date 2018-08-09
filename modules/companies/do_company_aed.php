<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$controller = new apm_Controllers_Base ( new CCompany (), $delete, 'Company', 'm=companies', 'm=companies&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );