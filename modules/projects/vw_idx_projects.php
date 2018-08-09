<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $project, $project_statuses, $tab, $company_id, $owner, $project_type, $orderby, $orderdir, $m;

$currentTabId = $tab;
$is_tabbed = false;
$project_status_filter = $currentTabId - 1;

switch ($tab) {
	case 0 :
		// do nothing
		$filter = '1 = 1';
		break;
	case 1 :
		$filter = 'project_active = 1';
		break;
	case count ( $project_statuses ) - 1 :
		$filter = 'project_active = 0';
		break;
	default :
		$filter = 'project_active = 1 AND project_status = ' . ($tab - 2);
}

$filter .= (($company_id > 0) ? ' AND project_company = ' . $company_id : '');
$filter .= (($owner > 0) ? ' AND project_owner = ' . $owner : '');
$filter .= (($project_type > - 1) ? ' AND project_type = ' . $project_type : '');
$orderby = property_exists ( 'CProject', $orderby ) ? $orderby : 'project_name';
$orderby = ($orderby == 'project_company') ? 'company_name' : $orderby;

$projects = $project->loadAll ( $orderby . ' ' . $orderdir, $filter );
$projects = array_values ( $projects );

$fieldList = array ();
$fieldNames = array ();

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'projects', 'index_list' );

if (0 == count ( $fields )) {
	// TODO: This is only in place to provide an pre-upgrade-safe
	// state for versions earlier than v2.3
	// At some point at/after v4.0, this should be deprecated
	$fieldList = array (
			'project_color_identifier',
			'project_priority',
			'project_name',
			'project_company',
			'project_start_date',
			'project_end_date',
			'project_actual_end_date',
			'project_owner',
			'project_task_count' 
	);
	$fieldNames = array (
			'%',
			'P',
			'Project Name',
			'Company',
			'Start',
			'End',
			'Actual',
			'Owner',
			'Tasks' 
	);
	
	$module->storeSettings ( 'projects', 'index_list', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
$fieldList = array_keys ( $fields );
$fieldNames = array_values ( $fields );

$page = ( int ) apmgetParam ( $_GET, 'page', 1 );
$paginator = new apm_Utilities_Paginator ( $projects );
$items = $paginator->getItemsOnPage ( $page );

// FIXME: APMX mod to using list template
include $AppUI->getTheme ()->resolveTemplate ( 'projects/list' );

?>
