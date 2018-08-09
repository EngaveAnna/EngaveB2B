<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

// check permissions for this record
$canRead = canView ( $m );

if (! $canRead) {
	$AppUI->redirect ( ACCESS_DENIED );
}

apmsetMicroTime ();

// retrieve any state parameters
if (isset ( $_REQUEST ['company_id'] )) {
	$AppUI->setState ( 'CalIdxCompany', intval ( apmgetParam ( $_REQUEST, 'company_id', 0 ) ) );
}
$company_id = $AppUI->getState ( 'CalIdxCompany', 0 );

// Using simplified set/get semantics. Doesn't need as much code in the module.
$event_filter = $AppUI->checkPrefState ( 'CalIdxFilter', apmgetParam ( $_REQUEST, 'event_filter', 'my' ), 'EVENTFILTER', 'my' );

// get the passed timestamp (today if none)
$ctoday = new apm_Utilities_Date ();
$ctoday->convertTZ ( $AppUI->getPref ( 'TIMEZONE' ) );
$today = $ctoday->format ( FMT_TIMESTAMP_DATE );
$date = apmgetParam ( $_GET, 'date', $today );

// get the list of visible companies
$company = new CCompany ();
$companies = $company->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$companies = arrayMerge ( array (
		'0' => $AppUI->_ ( 'All companies' ) 
), $companies );
$event_filter_list = array (
		'my' =>  $AppUI->_('My Events for selected Company'),
		'own' =>  $AppUI->_('Events I Created'),
		'all' =>  $AppUI->_('All Events for selected Company') 
);

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Monthly Calendar', 'icon.png', $m );
// $titleBlock->addCrumb('?m=events&date=' . $date, 'month view');
$titleBlock->addCrumb ( '?m=events&a=day_view&date=' . $date, 'day view' );

$titleBlock->addFilterCell ( 'Company', 'company_id', $companies, $company_id );
$titleBlock->addFilterCell ( 'Event Filter', 'event_filter', $event_filter_list, $event_filter );

$titleBlock->addCrumb ( '?m=events&a=addedit&date=' . $today, 'new event', '', true );
$titleBlock->show ();
?>

<script language="javascript" type="text/javascript">
function clickDay( uts, fdate ) {
	window.location = './index.php?m=events&a=day_view&date='+uts+'&tab=0';
}
function clickWeek( uts, fdate ) {
	window.location = './index.php?m=events&a=week_view&date='+uts;
}
</script>




<div>

		
		


<?php
// establish the focus 'date'
$date = new apm_Utilities_Date ( $date );

// prepare time period for 'events'
// "go back" to the first day shown on the calendar
// and "go forward" to the last day shown on the calendar
$first_time = new apm_Utilities_Date ( $date );
$first_time->setDay ( 1 );
$first_time->setTime ( 0, 0, 0 );

// if Sunday is the 1st, we don't need to go back
// as that's the first day shown on the calendar
if ($first_time->getDayOfWeek () != 0) {
	$last_day_of_previous_month = $first_time->getPrevDay ();
	$day_of_previous_month = $last_day_of_previous_month->getDayOfWeek ();
	$seconds_to_sub_in_previous_month = 86400 * $day_of_previous_month;
	// need to cast it to int because Pear::Date_Span::set down the line
	// fails to set the seconds correctly
	$last_day_of_previous_month->subtractSeconds ( ( int ) $seconds_to_sub_in_previous_month );
	
	$first_time->setDay ( $last_day_of_previous_month->getDay () );
	$first_time->setMonth ( $last_day_of_previous_month->getMonth () );
	$first_time->setYear ( $last_day_of_previous_month->getYear () );
}

$last_time = new apm_Utilities_Date ( $date );
$last_time->setDay ( $date->getDaysInMonth () );
$last_time->setTime ( 23, 59, 59 );

// if Saturday is the last day of month, we don't need to go forward
// as that's the last day shown on the calendar
if ($last_time->getDayOfWeek () != 6) {
	$first_day_of_next_month = $last_time->getNextDay ();
	$day_of_next_month = $first_day_of_next_month->getDayOfWeek ();
	$seconds_to_add_in_next_month = 86400 * $day_of_next_month;
	// need to cast it to int because Pear::Date_Span::set down the line
	// fails to set the seconds correctly
	$first_day_of_next_month->addSeconds ( ( int ) $seconds_to_add_in_next_month );
	$last_time->setDay ( $first_day_of_next_month->getDay () );
	$last_time->setMonth ( $first_day_of_next_month->getMonth () );
	$last_time->setYear ( $first_day_of_next_month->getYear () );
}

$user_filter = ($event_filter == 'my') ? $AppUI->user_id : 0;

$links = getTaskLinks ( $first_time, $last_time, array (), 20, $company_id, false, $user_filter );
$links += getEventLinks ( $first_time, $last_time, array (), 20 );

$hooks = new apm_System_HookHandler ( $AppUI );
$hooks->links = $links;
$links = $hooks->calendar_links ();

// create the main calendar
$cal = new apm_Output_MonthCalendar ( $date );
$cal->setStyles ( 'motitle', 'table table-bordered' );
$cal->setLinkFunctions ( 'clickDay', 'clickWeek' );
$cal->setEvents ( $links );

echo $cal->show ();
// echo '<pre>';print_r($cal);echo '</pre>';

?></div>
