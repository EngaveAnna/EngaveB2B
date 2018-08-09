<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $this_day, $first_time, $last_time, $company_id, $event_filter, $event_filter_list, $AppUI;

// load the event types
$types = apmgetSysVal ( 'EventType' );
$links = array ();

$perms = &$AppUI->acl ();
$user_id = $AppUI->user_id;
$other_users = false;
$no_modify = false;

if (canView ( 'system' )) {
	$other_users = true;
	if (($show_uid = apmgetParam ( $_REQUEST, 'show_user_events', 0 )) != 0) {
		$user_id = $show_uid;
		$no_modify = true;
		$AppUI->setState ( 'event_user_id', $user_id );
	}
}
// assemble the links for the events
$events = CEvent::getEventsForPeriod ( $first_time, $last_time, $event_filter, $user_id );
$events2 = array ();

$start_hour = apmgetConfig ( 'cal_day_start' );
$end_hour = apmgetConfig ( 'cal_day_end' );
foreach ( $events as $row ) {
	$start = new apm_Utilities_Date ( $row ['event_start_date'] );
	$end = new apm_Utilities_Date ( $row ['event_end_date'] );
	
	$key = $start->format ( '%H%M%S' );
	if (- 1 == $start->compare ( $start, $this_day )) {
		$myhour = ($start_hour < 10) ? '0' . $start_hour : $start_hour;
		$key = $myhour . '0000';
	}
	$events2 [$key] [] = $row;
	
	if ($start_hour > $start->format ( '%H' )) {
		$start_hour = $start->format ( '%H' );
	}
	if ($end_hour < $end->format ( '%H' )) {
		$end_hour = $end->format ( '%H' );
	}
}

$tf = $AppUI->getPref ( 'TIMEFORMAT' );

$dayStamp = $this_day->format ( FMT_TIMESTAMP_DATE );

$start = $start_hour;
$end = $end_hour;
$inc = apmgetConfig ( 'cal_day_increment' );

if ($start === null)
	$start = 8;
if ($end === null)
	$end = 17;
if ($inc === null)
	$inc = 15;

$this_day->setTime ( $start, 0, 0 );

$event = new CEvent ();
$event_filter_list = array (
		'my' => 'My Events',
		'own' => 'Events I Created',
		'all' => 'All Events' 
);

$html = '<form action="' . $_SERVER ['REQUEST_URI'] . '" method="post" name="pickFilter" accept-charset="utf-8">';
$html .= $AppUI->_ ( 'Event Filter' ) . ':' . arraySelect ( $event_filter_list, 'event_filter', 'onChange="document.pickFilter.submit()" class="form-control"', $event_filter, true );
if ($other_users) {
	$html .= $AppUI->_ ( 'Show Events for' ) . ':' . '<select name="show_user_events" onchange="document.pickFilter.submit()" class="form-control">';
	
	if (($rows = apmgetUsersList ())) {
		foreach ( $rows as $row ) {
			if ($user_id == $row ['user_id'])
				$html .= '<option value="' . $row ['user_id'] . '" selected="selected">' . $row ['contact_first_name'] . ' ' . $row ['contact_last_name'];
			else
				$html .= '<option value="' . $row ['user_id'] . '">' . $row ['contact_first_name'] . ' ' . $row ['contact_last_name'];
		}
	}
	$html .= '</select>';
}

$html .= '</form>';

// $start_date = new apm_Utilities_Date('2001-01-01 00:00:00');
// $end_date = new apm_Utilities_Date('2100-12-31 23:59:59');

$start_date = $first_time;
$end_date = $last_time;

$items = CEvent::getEventsForPeriod ( $start_date, $end_date, 'all', 0, $project_id );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'events', 'project_view' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'event_start_date',
			'event_end_date',
			'event_type',
			'event_name' 
	);
	$fieldNames = array (
			'Start Date',
			'End Date',
			'Type',
			'Event' 
	);
	$module->storeSettings ( 'events', 'project_view', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}

?><a name="events-project_view"> </a><?php

$event_types = apmgetSysVal ( 'EventType' );
$customLookups = array (
		'event_type' => $event_types 
);
// echo "<pre>"; print_r($fields); echo "</pre>";
// echo "<pre>"; print_r($items); echo "</pre>";
$listTable = new apm_Output_ListTable ( $AppUI );
$listTable->df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' ); // @todo cleanup this hack
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();