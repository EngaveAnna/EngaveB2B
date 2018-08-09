<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project_id, $task_id, $showProject, $tab, $search_string, $m;

$type_filter = ($m == 'notifications') ? $tab - 1 : - 1;

if ($task_id && ! $project_id) {
	$task = new CTask ();
	$task->load ( $task_id );
	$project_id = $task->task_project;
}

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );

if (! isset ( $project_id )) {
	$project_id = ( int ) apmgetParam ( $_POST, 'project_id', 0 );
}

$notification = new CNotification ();
$items = $notification->getProjectTaskNotificationsByCategory ( null, $project_id, $task_id, $type_filter, $search_string );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'notifications', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
		'notification_category',
		'notification_priority',
		'notification_name',
		'notification_project',
		'notification_task',
		'notification_owner',
		'notification_date',
		'notification_status',
		'notification_description',
		'notification_options'
	);
	$fieldNames = array (
		'Category',
		'Priority',
		'Notification Title',
		'Notification Project',
		'Notification Task',
		'Owner',
		'Date',
		'Notification Status',
		'Description',
		'Options'
	);
	
	$module->storeSettings ('notifications', 'index_list', $fieldList, $fieldNames);
	$fields = array_combine ($fieldList, $fieldNames);
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$notification_types = apmgetSysVal ( 'NotificationType' );
$customLookups = array (
		'notification_category' => $notification_types 
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {
	$item ['notification_options'] = '';
	if (canEdit ( 'notifications' )) {
		$item ['notification_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=notifications&a=addedit&notification_id=' . $item ['notification_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	if (canDelete ( 'notifications' )) {
		$item ['notification_options'] .= '<form name="frm_remove_notification_' . $item ['notification_id'] . '" action="?m=notifications" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_notification_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="notification_id" value="' . $item ['notification_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['notification_options'] .= '<a class="btn btn-xs btn-danger" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this notification?' ) . '\')) {document.frm_remove_notification_' . $item ['notification_id'] . '.submit()}"><span class="glyphicon glyphicon-remove"></span></a>';
		// $s .= $hidden_table;
	}
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;