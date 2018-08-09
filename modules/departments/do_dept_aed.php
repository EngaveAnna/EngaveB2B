<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );
$company_id = ( int ) apmgetParam ( $_POST, 'dept_company', 0 );
$successPath = 'm=companies&a=view&company_id=' . $company_id;

$controller = new apm_Controllers_Base ( new CDepartment (), $delete, 'Department', $successPath, 'm=departments&a=addedit&company_id=' . $company_id );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );