<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$perms = $AppUI->acl ();
$canEdit = canEdit ( 'system' );
if (! $canEdit) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$mod_id = ( int ) apmgetParam ( $_POST, 'mod_id' );
$module = new apm_System_Module ();
$module->load ( $mod_id );

$moduleName = $module->mod_directory;
$configName = apmgetParam ( $_POST, 'module_config_name', '' );
$displayColumns = apmgetParam ( $_POST, 'display', array () );
$displayOrder = apmgetParam ( $_POST, 'order', array () );
$displayFields = apmgetParam ( $_POST, 'displayFields', array () );
$displayNames = apmgetParam ( $_POST, 'displayNames', array () );

$result = apm_System_Module::saveSettings ( $moduleName, $configName, $displayColumns, $displayOrder, $displayFields, $displayNames );

if ($result) {
	$AppUI->setMsg ( 'The module settings were saved successfully', UI_MSG_OK, true );
} else {
	$AppUI->setMsg ( 'There was an error saving the module settings', UI_MSG_ERROR );
}
$AppUI->redirect ( 'm=system&u=modules&a=addedit&mod_id=' . $mod_id . '&v=' . $configName );