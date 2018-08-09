<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$module_id = ( int ) apmgetParam ( $_POST, 'module', 0 );
$module = new apm_System_Module ();
$module->load ( $module_id );
$_POST ['field_module'] = $module->mod_name;

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$controller = new apm_Controllers_Base ( new apm_Core_CustomFieldManager (), $delete, 'Custom Fields', 'm=system&u=customfields', 'm=system&u=customfields&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );