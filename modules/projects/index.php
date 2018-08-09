<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'ProjIdxTab', $_GET, 'tab', 5 );

$project = new CProject ();
$structprojs = $project->getProjects ();
$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
// $search_string = apmformSafe($search_string, true);

$canCreate = $project->canCreate ();
$company_id = (apmgetConfig ( 'company_filter_default', 'user' ) == 'user') ? $AppUI->user_company : '-1';
$orderby = (isset ( $_GET ['orderby'] ) && property_exists ( 'CProject', $_GET ['orderby'] )) ? $_GET ['orderby'] : 'project_name';
$project_type = $AppUI->processIntState ( 'ProjIdxType', $_POST, 'project_type', - 1 );
$owner = $AppUI->processIntState ( 'ProjIdxowner', $_POST, 'project_owner', - 1 );

$orderdir = $AppUI->getState ( 'ProjIdxOrderDir' ) ? $AppUI->getState ( 'ProjIdxOrderDir' ) : 'asc';
if (isset ( $_GET ['orderby'] )) {
	if ($AppUI->getState ( 'ProjIdxOrderDir' ) == 'asc') {
		$orderdir = 'desc';
	} else {
		$orderdir = 'asc';
	}
}
$AppUI->setState ( 'ProjIdxOrderDir', $orderdir );
// collect the full projects list data via function in projects.class.php
$search_text = $search_string; // @note this is only because the projects_list_data function takes a bunch of globals
                               // $projects = projects_list_data();

$oCompany = new CCompany ();
$allowedCompanies [- 1] = $AppUI->_ ( 'All Companies' );
$allowedCompanies += $oCompany->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );

$project_types = array (
		- 1 => $AppUI->_ ( 'All Projects' ) 
) + apmgetSysVal ( 'ProjectType' );
$user_list = array (
		0 => $AppUI->_ ( 'All Users' ) 
) + CProject::getOwners ();
// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Projects', 'icon.png', $m );
// $titleBlock->addSearchCell($search_string);
$titleBlock->addFilterCell ( 'Type', 'project_type', $project_types, $project_type );
$titleBlock->addFilterCell ( 'Company', 'project_company', $allowedCompanies, $company_id );
$titleBlock->addFilterCell ( 'Owner', 'project_owner', $user_list, $owner );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=projects&a=addedit', 'new project' );
}

// APM - can use glyphicon, just define 3th variable ex. array('glyphicon','print')
// APMoff $titleBlock->addCrumb(array('custom','href="javascript: void(0);" onclick ="window.open(\'index.php?m=projects&a=printprojects&dialog=1&suppressHeaders=1&company_id='.$company_id.'&project_type='.$project_type.'&project_owner='.$owner.'\', \'printprojects\',\'width=1200, height=600, menubar=1, scrollbars=1\')"'),'print', '', true);
$titleBlock->show ();
$project_statuses = array ();
//APMoff $project_statuses = apmgetSysVal ( 'ProjectStatus' );
$project_statuses [- 2] = $AppUI->_ ( 'All Projects');
$project_statuses [- 1] = $AppUI->_ ( 'All Active');
$project_statuses [0] = $AppUI->_ ('Not Defined');
$project_statuses [1] = $AppUI->_ ('Proposed');
$project_statuses [] = $AppUI->_ ('Archived');

//ksort ( $project_statuses );

$counts = $project->getProjectsByStatus ( $company_id );
$counts [- 2] = count ( $project->loadAll ( null, ($company_id > 0) ? 'project_company = ' . $company_id : '' ) );
$counts [- 1] = count ( $project->loadAll ( null, 'project_active = 1' . (($company_id > 0) ? ' AND project_company = ' . $company_id : '') ) );
//$counts [count ( $project_statuses ) - 3] = $counts [- 2] - $counts [- 1];

$tabBox = new CTabBox ( '?m=projects', apm_BASE_DIR . '/modules/projects/', $tab );
foreach ( $project_statuses as $key => $project_status ) {
	$tabname = $project_status . '(' . ( int ) $counts [$key] . ')';
	$tabBox->add ( 'vw_idx_projects', mb_trim ( $tabname ), true );
}
$min_view = true;
$tabBox->add ( 'viewgantt', 'Gantt' );
$tabBox->show ();