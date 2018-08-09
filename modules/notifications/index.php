<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'NotificationIdxTab', $_GET, 'tab', 0 );

if (isset ( $_REQUEST ['project_id'] )) {
	$AppUI->setState ( 'NotificationIdxProject', apmgetParam ( $_REQUEST, 'project_id', null ) );
}
$project_id = $AppUI->getState ( 'NotificationIdxProject' ) !== null ? $AppUI->getState ( 'NotificationIdxProject' ) : 0;

$notification = new CNotification ();

if (! $notification->canAccess ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $notification->canCreate ();

// get the list of visible companies
$extra = array (
		'from' => 'notifications',
		'where' => 'projects.project_id = notification_project' 
);

$project = new CProject ();
$projects = $project->getAllowedRecords ( $AppUI->user_id, 'projects.project_id,project_name', 'project_name', null, $extra, 'projects' );
$projects = arrayMerge ( array (
		'0' => $AppUI->_ ( 'All', UI_OUTPUT_JS ) 
), $projects );

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Notifications', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );
$titleBlock->addFilterCell ( 'Project', 'project_id', $projects, $project_id );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=notifications&a=addedit', 'new notification', '', true );
}
$titleBlock->show ();

$notificationTypes = apmgetSysVal ( 'NotificationType' );

$tabBox = new CTabBox ( '?m=notifications', apm_BASE_DIR . '/modules/notifications/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $notificationTypes, $AppUI->_ ( 'All Notifications', UI_OUTPUT_RAW ) );
}
foreach ( $notificationTypes as $notification_type ) {
	$tabBox->add ( 'index_table', $notification_type );
}
$showProject = true;
$tabBox->show ();