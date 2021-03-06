<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $AppUI, $company, $tab;

$sort = apmgetParam ( $_GET, 'sort', 'project_name' );
if ($sort == 'project_priority') {
	$sort .= ' DESC';
}

$items = $company->projects ( $AppUI, $company->company_id, ! $tab, $sort );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'projects', 'company_view' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'project_priority',
			'project_name',
			'user_username',
			'project_start_date',
			'project_status',
			'project_target_budget' 
	);
	$fieldNames = array (
			'P',
			'Name',
			'Owner',
			'Started',
			'Status',
			'Budget' 
	);
	
	$module->storeSettings ( 'projects', 'company_view', $fieldList, $fieldNames );
	
	$fields = array_combine ( $fieldList, $fieldNames );
}
?>
<a name="projects-company_view"> </a>
<?php

$pstatus = apmgetSysVal ( 'ProjectStatus' );
$countries = apmgetSysVal ( 'GlobalCountries' );
$company_types = apmgetSysVal ( 'CompanyType' );
$customLookups = array (
		'project_status' => $pstatus,
		'company_type' => $company_types,
		'company_country' => $countries 
);

include $AppUI->getTheme ()->resolveTemplate ( 'companies/vw_projects' );