<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project, $project_id, $canEdit, $m, $tab;

$company_id = $project->company ();

$task_log_costcodes = array (
		0 => '(all)' 
) + CProject::getBillingCodes ( $company_id, true );

$users = apmgetUsers ();

$cost_code = apmgetParam ( $_GET, 'cost_code', 0 );

if (isset ( $_GET ['user_id'] )) {
	$AppUI->setState ( 'ProjectsTaskLogsUserFilter', apmgetParam ( $_GET, 'user_id', 0 ) );
}
$user_id = $AppUI->getState ( 'ProjectsTaskLogsUserFilter' ) ? $AppUI->getState ( 'ProjectsTaskLogsUserFilter' ) : 0;

if (isset ( $_GET ['hide_inactive'] )) {
	$AppUI->setState ( 'ProjectsTaskLogsHideArchived', true );
} else {
	$AppUI->setState ( 'ProjectsTaskLogsHideArchived', false );
}
$hide_inactive = $AppUI->getState ( 'ProjectsTaskLogsHideArchived' );

if (isset ( $_GET ['hide_complete'] )) {
	$AppUI->setState ( 'ProjectsTaskLogsHideComplete', true );
} else {
	$AppUI->setState ( 'ProjectsTaskLogsHideComplete', false );
}
$hide_complete = $AppUI->getState ( 'ProjectsTaskLogsHideComplete' );

?>
<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
	?>
function delIt2(id) {
	if (confirm( '<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS) . ' ' . $AppUI->_('Task Log', UI_OUTPUT_JS) . '?'; ?>' )) {
		document.frmDelete2.task_log_id.value = id;
		document.frmDelete2.submit();
	}
}
<?php } ?>
</script>

<form name="frmDelete2" action="./index.php?m=tasks" method="post"
	accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_updatetask" /> <input
		type="hidden" name="del" value="1" /> <input type="hidden"
		name="task_log_id" value="0" />
</form>
<?php
$fieldList = array ();
$fieldNames = array ();

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'tasks', 'task_logs_projects_view' );

if (0 == count ( $fields )) {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'task_log_date',
			'task_log_name',
			'task_log_creator',
			'task_log_hours',
			'task_log_costcode',
			'task_log_description' 
	);
	$fieldNames = array (
			'Date',
			'Task name',
			'User',
			'Hours',
			'Cost Code',
			'Comments',
			'' 
	);
	
	$module->storeSettings ( 'tasks', 'task_logs_projects_view', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
$fieldList = array_keys ( $fields );
$fieldNames = array_values ( $fields );

$i = 0;
$hours_index = 0;
?>

<?php
// Winnow out the tasks we are not allowed to view.
$perms = &$AppUI->acl ();
$canDelete = canDelete ( 'task_log' );

// Pull the task comments
$project = new CProject ();
// TODO: this method should be moved to CTaskLog
$logs = $project->getTaskLogs ( null, $project_id, $user_id, $hide_inactive, $hide_complete, $cost_code );

$s = '';
$hrs = 0;
$canEdit = canEdit ( 'task_log' );

$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );

$billingCategory = apmgetSysVal ( 'BudgetCategory' );
$durnTypes = apmgetSysVal ( 'TaskDurationType' );
$status = apmgetSysVal ( 'TaskStatus' );
$task_types = apmgetSysVal ( 'TaskType' );

$customLookups = array (
		'budget_category' => $billingCategory,
		'task_duration_type' => $durnTypes,
		'task_status' => $status,
		'task_type' => $task_types 
);

$task = new CTask ();
$listTable = new apm_Output_HTML_TaskTable ( $AppUI, $task );
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $module );

if (count ( $logs )) {
	foreach ( $logs as $key => $row ) {
		$opt = null;
		if ($canEdit) {
			$opt .= '<a class="btn btn-xs btn-info" role="button" href="';
			$opt .= '?m=tasks&a=view&task_id=' . $task_id . '&tab=';
			$opt .= ($tab == - 1) ? $AppUI->getState ( 'TaskLogVwTab' ) : '1';
			$opt .= '&task_log_id=' . $row ['task_log_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
		}
		
		if ($canDelete) {
			$opt .= '<a class="btn btn-xs btn-danger" role="button" href="javascript:delIt2(' . $row ['task_log_id'] . ');"><span class="glyphicon glyphicon-remove"></span></a>';
		}
		
		$row ['task_log_options'] = $opt;
		$htmlHelper->stageRowData ( $row );
		
		$hrs += ( float ) $row ['task_log_hours'];
		$logs [$key] = $row;
	}
	echo $listTable->buildRows ( $logs, $customLookups );
}
echo $listTable->endTable();