<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
// @todo remove database query

global $AppUI, $m, $a, $project_id, $task_id, $f, $task_status, $min_view, $query_string, $durnTypes, $tpl;
global $task_sort_item1, $task_sort_type1, $task_sort_order1;
global $task_sort_item2, $task_sort_type2, $task_sort_order2;
global $user_id, $apmconfig, $currentTabId, $currentTabName, $canEdit, $showEditCheckbox, $tab;
global $history_active;

if (empty ( $query_string )) {
	$query_string = '?m=' . $m . '&amp;a=' . $a;
}
$mods = $AppUI->getActiveModules ();
$history_active = ! empty ( $mods ['history'] ) && canView ( 'history' );

/**
 * **
 * // Let's figure out which tasks are selected
 */
$task_id = ( int ) apmgetParam ( $_GET, 'task_id', 0 );

$pinned_only = ( int ) apmgetParam ( $_GET, 'pinned', 0 );
__extract_from_tasks_pinning ( $AppUI, $task_id );

$durnTypes = apmgetSysVal ( 'TaskDurationType' );
$taskPriority = apmgetSysVal ( 'TaskPriority' );

$task_project = $project_id;

$task_sort_item1 = apmgetParam ( $_GET, 'task_sort_item1', 'task_start_date' );
$task_sort_type1 = apmgetParam ( $_GET, 'task_sort_type1', '' );
$task_sort_item2 = apmgetParam ( $_GET, 'task_sort_item2', 'task_end_date' );
$task_sort_type2 = apmgetParam ( $_GET, 'task_sort_type2', '' );
$task_sort_order1 = ( int ) apmgetParam ( $_GET, 'task_sort_order1', 0 );
$task_sort_order2 = ( int ) apmgetParam ( $_GET, 'task_sort_order2', 0 );
if (isset ( $_POST ['show_task_options'] )) {
	$AppUI->setState ( 'TaskListShowIncomplete', apmgetParam ( $_POST, 'show_incomplete', 0 ) );
}
$showIncomplete = $AppUI->getState ( 'TaskListShowIncomplete', 0 );

$project = new CProject ();
$allowedProjects = $project->getAllowedSQL ( $AppUI->user_id, 'p.project_id' );

$where_list = (count ( $allowedProjects )) ? implode ( ' AND ', $allowedProjects ) : '';

$working_hours = ($apmconfig ['daily_working_hours'] ? $apmconfig ['daily_working_hours'] : 8);

$projects = __extract_from_tasks4 ( $where_list, $project_id, $task_id );
$subquery = __extract_from_tasks1 ();
$task_status = __extract_from_tasks ( $min_view, $currentTabId, $project_id, $currentTabName, $AppUI );

$q = new apm_Database_Query ();
$q = __extract_from_tasks5 ( $q, $subquery );

$q->addJoin ( 'projects', 'p', 'p.project_id = task_project', 'inner' );
$q->leftJoin ( 'users', 'usernames', 'task_owner = usernames.user_id' );
$q->leftJoin ( 'user_tasks', 'ut', 'ut.task_id = tasks.task_id' );
$q->leftJoin ( 'users', 'assignees', 'assignees.user_id = ut.user_id' );
$q->leftJoin ( 'contacts', 'co', 'co.contact_id = usernames.user_contact' );
$q->leftJoin ( 'task_log', 'tlog', 'tlog.task_log_task = tasks.task_id AND tlog.task_log_problem > 0' );
$q->leftJoin ( 'files', 'f', 'tasks.task_id = f.file_task' );
$q->leftJoin ( 'project_departments', 'project_departments', 'p.project_id = project_departments.project_id OR project_departments.project_id IS NULL' );
$q->leftJoin ( 'departments', 'departments', 'departments.dept_id = project_departments.department_id OR dept_id IS NULL' );
$q->leftJoin ( 'user_task_pin', 'pin', 'tasks.task_id = pin.task_id AND pin.user_id = ' . ( int ) $AppUI->user_id );

$f2 = isset ( $f2 ) ? $f2 : 0;
if (( int ) $f2) {
	$q->addWhere ( 'project_company = ' . ( int ) $f2 );
}
if ($project_id) {
	$q->addWhere ( 'task_project = ' . ( int ) $project_id );
	// if we are on a project context make sure we show all tasks
	$f = 'all';
} else {
	$q->addWhere ( 'project_active = 1' );
	if (($template_status = apmgetConfig ( 'template_projects_status_id' )) != '') {
		$q->addWhere ( 'project_status <> ' . $template_status );
	}
}

if ($pinned_only) {
	$q->addWhere ( 'task_pinned = 1' );
}

$q = __extract_from_tasks3 ( $f, $q, $user_id, $task_id, $AppUI );

if ($showIncomplete) {
	$q->addWhere ( 'task_percent_complete <> 100' );
}

// When in task view context show all the tasks, active and inactive. (by not limiting the query by task status)
// When in a project view or in the tasks list, show the active or the inactive tasks depending on the selected tab or button.
if (! $task_id) {
	if ($tab == 1) {
		$task_status = - 1;
	} else {
		$task_status = 0;
	}
	$q->addWhere ( 'task_status = ' . ( int ) $task_status );
}
if (isset ( $task_type ) && ( int ) $task_type > 0) {
	$q->addWhere ( 'task_type = ' . ( int ) $task_type );
}
if (isset ( $task_owner ) && ( int ) $task_owner > 0) {
	$q->addWhere ( 'task_owner = ' . ( int ) $task_owner );
}

if (($project_id || ! $task_id) && ! $min_view) {
	if ($search_text = $AppUI->getState ( 'tasks_search_string' )) {
		$q->addWhere ( '( task_name LIKE (\'%' . $search_text . '%\') OR task_description LIKE (\'%' . $search_text . '%\') )' );
	}
}

// filter tasks considering task and project permissions
$projects_filter = '';
$tasks_filter = '';

// TODO: Enable tasks filtering
$allowedProjects = $project->getAllowedSQL ( $AppUI->user_id, 'task_project' );
if (count ( $allowedProjects )) {
	$q->addWhere ( $allowedProjects );
}

$obj = new CTask ();
$allowedTasks = $obj->getAllowedSQL ( $AppUI->user_id, 'tasks.task_id' );
if (count ( $allowedTasks )) {
	$q->addWhere ( $allowedTasks );
}

$q->addGroup ( 'tasks.task_id' );
if (! $project_id && ! $task_id) {
	$q->addOrder ( 'p.project_id, task_start_date, task_end_date' );
} else {
	$q->addOrder ( 'task_start_date, task_end_date, task_name' );
}

$tasks = $q->loadList ();
// POST PROCESSING TASKS
if (count ( $tasks ) > 0) {
	foreach ( $tasks as $row ) {
		// add information about assigned users into the page output
		$assigned_users = __extract_from_tasks2 ( $row );
		$row ['task_assigned_users'] = $assigned_users;
		// pull the final task row into array
		$projects [$row ['task_project']] ['tasks'] [] = $row;
	}
}

$showEditCheckbox = ((isset ( $canEdit ) && $canEdit && apmgetConfig ( 'direct_edit_assignment' )) ? true : false);
$durnTypes = apmgetSysVal ( 'TaskDurationType' );
$tempTask = new CTask ();
$userAlloc = $tempTask->getAllocation ( 'user_id' );
global $expanded;
$expanded = $AppUI->getPref ( 'TASKSEXPANDED' );
$open_link = apmtoolTip ( $m, 'click to expand/collapse all the tasks for this project.' ) . '<a href="javascript: void(0);"><img onclick="expand_collapse(\'task_proj_' . $project_id . '_\', \'tblProjects\',\'collapse\',0,2);" id="task_proj_' . $project_id . '__collapse" src="' . apmfindImage ( 'up22.png', $m ) . '" class="center" ' . (! $expanded ? 'style="display:none"' : '') . ' /><img onclick="expand_collapse(\'task_proj_' . $project_id . '_\', \'tblProjects\',\'expand\',0,2);" id="task_proj_' . $project_id . '__expand" src="' . apmfindImage ( 'down22.png', $m ) . '" class="center" ' . ($expanded ? 'style="display:none"' : '') . ' /></a>' . apmendTip ();
$module = new apm_System_Module ();
$fields = $module->loadSettings ( $m, 'tasklist' );

if (0 == count ( $fields )) {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'task_percent_complete',
			'task_priority',
			'user_task_priority',
			'task_name',
			'task_owner',
			'task_assignees',
			'task_start_date',
			'task_duration',
			'task_end_date' 
	);
	$fieldNames = array (
			'Percent',
			'P',
			'U',
			'Task Name',
			'Owner',
			'Assignees',
			'Start Date',
			'Duration',
			'Finish Date' 
	);
	$module->storeSettings ( $m, 'tasklist', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
$fieldList = array_keys ( $fields );
$fieldNames = array_values ( $fields );
$listTable = new apm_Output_HTML_TaskTable ( $AppUI, $tempTask );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $m );

$status = apmgetSysVal ( 'TaskStatus' );
$priority = apmgetSysVal ( 'TaskPriority' );
$customLookups = array (
		'task_status' => $status,
		'task_priority' => $priority 
);

if ($task_id) {
	$task = new CTask ();
	$task->load ( $task_id );
	$taskTree = $tempTask->getTaskTree ( $task->task_project, $task_id );
	// $taskTree[$k]['task_options']="test - task id";
	echo $listTable->buildRows ( $taskTree, $customLookups );
} else {
	reset ( $projects );
	foreach ( $projects as $k => $p ) {
		$tnums = (isset ( $p ['tasks'] )) ? count ( $p ['tasks'] ) : 0;
		if ($tnums && $m == 'tasks') {
			$width = ($p ['project_percent_complete'] < 30) ? 30 : $p ['project_percent_complete'];
			?>
<tr>
	<td colspan="<?php echo count($fieldList) + 3; ?>">
		<div style="width: 100%;">
			<div class="row">
				<div class="col-sm-1" style="float: left; padding: 0px;">
					<a
						href="./index.php?m=projects&amp;a=view&amp;project_id=<?php echo $k; ?>"
						class="task-icon-lg btn"><span
						class=" glyphicon glyphicon-folder-open"> </span></a>
				</div>
				<!-- /col-sm-1 -->
				<div class="col-sm-11">
					<div class="panel panel-default">
						<div class="panel-heading arrow ar-left">
							<span class="text-muted"><?php echo $AppUI->__('Project');?>: </span>
							<strong><?php echo '<a href="./index.php?m=projects&amp;a=view&amp;project_id='.$k.'">' . $p['project_name'].'</a>'; ?></strong>
						</div>
						<div class="panel-body">
							<div class="progress">
								<div class="progress-bar progress-bar-striped active" style="width:<?php echo (int) $p['project_percent_complete']; ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="30" role="progressbar">
						<?php echo $AppUI->getPref('progress'); echo (int) $p['project_percent_complete']; ?>%
						</div>
							</div>
						</div>
						<!-- /panel-body -->
					</div>
					<!-- /panel panel-default -->
				</div>
				<!-- /col-sm-5 -->
			</div>
			<!-- /row -->
		</div>
	</td>
</tr>
<?php
			$taskTree = $tempTask->getTaskTree ( $k );
			
			foreach ( $taskTree as $key => $task ) {
				$taskTree [$key] ['task_options'] = '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=tasks&a=addedit&task_id=' . $task ['task_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
				$taskTree [$key] ['task_options'] .= '<a class="btn btn-xs btn-default" role="button" href="./index.php?m=tasks&a=view&tab=1&task_id=' . $task ['task_id'] . '"><span class="glyphicon glyphicon-tasks"></a>';
			}
			echo $listTable->buildRows ( $taskTree, $customLookups );
		}
		if ('projects' == $m || 'projectdesigner' == $m) {
			$taskTree = $tempTask->getTaskTree ( $k );
			echo $listTable->buildRows ( $taskTree, $customLookups );
		}
	}
}
echo $listTable->endTable ();

//include $AppUI->getTheme()->resolveTemplate('task_key');