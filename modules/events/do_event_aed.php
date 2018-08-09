<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$controller = new apm_Controllers_Base ( new CEvent (), $delete, 'Event', 'm=events', 'm=events&a=addedit' );

$start_date = new apm_Utilities_Date ( $_POST ['event_start_date'] . $_POST ['start_time'] );
$_POST ['event_start_date'] = $start_date->format ( FMT_DATETIME_MYSQL );

$end_date = new apm_Utilities_Date ( $_POST ['event_end_date'] . $_POST ['end_time'] );
$_POST ['event_end_date'] = $end_date->format ( FMT_DATETIME_MYSQL );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );