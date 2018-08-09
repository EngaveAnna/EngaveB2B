<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $priorities;
global $m, $a, $date, $other_users, $user_id, $task_type;
global $task_sort_item1, $task_sort_type1, $task_sort_order1;
global $task_sort_item2, $task_sort_type2, $task_sort_order2;

$showEditCheckbox = apmgetConfig ( 'direct_edit_assignment' );

// retrieve any state parameters
if (isset ( $_POST ['show_form'] )) {
	$AppUI->setState ( 'TaskDayShowArc', apmgetParam ( $_POST, 'show_arc_proj', 0 ) );
	$AppUI->setState ( 'TaskDayShowLow', apmgetParam ( $_POST, 'show_low_task', 0 ) );
	$AppUI->setState ( 'TaskDayShowHold', apmgetParam ( $_POST, 'show_hold_proj', 0 ) );
	$AppUI->setState ( 'TaskDayShowDyn', apmgetParam ( $_POST, 'show_dyn_task', 0 ) );
	$AppUI->setState ( 'TaskDayShowPin', apmgetParam ( $_POST, 'show_pinned', 0 ) );
	$AppUI->setState ( 'TaskDayShowEmptyDate', apmgetParam ( $_POST, 'show_empty_date', 0 ) );
	$AppUI->setState ( 'TaskDayShowInProgress', apmgetParam ( $_POST, 'show_inprogress', 0 ) );
}

// Required for today view.
$AppUI = is_object ( $AppUI ) ? $AppUI : new apm_Core_CAppUI ();
$showArcProjs = $AppUI->getState ( 'TaskDayShowArc', 0 );
$showLowTasks = $AppUI->getState ( 'TaskDayShowLow', 1 );
$showHoldProjs = $AppUI->getState ( 'TaskDayShowHold', 0 );
$showDynTasks = $AppUI->getState ( 'TaskDayShowDyn', 0 );
$showPinned = $AppUI->getState ( 'TaskDayShowPin', 0 );
$showEmptyDate = $AppUI->getState ( 'TaskDayShowEmptyDate', 0 );
$showInProgress = $AppUI->getState ( 'TaskDayShowInProgress', 0 );

/*
 * TODO: This is a nasty, dirty hack because globals have stacked on top of
 * globals and have made a mess of things.. we need a better option.
 */
if (! isset ( $tasks ) || ! count ( $tasks )) {
	global $tasks;
}
$perms = &$AppUI->acl ();
$canDelete = $perms->checkModuleItem ( $m, 'delete' );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'tasks', 'todo' );

if (0 == count ( $fields )) {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'task_percent_complete',
			'task_priority',
			'user_task_priority',
			'task_name',
			'task_project',
			'task_start_date',
			'task_duration',
			'task_end_date',
			'task_due_in' 
	);
	$fieldNames = array (
			'',
			'P',
			'U',
			'Task Name',
			'Project Name',
			'Start Date',
			'Duration',
			'Finish Date',
			'Due In' 
	);
	
	$module->storeSettings ( 'tasks', 'todo', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
$fieldNames = array_values ( $fields );

$listTable = new apm_Output_HTML_TaskTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $tasks );
echo $listTable->endTable ();