<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'DeptIdxTab', $_GET, 'tab', 0 );

if (isset ( $_GET ['orderby'] )) {
	$orderdir = $AppUI->getState ( 'DeptIdxOrderDir' ) ? ($AppUI->getState ( 'DeptIdxOrderDir' ) == 'asc' ? 'desc' : 'asc') : 'desc';
	$AppUI->setState ( 'DeptIdxOrderBy', apmgetParam ( $_GET, 'orderby', null ) );
	$AppUI->setState ( 'DeptIdxOrderDir', $orderdir );
}
$orderby = $AppUI->getState ( 'DeptIdxOrderBy' ) ? $AppUI->getState ( 'DeptIdxOrderBy' ) : 'dept_name';
$orderdir = $AppUI->getState ( 'DeptIdxOrderDir' ) ? $AppUI->getState ( 'DeptIdxOrderDir' ) : 'asc';

if (isset ( $_REQUEST ['owner_filter_id'] )) {
	$AppUI->setState ( 'dept_owner_filter_id', apmgetParam ( $_REQUEST, 'owner_filter_id', null ) );
	$owner_filter_id = apmgetParam ( $_REQUEST, 'owner_filter_id', null );
} else {
	$owner_filter_id = $AppUI->getState ( 'dept_owner_filter_id' );
	if (! isset ( $owner_filter_id )) {
		$owner_filter_id = 0; // By default show all companies instead of $AppUI->user_id current user.
		$AppUI->setState ( 'dept_owner_filter_id', $owner_filter_id );
	}
}

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

$perms = &$AppUI->acl ();
$owner_list = array (
		0 => $AppUI->_ ( 'All', UI_OUTPUT_RAW ) 
) + $perms->getPermittedUsers ( 'departments' );

$titleBlock = new apm_Theme_TitleBlock ( 'Departments', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );
$titleBlock->addCell ( '<label>' . $AppUI->_ ( 'Owner' ) . '</label>' );
$titleBlock->addCell ( '<form name="searchform2" action="?m=departments" method="post" accept-charset="utf-8">' . arraySelect ( $owner_list, 'owner_filter_id', 'onChange="document.searchform2.submit()" size="1" class="form-control"', $owner_filter_id ) . '</form>' );

$titleBlock->show ();

// load the department types
$deptTypes = apmgetSysVal ( 'DepartmentType' );

$tabBox = new CTabBox ( '?m=departments', apm_BASE_DIR . '/modules/departments/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $deptTypes, $AppUI->_ ( 'All Departments', UI_OUTPUT_RAW ) );
}

foreach ( $deptTypes as $deptType ) {
	$tabBox->add ( 'vw_depts', $deptType );
}
$tabBox->show ();