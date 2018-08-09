<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

// Grab a list of the other resources, determine how they are allocated,
// and if there is a clash between this and other tasks.
global $AppUI, $task_id, $obj;

$resource = new CResource ();

$resources = $resource->getResourcesByTask ( $task_id );

// Determine any other clashes.
$resource_tasks = array ();

if (count ( $resources )) {
	$resource_tasks = $resource->getTasksByResources ( $resources, $obj->task_start_date, $obj->task_end_date );
}

$fieldList = array ();
$fieldNames = array ();

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'resources', 'task_view' );
if (count ( $fields ) > 0) {
	$fieldList = array_keys ( $fields );
	$fieldNames = array_values ( $fields );
} else {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v3.0
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'resource_type',
			'resource_name',
			'percent_allocated' 
	);
	$fieldNames = array (
			'Resource Type',
			'Resource',
			'Allocation' 
	);
	
	$module->storeSettings ( 'resources', 'task_view', $fieldList, $fieldNames );
}

$listTable = new apm_Output_HTML_TaskTable ( $AppUI, $tempTask );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );
?>

<?php
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $m );

$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
$typelist = apmgetSysVal ( 'ResourceTypes' );
$customLookups = array (
		'resource_type' => $typelist 
);
echo $listTable->buildRows ( $taskTree, $customLookups );

foreach ( $resources as $row ) {
	$overallocated = (isset ( $resource_tasks [$row ['resource_id']] ) && $resource_tasks [$row ['resource_id']] > $row ['resource_max_allocation']);
	
	echo '<tr>';
	$htmlHelper->stageRowData ( $row );
	foreach ( $fieldList as $index => $column ) {
		echo $htmlHelper->createCell ( $fieldList [$index], $row [$fieldList [$index]], $customLookups );
	}
	echo '<td class="warning">' . ($overallocated ? $AppUI->_ ( 'OVERALLOCATED' ) : '') . '</td>';
	echo '</tr>';
}

echo $listTable->endTable ();
?>