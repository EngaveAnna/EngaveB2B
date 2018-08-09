<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $cal_sdf;


$do_report = apmgetParam ( $_POST, 'do_report', 0 );
$log_start_date = apmgetParam ( $_POST, 'log_start_date', 0 );
$log_end_date = apmgetParam ( $_POST, 'log_end_date', 0 );
$log_all = apmgetParam ( $_POST ['log_all'], 0 );
$group_by_unit = apmgetParam ( $_POST ['group_by_unit'], 'day' );

// create Date objects from the datetime fields
$start_date = intval ( $log_start_date ) ? new apm_Utilities_Date ( $log_start_date ) : new apm_Utilities_Date ();
$end_date = intval ( $log_end_date ) ? new apm_Utilities_Date ( $log_end_date ) : new apm_Utilities_Date ();

if (! $log_start_date) {
	$start_date->subtractSpan ( new Date_Span ( '14,0,0,0' ) );
}
$end_date->setTime ( 23, 59, 59 );

if ($do_report==1) {
	//echo "do_report: ".$do_report.' date: '.date('U',time()); 

	$source='<table class="table table-bordered table-hover" border="0" cellspacing="0" cellpadding="3"><tr><td>';
	$user_list = apmgetUsersHashList ();
	
	// Now which tasks will we need and the real allocated hours (estimated time / number of users)
	// Also we will use tasks with duration_type = 1 (hours) and those that are not marked
	// as milstones
	// GJB: Note that we have to special case duration type 24 and this refers to the hours in a day, NOT 24 hours
	$working_hours = $apmconfig ['daily_working_hours'];
	
	$q = new apm_Database_Query ();
	$q->addTable ( 'tasks', 't' );
	$q->addTable ( 'user_tasks', 'ut' );
	$q->addJoin ( 'projects', '', 'project_id = task_project', 'inner' );
	$q->addQuery ( 't.task_id, round(t.task_duration * IF(t.task_duration_type = 24, ' . $working_hours . ', t.task_duration_type)/count(ut.task_id),2) as hours_allocated' );
	$q->addWhere ( 't.task_id = ut.task_id' );
	$q->addWhere ( 't.task_milestone = 0' );
	$q->addWhere ( 'project_active = 1' );
	if (($template_status = apmgetConfig ( 'template_projects_status_id' )) != '') {
		$q->addWhere ( 'project_status <> ' . ( int ) $template_status );
	}
	
	if ($project_id != 0) {
		$q->addWhere ( 't.task_project = ' . ( int ) $project_id );
	}
	
	if (! $log_all) {
		$q->addWhere ( 't.task_start_date >= \'' . $start_date->format ( FMT_DATETIME_MYSQL ) . '\'' );
		$q->addWhere ( 't.task_start_date <= \'' . $end_date->format ( FMT_DATETIME_MYSQL ) . '\'' );
	}
	$q->addGroup ( 't.task_id' );
	
	$task_list = $q->loadHashList ( 'task_id' );
	$q->clear ();
	
	$rid=date('U',time());
	$source.='<table border="0" cellspacing="0" cellpadding="5" width="100%">
	<tr>
		<td align="left">'.$AppUI->_ ( 'Owner' ).': '.$AppUI->user_first_name.' '.$AppUI->user_last_name.'<br>'.$AppUI->_ ( 'Report' ).' ID: '.$rid.'</td>
		<td align="right">'.$AppUI->_ ( 'Date' ).' '.date('Y-m-d H:i:s').'</td>		
	</tr>			
	<tr>
		<td align="center"><h1>'.$AppUI->_ ( 'Report' ).': '.$AppUI->_ ( $report_type . '_name' ).'</h1></td>
	</tr>
	</table>';
	
	$source.='<table border="1" cellspacing="0" cellpadding="5">';

	$source.='<tr>
		<th colspan="2">'.$AppUI->_("User").'</th>
		<th>'.$AppUI->_("Hours allocated").'</th>
		<th>'.$AppUI->_("Hours worked").'</th>
		<th>'.$AppUI->_("% of work done (based on duration)").'</th>
		<th>'.$AppUI->_("User Efficiency (based on completed tasks)").'</th>
	</tr>';

	if (count ( $user_list )) {
		$percentage_sum = $hours_allocated_sum = $hours_worked_sum = 0;
		$sum_total_hours_allocated = $sum_total_hours_worked = 0;
		$sum_hours_allocated_complete = $sum_hours_worked_complete = 0;
		
		// TODO: Split times for which more than one users were working...
		foreach ( $user_list as $user_id => $user ) {
			$q->addTable ( 'user_tasks', 'ut' );
			$q->addQuery ( 'task_id' );
			$q->addWhere ( 'user_id = ' . ( int ) $user_id );
			$tasks_id = $q->loadColumn ();
			$q->clear ();
			
			$total_hours_allocated = $total_hours_worked = 0;
			$hours_allocated_complete = $hours_worked_complete = 0;
			
			foreach ( $tasks_id as $task_id ) {
				if (isset ( $task_list [$task_id] )) {
					// Now let's figure out how many time did the user spent in this task
					$q->addTable ( 'task_log' );
					$q->addQuery ( 'SUM(task_log_hours)' );
					$q->addWhere ( 'task_log_task =' . ( int ) $task_id );
					$q->addWhere ( 'task_log_creator =' . ( int ) $user_id );
					$hours_worked = round ( $q->loadResult (), 2 );
					$q->clear ();
					
					$q->addTable ( 'tasks' );
					$q->addQuery ( 'task_percent_complete' );
					$q->addWhere ( 'task_id =' . ( int ) $task_id );
					$percent = $q->loadColumn ();
					$q->clear ();
					$complete = ($percent [0] == 100);
					
					if ($complete) {
						$hours_allocated_complete += $task_list [$task_id] ['hours_allocated'];
						$hours_worked_complete += $hours_worked;
					}
					
					$total_hours_allocated += $task_list [$task_id] ['hours_allocated'];
					$total_hours_worked += $hours_worked;
				}
			}
			
			$sum_total_hours_allocated += $total_hours_allocated;
			$sum_total_hours_worked += $total_hours_worked;
			
			$sum_hours_allocated_complete += $hours_allocated_complete;
			$sum_hours_worked_complete += $hours_worked_complete;
			
			if ($total_hours_allocated > 0 || $total_hours_worked > 0) {
				$percentage = 0;
				$percentage_e = 0;
				if ($total_hours_worked > 0) {
					$percentage = ($total_hours_worked / $total_hours_allocated) * 100;
					if ($hours_worked_complete > 0) {
						$percentage_e = ($hours_allocated_complete / $hours_worked_complete) * 100;
					}
				}
				
		$source.='<tr><td>(' . $user['user_username'] . ') </td><td>'.$user['contact_first_name'].' '.$user['contact_last_name'].'</td>
		<td align="right">'.$total_hours_allocated.'</td>
		<td align="right">'.$total_hours_worked.'</td>
		<td align="right">'.number_format($percentage, 0).'% </td>
		<td align="right">'.number_format($percentage_e, 0).'% </td>
		</tr>';
			}
		}
		$sum_percentage = 0;
		$sum_efficiency = 0;
		if ($sum_total_hours_worked > 0) {
			$sum_percentage = ($sum_total_hours_worked / $sum_total_hours_allocated) * 100;
			if ($sum_hours_worked_complete > 0)
				$sum_efficiency = ($sum_hours_allocated_complete / $sum_hours_worked_complete) * 100;
		}
		
		$source.='<tr><td colspan="2">'.$AppUI->_('Total').'</td>
		<td align="right">'.$sum_total_hours_allocated.'</td>
		<td align="right">'.$sum_total_hours_worked.'</td>
		<td align="right">'.number_format($sum_percentage, 0).'%</td>
		<td align="right">'.number_format($sum_efficiency, 0).'%</td>
		</tr>';
		} else {
		$source.='<tr><td><p>'.$AppUI->_('There are no tasks that fulfill selected filters').'</p></td></tr>';
	}
	$source .='</table></td></tr></table>';

	require_once("./modules/reports/do_report_pdf.php");
}
else 
{	
$form = new apm_Output_HTML_FormHelper ( $AppUI );	
$AppUI->getTheme ()->loadCalendarJS ();	
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
?>
<form name="editFrm" action="?m=<?php echo $m; ?>&report_type=<?php echo $report_type; ?>&project_id=<?php echo $project_id; ?>&suppressHeaders=true" method="post" accept-charset="utf-8">
<input type="hidden" name="project_id"	value="<?php echo $project_id; ?>" /> 
<input type="hidden" name="report_type" value="<?php echo $report_type; ?>" /> 
<input type="hidden" name="datePicker" value="report">
<input type="hidden" name="do_report" value="1">
<input type="hidden" name="log_all" id="log_all" value="1" />

<div class="panel panel-default">
	<div class="panel-heading"><?php echo $AppUI->_ ( $report_type . '_name' ); ?>
    </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Report scope'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Start date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('report_start_date', date('Y-m-d H:i:s', time())); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Start date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('report_end_date', date('Y-m-d H:i:s', time())); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				</div>
				
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="alert alert-success"><span class="fa fa-alert fa-info-circle"></span>
					<a class="close" data-dismiss="alert" href="#">×</a>
					<?php echo $AppUI->_ ( 'Report' ).': '.$AppUI->_ ( $report_type . '_name' ).'. '.$AppUI->_ ( $report_type . '_desc' ).'.';?>
				</div>	

				<div>
					<?php $form->showCancelButton(); ?>
					<button class="btn btn-info" onclick="submitIt('preview');" /><span class="fa fa-file-pdf-o" aria-hidden="true" style="margin-right:5px;"></span><?php echo $AppUI->_ ( 'preview raport' );?></button>
	            </div>
				</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
</form>
<script language="javascript" charset="utf-8" type="text/javascript">
	function submitIt(hid)
	{
		editFrm.submit();
	}
</script>
<?php
}
?>