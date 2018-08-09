<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'LinkIdxTab', $_GET, 'tab', 0 );

if (isset ( $_REQUEST ['project_id'] )) {
	$AppUI->setState ( 'LinkIdxProject', apmgetParam ( $_REQUEST, 'project_id', null ) );
}
$project_id = $AppUI->getState ( 'LinkIdxProject' ) !== null ? $AppUI->getState ( 'LinkIdxProject' ) : 0;

$link = new CLink ();

if (! $link->canAccess ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $link->canCreate ();

// get the list of visible companies
$extra = array (
		'from' => 'links',
		'where' => 'projects.project_id = link_project' 
);

$project = new CProject ();
$projects = $project->getAllowedRecords ( $AppUI->user_id, 'projects.project_id,project_name', 'project_name', null, $extra, 'projects' );
$projects = arrayMerge ( array (
		'0' => $AppUI->_ ( 'All', UI_OUTPUT_JS ) 
), $projects );

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Links', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );
$titleBlock->addFilterCell ( 'Project', 'project_id', $projects, $project_id );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=links&a=addedit', 'new link', '', true );
}
$titleBlock->show ();

$linkTypes = apmgetSysVal ( 'LinkType' );

$tabBox = new CTabBox ( '?m=links', apm_BASE_DIR . '/modules/links/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $linkTypes, $AppUI->_ ( 'All Links', UI_OUTPUT_RAW ) );
}
foreach ( $linkTypes as $link_type ) {
	$tabBox->add ( 'index_table', $link_type );
}
$showProject = true;
$tabBox->show ();