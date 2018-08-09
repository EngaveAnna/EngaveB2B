<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
// @todo remove database query

global $AppUI, $apmconfig, $canEdit, $canDelete, $stub, $where, $orderby;

/*
 * Flag value to determine if "logout user" button should show.
 * Could be determined by a configuration value in the future.
 */
$logoutUserFlag = true;
$canEdit = canEdit ( 'users' );
$canDelete = canDelete ( 'users' );

if (isset ( $_GET ['out_user_id'] ) && $_GET ['out_user_id'] && isset ( $_GET ['out_name'] ) && $_GET ['out_name'] && $canEdit && $canDelete) {
	$boot_user_id = apmgetParam ( $_GET, 'out_user_id', null );
	$boot_user_name = $_GET ['out_name'];
	$details = $boot_user_name . ' by ' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name;
	
	// one session or many?
	if ($_GET ['out_session'] && $_GET ['out_user_log_id']) {
		$boot_user_session = $_GET ['out_session'];
		$boot_user_log_id = apmgetParam ( $_GET, 'out_user_log_id', null );
		$boot_query_row = false;
	} else {
		if ($canEdit && $canDelete && $logoutUserFlag) {
			// query for all sessions open for a given user
			$r = new apm_Database_Query ();
			$r->addTable ( 'sessions', 's' );
			$r->addQuery ( 'DISTINCT(session_id), user_access_log_id' );
			$r->addJoin ( 'user_access_log', 'ual', 'session_user = user_access_log_id' );
			$r->addWhere ( 'user_id = ' . ( int ) $boot_user_id );
			$r->addOrder ( 'user_access_log_id' );
			
			// execute query and fetch results
			$r->exec ();
			$boot_query_row = $r->fetchRow ();
			if ($boot_query_row) {
				$boot_user_session = $boot_query_row ['session_id'];
				$boot_user_log_id = $boot_query_row ['user_access_log_id'];
			}
		}
	}
	
	do {
		if ($boot_user_id == $AppUI->user_id && $boot_user_session == $_COOKIE ['PHPSESSID']) {
			$AppUI->resetPlace ();
			$AppUI->redirect ( 'logout=-1' );
		} else {
			addHistory ( 'login', $boot_user_id, 'logout', $details );
			$session = new apm_System_Session ();
			$session->destroy ( $boot_user_session, $boot_user_log_id );
		}
		
		if ($boot_query_row) {
			$boot_query_row = $r->fetchRow ();
			if ($boot_query_row) {
				$boot_user_session = $boot_query_row ['session_id'];
				$boot_user_log_id = $boot_query_row ['user_access_log_id'];
			} else {
				$r->clear ();
			}
		}
	} while ( $boot_query_row );
	
	$msg = $boot_user_name . ' logged out by ' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name;
	$AppUI->setMsg ( $msg, UI_MSG_OK );
	$AppUI->redirect ( 'm=users&tab=3' );
}

$rows = __extract_from_vw_usr_sessions ( $orderby );

$tab = apmgetParam ( $_REQUEST, 'tab', 0 );

$fields = array (
		0 => array (
				'module_config_value' => 'user_username',
				'module_config_text' => 'Login Name',
				'module_config_priority' => 1 
		),
		1 => array (
				'module_config_value' => 'contact_last_name',
				'module_config_text' => 'Real Name',
				'module_config_priority' => 1 
		),
		2 => array (
				'module_config_value' => 'company_name',
				'module_config_text' => 'Company',
				'module_config_priority' => 4 
		),
		3 => array (
				'module_config_value' => 'date_time_in',
				'module_config_text' => 'Date Time IN',
				'module_config_priority' => 1 
		),
		4 => array (
				'module_config_value' => 'user_ip',
				'module_config_text' => 'Internet Address',
				'module_config_priority' => 4 
		),
		5 => array (
				'module_config_value' => 'user_options',
				'module_config_text' => 'Options',
				'module_config_priority' => 1 
		) 
);

$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
$listTable = new apm_Output_ListTable ( $AppUI );
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $m );

foreach ( $rows as $row ) {
	$htmlHelper->stageRowData ( $row );
	
	$s = '<tr>';
	$s .= $htmlHelper->createCell ( 'na', $row ['user_username'] );
	$s .= $htmlHelper->createCell ( 'na', $row ['contact_display_name'] );
	$s .= $htmlHelper->createCell ( 'contact_company', $row ['contact_company'] );
	$s .= $htmlHelper->createCell ( 'log_in_datetime', $row ['date_time_in'] );
	$s .= $htmlHelper->createCell ( 'user_ip', $row ['user_ip'] );
	$s .= '<td align="center" nowrap="nowrap">';
	
	if ($canEdit && $canDelete) {
		$s .= '<a class="btn btn-xs btn-default" role="button" onclick="javascript:window.location=\'./index.php?m=users&tab=3&out_session=' . $row ['session_id'] . '&out_user_log_id=' . $row ['user_access_log_id'] . '&out_user_id=' . $row ['u_user_id'] . '&out_name=' . addslashes ( $row ['contact_display_name'] ) . '\';"    data-toggle="tooltip" data-container="body" data-original-title="' . $AppUI->_ ( 'logout_session' ) . '"><span class="glyphicon glyphicon-log-out"></span></a>';
	}
	if ($canEdit && $canDelete && $logoutUserFlag) {
		$s .= '<a class="btn btn-xs btn-warning" role="button" onclick="javascript:window.location=\'./index.php?m=users&tab=3&out_user_id=' . $row ['u_user_id'] . '&out_name=' . addslashes ( $row ['contact_display_name'] ) . '\';"data-toggle="tooltip" data-placement="right" data-container="body" data-original-title="' . $AppUI->_ ( 'logout_user' ) . '"><span class="glyphicon glyphicon-ban-circle"></span></a>';
	}
	$s .= '</td>';
	
	$s .= '</tr>';
	echo $s;
}
echo $listTable->endTable ();
?>
