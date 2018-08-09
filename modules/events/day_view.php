<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

// check permissions for this record
$perms = &$AppUI->acl ();
$canRead = canView ( $m );

if (! $canRead) {
	$AppUI->redirect ( ACCESS_DENIED );
}

global $tab, $locale_char_set, $date;
$company_id = $AppUI->processIntState ( 'CalIdxCompany', $_REQUEST, 'company_id', $AppUI->user_company );
$event_filter = $AppUI->checkPrefState ( 'CalIdxFilter', apmgetParam ( $_REQUEST, 'event_filter', '' ), 'EVENTFILTER', 'my' );
$tab = $AppUI->processIntState ( 'CalDayViewTab', $_GET, 'tab', (isset ( $tab ) ? $tab : 0) );

// get the prefered date format
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

// get the passed timestamp (today if none)
$ctoday = new apm_Utilities_Date ();
$today = $ctoday->format ( FMT_TIMESTAMP_DATE );
$date = ( int ) apmgetParam ( $_GET, 'date', $today );
// establish the focus 'date'
$this_day = new apm_Utilities_Date ( $date );
$dd = $this_day->getDay ();
$mm = $this_day->getMonth ();
$yy = $this_day->getYear ();

// get current week
$this_week = Date_Calc::beginOfWeek ( $dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY );

// prepare time period for 'events'
$first_time = clone $this_day;
$first_time->setTime ( 0, 0, 0 );

$last_time = clone $this_day;
$last_time->setTime ( 23, 59, 59 );

$prev_day = new apm_Utilities_Date ( Date_Calc::prevDay ( $dd, $mm, $yy, FMT_TIMESTAMP_DATE ) );
$next_day = new apm_Utilities_Date ( Date_Calc::nextDay ( $dd, $mm, $yy, FMT_TIMESTAMP_DATE ) );

// get the list of visible companies
$company = new CCompany ();
global $companies;
$companies = $company->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$companies = arrayMerge ( array (
		'0' => $AppUI->_ ( 'All' ) 
), $companies );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Day View', 'icon.png', $m );
$titleBlock->addCrumb ( '?m=events&date=' . $this_day->format ( FMT_TIMESTAMP_DATE ), 'month view' );
$titleBlock->addCell ( '<label>' . $AppUI->_ ( 'Company' ) . '</label>' );
$titleBlock->addCell ( arraySelect ( $companies, 'company_id', 'onChange="document.pickCompany.submit()" class="form-control"', $company_id ), '', '<form action="' . $_SERVER ['REQUEST_URI'] . '" method="post" name="pickCompany" accept-charset="utf-8">', '</form>' );

$titleBlock->addCrumb ( '?m=events&a=addedit&date=' . $today, 'new event', '', true );

$titleBlock->show ();
?>
<script language="javascript">
function clickDay( idate, fdate ) {
        window.location = './index.php?m=events&a=day_view&date='+idate+'&tab=0';
}
</script>



<div class="panel panel-default">
	<div class="panel-heading">
		<tr>
			<td><a
				href="<?php echo '?m=events&a=day_view&date=' . $prev_day->format(FMT_TIMESTAMP_DATE); ?>"><span
					class="glyphicon glyphicon-chevron-left"></span></a></td>
			<th width="100%" class="bolder">
                        <?php echo $AppUI->_(htmlspecialchars($this_day->format('%A'), ENT_COMPAT, $locale_char_set)) . ', ' . $this_day->format($df); ?>
                    </th>
			<td><a
				href="<?php echo '?m=events&a=day_view&date=' . $next_day->format(FMT_TIMESTAMP_DATE); ?>"><span
					class="glyphicon glyphicon-chevron-right"></span></a></td>
		</tr>
	</div>
	<div class="panel-body">
		<div id="event-day-view-panel-body"
			class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php
												// tabbed information boxes
												$tabBox = new CTabBox ( '?m=events&a=day_view&date=' . $this_day->format ( FMT_TIMESTAMP_DATE ), apm_BASE_DIR . '/modules/events/', $tab );
												$tabBox->add ( 'vw_day_tasks', 'Tasks' );
												$tabBox->add ( 'vw_day_events', 'Events' );
												$tabBox->show ();
												?>	
		
		</div>
	</div>
</div>

