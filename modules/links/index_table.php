<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project_id, $task_id, $showProject, $tab, $search_string, $m;

$type_filter = ($m == 'links') ? $tab - 1 : - 1;

if ($task_id && ! $project_id) {
	$task = new CTask ();
	$task->load ( $task_id );
	$project_id = $task->task_project;
}

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );

if (! isset ( $project_id )) {
	$project_id = ( int ) apmgetParam ( $_POST, 'project_id', 0 );
}

$link = new CLink ();
$items = $link->getProjectTaskLinksByCategory ( null, $project_id, $task_id, $type_filter, $search_string );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'links', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'link_name',
			'link_description',
			'link_category',
			'link_project',
			'link_task',
			'link_owner',
			'link_date' 
	);
	$fieldNames = array (
			'Link Name',
			'Description',
			'Category',
			'Project Task',
			'Task Name',
			'Owner',
			'Date' 
	);
	
	$module->storeSettings ( 'links', 'index_list', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        // counts total recs from selection
$xpg_totalrecs = count ( $items );
$items = array_slice ( $items, $xpg_min, $xpg_pagesize );

$link_types = apmgetSysVal ( 'LinkType' );
$customLookups = array (
		'link_category' => $link_types 
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

foreach ( $items as $key => $item ) {
	$item ['link_options'] = '';
	if (canEdit ( 'links' )) {
		$item ['link_options'] .= '<a class="btn btn-xs btn-info" role="button" href="./index.php?m=links&a=addedit&link_id=' . $item ['link_id'] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	if (canDelete ( 'links' )) {
		$item ['link_options'] .= '<form name="frm_remove_link_' . $item ['link_id'] . '" action="?m=links" method="post" accept-charset="utf-8">
							<input type="hidden" name="dosql" value="do_link_aed" />
							<input type="hidden" name="del" value="1" />
							<input type="hidden" name="link_id" value="' . $item ['link_id'] . '" />
							<input type="hidden" name="redirect" value="' . $current_uri . '" />
							</form>';
		$item ['link_options'] .= '<a class="btn btn-xs btn-danger" role="button" href="javascript: void(0);" onclick="if (confirm(\'' . $AppUI->_ ( 'Are you sure you want to delete this link?' ) . '\')) {document.frm_remove_link_' . $item ['link_id'] . '.submit()}"><span class="glyphicon glyphicon-remove"></span></a>';
		// $s .= $hidden_table;
	}
	$items [$key] = $item;
}

echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();
echo $pageNav;