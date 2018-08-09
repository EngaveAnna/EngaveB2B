<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $AppUI, $m, $obj, $task_id, $apmconfig;
if (canView ( 'files' )) {
	$showProject = false;
	$project_id = $obj->task_project;
	include (apm_BASE_DIR . '/modules/files/index_table.php');
	if (canAdd ( 'files' )) {
		echo '<a class="btn btn-default" href="./index.php?m=files&a=addedit&project_id=' . $obj->task_project . '&file_task=' . $task_id . '">' . $AppUI->_ ( 'Attach a file' ) . '</a>';
	}
}