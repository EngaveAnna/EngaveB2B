<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
// @todo remove database query

global $AppUI, $m;

$display_last_login = ! (( int ) apmgetParam ( $_REQUEST, 'tab', 0 ));

$fieldList = array ();
$fieldNames = array ();
$module = new apm_System_Module ();
// $fields = apm_System_Module::getSettings('users', 'index_table');
$fields = $module->loadSettings ( 'users', 'index_table' );

if (count ( $fields ) > 0) {
	$fieldList = array_keys ( $fields );
	$fieldNames = array_values ( $fields );
} else {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'contact_display_name',
			'user_username',
			'company_name',
			'dept_name' 
	);
	$fieldNames = array (
			'Real Name',
			'Login Name',
			'Company',
			'Department' 
	);
	// TODO: This doesn't save the columns yet as we can't allow customization yet.
}

$listTable = new apm_Output_ListTable ( $AppUI );

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $m );

$types = apmgetSysVal ( 'UserType' );
$customLookups = array (
		'user_type' => $types 
);

$perms = &$AppUI->acl ();

foreach ( $users as $row ) {
	if ($perms->isUserPermitted ( $row ['user_id'] ) != $canLogin) {
		continue;
	}
	
	$item ['users_options'] = '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=users&a=addedit&user_id=' . $row ['user_id'] . '"><span class="glyphicon glyphicon-edit" data-original-title="' . $this->_AppUI->_ ( "Edit" ) . '" data-container="body" data-toggle="tooltip" data-placement="right"></span></a>
	<a class="btn btn-xs btn-default" role="button" href="./index.php?m=users&a=view&tab=1&user_id=' . $row ['user_id'] . '"><span class="glyphicon glyphicon-user" data-original-title="' . $this->_AppUI->_ ( "View User" ) . '" data-container="body" data-toggle="tooltip" data-placement="right"></span></a>';
	$m = '';
	if (apmgetParam ( $_REQUEST, 'tab', 0 ) == 0) {
		$user_logs = __extract_from_vw_usr ( $row );
		
		if ($user_logs) {
			foreach ( $user_logs as $row_log ) {
				if ($row_log ['online'] == '1') {
					$m .= '<span>' . $row_log ['hours'] . ' ' . $AppUI->_ ( 'hrs.' ) . ' (' . $row_log ['idle'] . ' ' . $AppUI->_ ( 'hrs.' ) . ' ' . $AppUI->_ ( 'idle' ) . ') - ' . $AppUI->_ ( 'Online' );
				} else {
					$m .= '<span>' . $AppUI->_ ( 'Offline' );
				}
			}
		} else {
			$m .= '<span style="color: #777;">' . $AppUI->_ ( 'Never Visited' );
		}
		$m .= '</span>';
	}
	
	$item ['history_options'] = $m;
	$item ['contact_display_name_na'] = $row ['contact_display_name'];
	$item ['user_name_na'] = $row ['user_username'];
	$item ['company_name_na'] = $row ['company_name'];
	$item ['dept_name_na'] = $row ['dept_name'];
	$items [] = $item;
}

echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
