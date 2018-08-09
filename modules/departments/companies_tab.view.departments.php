<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $AppUI, $company;

$items = $company->departments ( $company->company_id );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'departments', 'company_view' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'dept_name',
			'dept_users' 
	);
	$fieldNames = array (
			'Name',
			'Users' 
	);
	
	$module->storeSettings ( 'departments', 'company_view', $fieldList, $fieldNames );
	
	$fields = array_combine ( $fieldList, $fieldNames );
}
?>
<a name="departments-company_view"> </a>
<?php

$dept_types = apmgetSysVal ( 'DepartmentType' );
$customLookups = array (
		'dept_type' => $dept_types 
);

include $AppUI->getTheme ()->resolveTemplate ( 'companies/vw_depts' );