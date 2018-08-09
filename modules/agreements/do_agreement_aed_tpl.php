<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

if(!function_exists('classAutoLoader')){
	function classAutoLoader($class){
		include 'agreements_tpl.class.php';
	}
}
spl_autoload_register('classAutoLoader');

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$controller = new apm_Controllers_Base ( new CAgreementTemplate(), $delete, 'templates', 'm=agreements&submod=template', 'm=agreements&submod=template&a=addedit_tpl' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );