<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$perms = &$AppUI->acl ();
$user_id = ( int ) apmgetParam ( $_POST, 'user_id', $AppUI->user_id );

if (isset ( $_POST ['f'] )) {
	$AppUI->setState ( 'TaskIdxFilter', $_POST ['f'] );
}
$f = $AppUI->getState ( 'TaskIdxFilter' ) ? $AppUI->getState ( 'TaskIdxFilter' ) : apmgetConfig ( 'task_filter_default', 'myunfinished' );

if (isset ( $_POST ['f2'] )) {
	$AppUI->setState ( 'CompanyIdxFilter', $_POST ['f2'] );
}

$f2 = ($AppUI->getState ( 'CompanyIdxFilter' )) ? $AppUI->getState ( 'CompanyIdxFilter' ) : ((apmgetConfig ( 'company_filter_default', 'user' ) == 'user') ? $AppUI->user_company : 'allcompanies');

if (isset ( $_GET ['project_id'] )) {
	$AppUI->setState ( 'TaskIdxProject', apmgetParam ( $_GET, 'project_id', null ) );
}
$project_id = $AppUI->getState ( 'TaskIdxProject' ) ? $AppUI->getState ( 'TaskIdxProject' ) : 0;
if (isset ( $_POST ['show_task_options'] )) {
	$AppUI->setState ( 'TaskListShowIncomplete', apmgetParam ( $_POST, 'show_incomplete', 0 ) );
}
$showIncomplete = $AppUI->getState ( 'TaskListShowIncomplete', 0 );

// get CCompany() to filter tasks by company
$obj = new CCompany ();
$companies = $obj->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$filters2 = arrayMerge ( array (
		'allcompanies' => $AppUI->_ ( 'All Companies', UI_OUTPUT_RAW ) 
), $companies );
$filters = array (
		'my' => 'My Tasks',
		'myunfinished' => 'My Unfinished Tasks',
		'allunfinished' => 'All Unfinished Tasks',
		'myproj' => 'My Projects',
		'mycomp' => 'All Tasks for my Company',
		'unassigned' => 'All Tasks (unassigned)',
		'taskowned' => 'All Tasks That I Am Owner',
		'taskcreated' => 'All Tasks I Have Created',
		'all' => 'All Tasks',
		'allfinished7days' => 'All Tasks Finished Last 7 Days',
		'myfinished7days' => 'My Tasks Finished Last 7 Days' 
);

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Tasks', 'icon.png', $m );

// Let's see if this user has admin privileges
if (canView ( 'users' )) {
	$user_list = array (
			0 => '' 
	);
	$user_list += $perms->getPermittedUsers ( 'tasks' );
	$titleBlock->addFilterCell ( 'User', 'user_id', $user_list, $user_id );
}

$titleBlock->addFilterCell ( 'Company', 'f2', $filters2, $f2 );

if (apmgetParam ( $_GET, 'inactive', '' ) == 'toggle') {
	$AppUI->setState ( 'inactive', $AppUI->getState ( 'inactive' ) == - 1 ? 0 : - 1 );
}
$in = $AppUI->getState ( 'inactive' ) == - 1 ? '' : 'in';

$titleBlock->showhelp = false;
$titleBlock->addCell ( '<label>' . $AppUI->_ ( 'Task Filter' ) . '</label>' );
$titleBlock->addCell ( '<form action="?m=tasks" method="post" name="taskFilter" accept-charset="utf-8">' . arraySelect ( $filters, 'f', 'size="1" class="form-control" onChange="document.taskFilter.submit();"', $f, true ) . '</form>' );

$titleBlock->addCrumb ( '?m=tasks&amp;a=todo&amp;user_id=' . $user_id, 'my todo' );
if (apmgetParam ( $_GET, 'pinned' ) == 1) {
	// APMoff $titleBlock->addCrumb('?m=tasks', 'all tasks');
} else {
	// APMoff $titleBlock->addCrumb('?m=tasks&amp;pinned=1', 'my pinned tasks');
}
// APMoff $titleBlock->addCrumb ( '?m=tasks&amp;inactive=toggle', 'show ' . $in . 'active tasks' );
// APMoff $titleBlock->addCrumb('?m=tasks&amp;a=tasksperuser', 'tasks per user');
if (! $project_id) {
	// $titleBlock->addCell('<form name="task_list_options" method="post" action="?m=tasks" accept-charset="utf-8"> <label for="show_incomplete" ><input type="hidden" name="show_task_options" value="1" /><input type="checkbox" name="show_incomplete" id="show_incomplete" onclick="document.task_list_options.submit();"' . ($showIncomplete ? 'checked="checked"' : '') . '/>' . $AppUI->_("Incomplete Tasks Only") . '</label> </form>');
}

$titleBlock->show ();

// include the re-usable sub view
$min_view = false;

include (apm_BASE_DIR . '/modules/tasks/vw_tasks.php');