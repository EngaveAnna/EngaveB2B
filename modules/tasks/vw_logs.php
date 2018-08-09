<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $task_id, $sf, $df, $canEdit, $m;

$perms = &$AppUI->acl ();
if (! canView ( 'task_log' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$problem = ( int ) apmgetParam ( $_GET, 'problem', null );

?>
<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
$canDelete = canDelete ( 'task_log' );
if ($canDelete) {
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
$fields = $module->loadSettings ( 'tasks', 'task_logs_tasks_view' );

if (0 == count ( $fields )) {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'task_log_date',
			'task_log_reference',
			'task_log_name',
			'task_log_related_url',
			'task_log_creator',
			'task_log_hours',
			'task_log_costcode',
			'task_log_description' 
	);
	$fieldNames = array (
			'Date',
			'Ref',
			'Summary',
			'URL',
			'User',
			'Hours',
			'Cost Code',
			'Comments' 
	);
	
	$module->storeSettings ( 'tasks', 'task_logs_tasks_view', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
$fieldList = array_keys ( $fields );
$fieldNames = array_values ( $fields );
$task = new CTask ();
$listTable = new apm_Output_HTML_TaskTable ( $AppUI, $task );

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $m );

// Pull the task comments
// TODO: this method should be moved to CTaskLog
$logs = $task->getTaskLogs ( $task_id, $problem );

$s = '';
$hrs = 0;
$canEdit = canEdit ( 'task_log' );

$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );

$billingCategory = apmgetSysVal ( 'BudgetCategory' );
$durnTypes = apmgetSysVal ( 'TaskDurationType' );
$taskLogReference = apmgetSysVal ( 'TaskLogReference' );
$status = apmgetSysVal ( 'TaskStatus' );
$task_types = apmgetSysVal ( 'TaskType' );

$customLookups = array (
		'budget_category' => $billingCategory,
		'task_duration_type' => $durnTypes,
		'task_log_reference' => $taskLogReference,
		'task_status' => $status,
		'task_type' => $task_types 
);

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
echo $listTable->endTable ();

if ($perms->checkModuleItem ( 'tasks', 'edit', $task_id )) {
	$s .= '<div class="row"><form action="?m=tasks&a=view&tab=1&task_id=' . $task_id . '" method="post" accept-charset="utf-8">';
	$s .= '<input type="submit" class="btn btn-default" value="' . $AppUI->_ ( 'New log' ) . '"></form></div>';
	echo $s;
}
?>
