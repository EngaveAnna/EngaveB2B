<?php
require_once 'base.php';
require_once apm_BASE_DIR . '/includes/config.php';
require_once apm_BASE_DIR . '/includes/main_functions.php';
require_once apm_BASE_DIR . '/includes/db_adodb.php';

$AppUI = new apm_Core_CAppUI ();

$token = apmgetParam ( $_GET, 'token', '' );
$token = preg_replace ( "/[^A-Za-z0-9]/", "", $token );
$format = apmgetParam ( $_GET, 'format', 'ical' );

$user = new CUser ();
$userId = $user->getIdByToken ( $token );
$AppUI->loadPrefs ( $userId );
$AppUI->user_id = $userId;
$AppUI->setUserLocale ();
include apm_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php';
include apm_BASE_DIR . '/locales/core.php';

$defaultTZ = apmgetConfig ( 'system_timezone', 'UTC' );
$defaultTZ = ('' == $defaultTZ) ? 'UTC' : $defaultTZ;
date_default_timezone_set ( $defaultTZ );

switch ($format) {
	// TODO: We only output in vCal, are there others we need to consider?
	case 'vcal' :
	default :
		$format = 'vcal';
		header ( 'Content-Type: text/calendar' );
		header ( 'Content-disposition: attachment; filename="calendar.ics"' );
		break;
}

if ($userId > 0) {
	$myTimezoneName = date ( 'e' );
	$calendarHeader = "BEGIN:VCALENDAR\nPRODID:-//apmProject//EN\nVERSION:2.0\nCALSCALE:GREGORIAN\nMETHOD:PUBLISH\nX-WR-TIMEZONE:UTC\n";
	$calendarFooter = "END:VCALENDAR";
	
	$hooks = new apm_System_HookHandler ( $AppUI );
	$buffer = $hooks->calendar ();
	
	echo $calendarHeader . $buffer . $calendarFooter;
}