<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$module = new apm_System_Module ();
$fields = $module->loadSettings ( $m, 'index_list' );

if (0 == count ( $fields )) {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'report_name',
			'report_desc' 
	);
	$fieldNames = array (
			'Report name',
			'Description' 
	);
	$module->storeSettings ( $m, 'index_list', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
include $AppUI->getTheme ()->resolveTemplate ( 'list' );
?>