<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'CompanyIdxTab', $_GET, 'tab', 0 );

if (isset ( $_GET ['orderby'] )) {
	$orderdir = $AppUI->getState ( 'CompIdxOrderDir' ) ? ($AppUI->getState ( 'CompIdxOrderDir' ) == 'asc' ? 'desc' : 'asc') : 'desc';
	$AppUI->setState ( 'CompIdxOrderBy', apmgetParam ( $_GET, 'orderby', null ) );
	$AppUI->setState ( 'CompIdxOrderDir', $orderdir );
}
$orderby = $AppUI->getState ( 'CompIdxOrderBy' ) ? $AppUI->getState ( 'CompIdxOrderBy' ) : 'company_name';
$orderdir = $AppUI->getState ( 'CompIdxOrderDir' ) ? $AppUI->getState ( 'CompIdxOrderDir' ) : 'asc';

$owner_filter_id = $AppUI->processIntState ( 'owner_filter_id', $_POST, 'owner_filter_id', 0 );

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$search_string = apmformSafe ( $search_string, true );

$company = new CCompany ();
$canCreate = $company->canCreate ();

$perms = &$AppUI->acl ();
$baseArray = array (
		0 => $AppUI->_ ( 'All Users', UI_OUTPUT_RAW ) 
);
$allowedArray = $perms->getPermittedUsers ( 'companies' );
$owner_list = is_array ( $allowedArray ) ? ($baseArray + $allowedArray) : $baseArray;

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Companies', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );
$titleBlock->addFilterCell ( 'Owner', 'owner_filter_id', $owner_list, $owner_filter_id );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=companies&a=addedit', 'New company', '', true );
}
$titleBlock->show ();

// load the company types
$companyTypes = apmgetSysVal ( 'CompanyType' );

$tabBox = new CTabBox ( '?m=companies', apm_BASE_DIR . '/modules/companies/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $companyTypes, $AppUI->_ ( 'All Companies', UI_OUTPUT_RAW ) );
}

foreach ( $companyTypes as $type_name ) {
	$tabBox->add ( 'vw_companies', $type_name );
}
$tabBox->show ();