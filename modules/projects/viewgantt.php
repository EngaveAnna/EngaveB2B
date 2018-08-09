<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $company_id, $dept_ids, $department, $min_view, $m, $a, $user_id, $tab, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();

$min_view = defVal ( $min_view, false );
$project_id = apmgetParam ( $_GET, 'project_id', 0 );
$user_id = apmgetParam ( $_GET, 'user_id', $AppUI->user_id );
// sdate and edate passed as unix time stamps
$sdate = apmgetParam ( $_POST, 'project_start_date', 0 );
$edate = apmgetParam ( $_POST, 'project_end_date', 0 );
$showInactive = apmgetParam ( $_POST, 'showInactive', '0' );
$showLabels = apmgetParam ( $_POST, 'showLabels', '0' );
$sortTasksByName = apmgetParam ( $_POST, 'sortTasksByName', '0' );
$showAllGantt = apmgetParam ( $_POST, 'showAllGantt', '0' );
$showTaskGantt = apmgetParam ( $_POST, 'showTaskGantt', '0' );
$addPwOiD = apmgetParam ( $_POST, 'add_pwoid', isset ( $addPwOiD ) ? $addPwOiD : 0 );
$owner_id=$user_id;

// if set GantChart includes user labels as captions of every GantBar
if ($showLabels != '0') {
	$showLabels = '1';
}
if ($showInactive != '0') {
	$showInactive = '1';
}

if ($showAllGantt != '0') {
	$showAllGantt = '1';
}

$projectStatus = apmgetSysVal ( 'ProjectStatus' );

if (isset ( $_POST ['proFilter'] )) {
	$AppUI->setState ( 'ProjectIdxFilter', $_POST ['proFilter'] );
}
$proFilter = $AppUI->getState ( 'ProjectIdxFilter' ) !== null ? $AppUI->getState ( 'ProjectIdxFilter' ) : '-1';

$projFilter = arrayMerge ( array (
		'-1' => 'All Projects' 
), $projectStatus );
// months to scroll
$scroll_date = 1;

$display_option = apmgetParam ( $_POST, 'display_option', 'this_month' );

// format dates
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

if ($display_option == 'custom') {
	// custom dates
	$start_date = intval ( $sdate ) ? new apm_Utilities_Date ( $sdate ) : new apm_Utilities_Date ();
	$end_date = intval ( $edate ) ? new apm_Utilities_Date ( $edate ) : new apm_Utilities_Date ();
} else {
	// month
	$start_date = new apm_Utilities_Date ();
	$start_date->day = 1;
	$end_date = new apm_Utilities_Date ( $start_date );
	$end_date->addMonths ( $scroll_date );
}

// setup the title block
if (! $min_view) {
	$titleBlock = new apm_Theme_TitleBlock ( 'Gantt Chart', 'icon.png', $m );
	$titleBlock->addCrumb ( '?m=' . $m, 'projects list' );
	$titleBlock->show ();
}

?>

<script language="javascript" type="text/javascript">

function scrollPrev() {
	f = document.editFrm;
<?php
$new_start = new apm_Utilities_Date ( $start_date );
$new_start->day = 1;
$new_end = new apm_Utilities_Date ( $end_date );
$new_start->addMonths ( - $scroll_date );
$new_end->addMonths ( - $scroll_date );

echo "f.project_start_date.value='" . $new_start->format ( FMT_TIMESTAMP_DATE ) . "';";
echo "f.project_end_date.value='" . $new_end->format ( FMT_TIMESTAMP_DATE ) . "';";
?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function scrollNext() {
	f = document.editFrm;
<?php
$new_start = new apm_Utilities_Date ( $start_date );
$new_start->day = 1;
$new_end = new apm_Utilities_Date ( $end_date );
$new_start->addMonths ( $scroll_date );
$new_end->addMonths ( $scroll_date );
echo "f.project_start_date.value='" . $new_start->format ( FMT_TIMESTAMP_DATE ) . "';";
echo "f.project_end_date.value='" . $new_end->format ( FMT_TIMESTAMP_DATE ) . "';";
?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function showThisMonth() {
	document.editFrm.display_option.value = 'this_month';
	document.editFrm.submit();
}

function showFullProject() {
	document.editFrm.display_option.value = 'all';
	document.editFrm.submit();
}

</script>
<form name="editFrm" method="post"	action="?<?php echo 'm=' . $m . '&a=' . $a . (isset($user_id) ? '&user_id=' . $user_id : '') . '&tab=' . $tab; ?>"	accept-charset="utf-8">
	
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">	
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Gantt params'); ?></td>
	</tr>
	</thead>
	<tbody>		
	
	<input type="hidden" name="display_option"
		value="<?php echo $display_option; ?>" /> <input type="hidden"
		name="datePicker" value="project" />

					<tr>


						<td class="apm-label"><?php echo $AppUI->_('From'); ?>:</td>
						<td align="left" nowrap="nowrap" class="input-group date"><input type="hidden"
							name="project_start_date" id="project_start_date"
							value="<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
							<input type="text" name="start_date" id="start_date"
							onchange="setDate_new('editFrm', 'start_date');"
							value="<?php echo $start_date ? $start_date->format($df) : ''; ?>"
							class="form-control" /> 
							<span class="input-group-addon"><a href="javascript: void(0);"
							onclick="return showCalendar('start_date', '<?php echo $df ?>', 'editFrm', null, true, true)">
								<i class="glyphicon glyphicon-calendar btn-default"></i>
						</a></span></td>
</tr><tr>
						<td class="apm-label"><?php echo $AppUI->_('To'); ?>:</td>
						<td align="left" nowrap="nowrap" class="input-group date"><input type="hidden"
							name="project_end_date" id="project_end_date"
							value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
							<input type="text" name="end_date" id="end_date"
							onchange="setDate_new('editFrm', 'end_date');"
							value="<?php echo $end_date ? $end_date->format($df) : ''; ?>"
							class="form-control" />
							<span class="input-group-addon"><a href="javascript: void(0);"
							onclick="return showCalendar('end_date', '<?php echo $df ?>', 'editFrm', null, true, true)">
								<i class="glyphicon glyphicon-calendar btn-default"></i>
						</a></span></td>
</tr><tr>				
<td class="apm-label"><?php echo $AppUI->_('Projects'); ?>:</td>		
						<td>
                            <?php echo arraySelect($projFilter, 'proFilter', 'size="1" class="form-control"', $proFilter, true); ?>
                        </td>
</tr><tr>                        
						<td class="apm-label"><?php echo $AppUI->_('Show captions'); ?>	</td>                       
						<td><input type="checkbox" name="showLabels" id="showLabels"
							value="1"
							<?php echo (($showLabels == 1) ? 'checked="checked"' : ""); ?> /></td>
</tr><tr>
						<td class="apm-label"><?php echo $AppUI->_('Show Archived/Templates'); ?></td>  						
						<td><input type="checkbox" value="1" name="showInactive"
							id="showInactive"
							<?php echo (($showInactive == 1) ? 'checked="checked"' : ""); ?> /></td>
</tr><tr>
						<td class="apm-label"><?php echo $AppUI->_('Show Tasks'); ?></td> 						
						<td><input type="checkbox" value="1" name="showAllGantt"
							id="showAllGantt"
							<?php echo (($showAllGantt == 1) ? 'checked="checked"' : ""); ?> /></td>
						

</tr><tr> 
						<td class="apm-label"><?php echo $AppUI->_('Sort Tasks By Name'); ?>
						</td> 						
						<td valign="top"><input type="checkbox" value="1"
							name="sortTasksByName" id="sortTasksByName"
							<?php echo (($sortTasksByName == 1) ? 'checked="checked"' : ""); ?> /></td>
</tr>
</tbody></table></div>



<div>
<input type="button" class="btn btn-info" value="<?php echo $AppUI->_('submit'); ?>" onclick='document.editFrm.display_option.value="custom";submit();' />
<?php echo '<button class="btn btn-default" href="javascript:showThisMonth()">' . $AppUI->_('show this month') . '</button> <button class="btn btn-default" href="javascript:showFullProject()">'. $AppUI->_('show all') . '</button>'; ?>
</div>
</div>


<div style="clear:both; margin:15px 0; padding-top:20px;">
<?php $src = '?m=projects&a=gantt&suppressHeaders=1' . ($display_option == 'all' ? '' : '&start_date=' . $start_date->format ( '%Y-%m-%d' ) . '&end_date=' . $end_date->format ( '%Y-%m-%d' )) . "&width=' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '&showLabels=$showLabels&sortTasksByName=$sortTasksByName&proFilter=$proFilter&showInactive=$showInactive&company_id=$company_id&department=$department&dept_ids=$dept_ids&showAllGantt=$showAllGantt&user_id=$user_id&addPwOiD=$addPwOiD";
 echo "<script>document.write('<img src=\"$src\">')</script>";
?>
</div>
					

</form>