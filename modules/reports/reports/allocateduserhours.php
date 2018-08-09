<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $cal_sdf;

$coarseness = apmgetParam ( $_POST, 'coarseness', 1 );
$do_report = apmgetParam ( $_POST, 'do_report', 0 );
$hideNonWd = apmgetParam ( $_POST, 'hideNonWd', 0 );
$log_start_date = apmgetParam ( $_POST, 'report_start_date', 0 );
$log_end_date = apmgetParam ( $_POST, 'report_end_date', 0 );
$use_assigned_percentage = apmgetParam ( $_POST, 'use_assigned_percentage', 0 );
$user_id = apmgetParam ( $_POST, 'log_owner', 0 );

// create Date objects from the datetime fields
$start_date = intval ( $log_start_date ) ? new apm_Utilities_Date ( $log_start_date ) : new apm_Utilities_Date ( date ( 'Y-m-01' ) );
$end_date = intval ( $log_end_date ) ? new apm_Utilities_Date ( $log_end_date ) : new apm_Utilities_Date ();
$end_date->setTime ( 23, 59, 59 );

// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'reports' );
$user_list=$users;
$user_list[0]=$AppUI->_('All users');


if ($do_report==1) 
{ 
	error_reporting(0);
	
	$rid=date('U',time());
	$source.='<table border="0" cellspacing="0" cellpadding="5" width="100%">
	<tr>
		<td align="left">'.$AppUI->_ ( 'Owner' ).': '.$AppUI->user_first_name.' '.$AppUI->user_last_name.'<br>'.$AppUI->_ ( 'Report' ).' ID: '.$rid.'</td>
		<td align="right">'.$AppUI->_ ( 'Date' ).': '.date('Y-m-d H:i:s').'</td>
	</tr>
	<tr>
		<td align="center"><h1>'.$AppUI->_ ( 'Report' ).': '.$AppUI->_ ( $report_type . '_name' ).'</h1></td>
	</tr></table>';
	$source.='<table cellspacing="0" cellpadding="5" width="100%"><tr><td width="50%;">';
	
	$q = new apm_Database_Query ();
	$q->addTable ( 'tasks', 't' );
	$q->addTable ( 'user_tasks', 'ut' );
	$q->addTable ( 'projects', 'pr' );
	$q->addQuery ( 't.*, ut.*, pr.project_name' );
	$q->addWhere ( '( task_start_date BETWEEN \'' . $start_date->format ( FMT_DATETIME_MYSQL ) . '\' AND \'' . $end_date->format ( FMT_DATETIME_MYSQL ) . '\' 
	           OR task_end_date	BETWEEN \'' . $start_date->format ( FMT_DATETIME_MYSQL ) . '\' 
	                AND \'' . $end_date->format ( FMT_DATETIME_MYSQL ) . '\' 
		   OR ( task_start_date <= \'' . $start_date->format ( FMT_DATETIME_MYSQL ) . '\'
	                AND task_end_date >= \'' . $end_date->format ( FMT_DATETIME_MYSQL ) . '\') )' );
	$q->addWhere ( 'task_end_date IS NOT NULL' );
	$q->addWhere ( 'task_end_date <> \'0000-00-00 00:00:00\'' );
	$q->addWhere ( 'task_start_date IS NOT NULL' );
	$q->addWhere ( 'task_start_date <> \'0000-00-00 00:00:00\'' );
	$q->addWhere ( 'task_dynamic <> 1' );
	$q->addWhere ( 'task_milestone = 0' );
	$q->addWhere ( 'task_duration  > 0' );
	$q->addWhere ( 't.task_project = pr.project_id' );
	$q->addWhere ( 't.task_id = ut.task_id' );
	$q->addWhere ( 'pr.project_active = 1' );
	if (($template_status = apmgetConfig ( 'template_projects_status_id' )) != '') {
		$q->addWhere ( 'pr.project_status <> ' . ( int ) $template_status );
	}
	
	if ($user_id!=0) {
		$q->addWhere('t.task_owner = '.$user_id);
	}
	if ($project_id != 0) {
		$q->addWhere('t.task_project = '.$project_id);
	}
	
	$proj = new CProject ();
	$q = $proj->setAllowedSQL ( $AppUI->user_id, $q, null, 'pr' );
	
	$obj = new CTask ();
	$q = $obj->setAllowedSQL ( $AppUI->user_id, $q );
	
	$task_list_hash = $q->loadHashList ( 'task_id' );
	
	$q->clear ();
	
	$task_list = array ();
	$fetched_projects = array ();
	foreach ( $task_list_hash as $task_id => $task_data ) {
		$task = new CTask ();
		$task->bind ( $task_data );
		$task_list [] = $task;
		$fetched_projects [$task->task_project] = $task_data ['project_name'];
	}
	
	$user_usage = array ();
	$task_dates = array ();
	
	$actual_date = $start_date;
	$days_header = ''; // we will save days title here
	
	$user_tasks_counted_in = array ();
	$user_names = array ();


	$source_ext=$AppUI->_ ( 'Task owner' ).': '.$users[$user_id].'<br>'.$AppUI->_ ( 'Period' ).': '.$start_date->format('%Y-%m-%d').' - '.$end_date->format('%Y-%m-%d');
	$source_ext.='</td></tr></table>';

	if (count ( $task_list ) == 0) {
		$source.='<p>' . $AppUI->_ ( 'No data available' ) . '</p>';
	} else {
		foreach ( $task_list as $task ) {
			$task_start_date = new apm_Utilities_Date ( $task->task_start_date );
			$task_end_date = new apm_Utilities_Date ( $task->task_end_date );
			
			$day_difference = $task_end_date->dateDiff ( $task_start_date );
			$actual_date = $task_start_date;
			
			$users = $task->getAssignedUsers ( $task->task_id );
			
			if ($coarseness == 1) {
				userUsageDays ();
			} elseif ($coarseness == 7) {
				userUsageWeeks ();
			}
		}
		
		if ($coarseness == 1) {
			showDays ();
		} elseif ($coarseness == 7) {
			showWeeks ();
		}
		

	}
	$source.='<h4>' . $AppUI->_ ( 'Total capacity for shown users' ) . '</h4>';
	$source.=$AppUI->_ ( 'Allocated hours' ) . ': ' . number_format ( $allocated_hours_sum, 2, ',', '') . '<br />';
	$source.=$AppUI->_ ( 'Total capacity' ) . ': ' . number_format ( $total_hours_capacity, 2, ',', '' ) . '<br />';
	$source.=$AppUI->_ ( 'Percentage used' ) . ': ' . (($total_hours_capacity > 0) ? number_format ( $allocated_hours_sum / $total_hours_capacity, 2 ) * 100 : 0) . '%<br />';
	$source.='</td><td width="50%;">';
	$source.=$source_ext;

//	print_r($users);
	
	foreach ( $user_tasks_counted_in as $user_id2 => $project_information ) {
		

		foreach ( $project_information as $project_id => $task_information ) {
			$source.='<br><table cellspacing="0" cellpadding="5" width="100%" border="1">';
		
			$source.='<tr><td colspan="3"><span style="text-align:left;" align="left"><b>' . $users[$user_id2] ['contact_first_name'].' '.$users[$user_id2] ['contact_last_name'].': '.$fetched_projects [$project_id] . '</b></span></th></tr>';
			
			$project_total = 0;
			$n=1;
			foreach ( $task_information as $task_id => $hours_assigned ) {
				$source.='<tr><td width="5%">'.$n.'. </td><td>' . $task_list_hash [$task_id] ['task_name'] . '</td><td style="text-align:right;">' . number_format ( round ( $hours_assigned, 2 ), 2 ) . ' godz.</td></tr>';
				$project_total += round ( $hours_assigned, 2 );
				$n++;
			}
			$source.='<tr><td colspan="2" align="right"><b>' . $AppUI->_ ( 'Total assigned' ) . '</b></td><td style="text-align:right;"><b>' . number_format ( $project_total, 2 ) . ' godz.</b></td></tr>';
			$source.='</table>';
		}

	}
	$source.='</td></tr></table>';
	
	


//	exit();
	require_once("./modules/reports/do_report_pdf.php");
}
else 
{

	
	$form = new apm_Output_HTML_FormHelper ( $AppUI );
	$AppUI->getTheme ()->loadCalendarJS ();
	$df = $AppUI->getPref ( 'SHDATEFORMAT' );
	
	?>
	<form name="editFrm" action="index.php?m=reports&suppressHeaders=true" method="post" accept-charset="utf-8">
	<input type="hidden" name="project_id"	value="<?php echo $project_id; ?>" /> 
	<input type="hidden" name="report_category" value="<?php echo $report_category; ?>" /> 
	<input type="hidden" name="report_type" value="<?php echo $report_type; ?>" />
	<input type="hidden" name="datePicker" value="report" />
	<input type="hidden" name="do_report" value="1" />
	<input type="hidden" name="use_assigned_percentage" value="0" />
	<input type="hidden" name="coarseness" value="1"/>
	<input type="hidden" name="hideNonWd" value="1"  />
								</td>
							</tr>
	
	
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $AppUI->_ ( $report_type . '_name' ); ?></div>
			<div class="panel-body">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%" class="table table-bordered table-striped table-static">
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

							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Tasks created by'); ?></td>
								<td>
								<?php echo $form->showField('log_owner', $user_list[$AppUI->user_id],  array(), $users); ?>
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
<?php } ?>