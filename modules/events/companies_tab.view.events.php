<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $company_id;

$start_date = new apm_Utilities_Date ( '1999-12-31 00:00:00' );
$end_date = new apm_Utilities_Date ( '2036-12-31 00:00:00' );

$items = CEvent::getEventsForPeriod ( $start_date, $end_date, 'all', 0, 0, $company_id );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'events', 'company_view' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'event_start_date',
			'event_end_date',
			'event_type',
			'event_name' 
	);
	$fieldNames = array (
			'Starting Time',
			'Ending Time',
			'Type',
			'Name' 
	);
	
	$module->storeSettings ( 'events', 'company_view', $fieldList, $fieldNames );
	
	$fields = array_combine ( $fieldList, $fieldNames );
}

$event_types = apmgetSysVal ( 'EventType' );
$customLookups = array (
		'event_type' => $event_types 
);

$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' ); // @todo cleanup this hack
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();