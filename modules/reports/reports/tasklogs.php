<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

/**
 * Generates a report of the task logs for given dates
 */
global $AppUI, $cal_sdf;
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$perms = &$AppUI->acl ();
if (! canView ( 'task_log' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$do_report = apmgetParam ( $_POST, 'do_report', 0 );
$log_all = apmgetParam ( $_POST, 'log_all', 0 );
$log_pdf = apmgetParam ( $_POST, 'log_pdf', 0 );
$log_ignore = apmgetParam ( $_POST, 'log_ignore', 0 );
$log_userfilter = apmgetParam ( $_POST, 'log_userfilter', '0' );

$log_start_date = apmgetParam ( $_POST, 'log_start_date', 0 );
$log_end_date = apmgetParam ( $_POST, 'log_end_date', 0 );

// create Date objects from the datetime fields
$start_date = intval ( $log_start_date ) ? new apm_Utilities_Date ( $log_start_date ) : new apm_Utilities_Date ();
$end_date = intval ( $log_end_date ) ? new apm_Utilities_Date ( $log_end_date ) : new apm_Utilities_Date ();

if (! $log_start_date) {
	$start_date->subtractSpan ( new Date_Span ( '14,0,0,0' ) );
}
$end_date->setTime ( 23, 59, 59 );

// Lets check cost codes
$q = new apm_Database_Query ();
$q->addTable ( 'billingcode' );
$q->addQuery ( 'billingcode_id, billingcode_name' );

$task_log_costcodes [0] = $AppUI->_ ( 'None' );
$rows = $q->loadList ();

$nums = 0;
if ($rows) {
	$nums = count ( $rows );
}
foreach ( $rows as $row ) {
	$task_log_costcodes [$row ['billingcode_id']] = $row ['billingcode_name'];
}

if ($do_report==1) {
	
	$q = new apm_Database_Query ();
	$q->addTable ( 'task_log', 't' );
	$q->addQuery ( 'distinct(t.task_log_id), contact_display_name AS creator' );
	$q->addQuery ( 'billingcode_value, billingcode_name' );
	$q->addQuery ( 'ROUND((billingcode_value * t.task_log_hours), 2) AS amount' );
	$q->addQuery ( 'c.company_name, project_name' );
	$q->addQuery ( 'ts.task_name, task_log_task, task_log_hours, task_log_description, task_log_date' );
	
	$q->addJoin ( 'tasks', 'ts', 'ts.task_id = t.task_log_task' );
	$q->addJoin ( 'projects', '', 'projects.project_id = ts.task_project' );
	$q->addJoin ( 'users', 'u', 'user_id = task_log_creator' );
	$q->addJoin ( 'contacts', '', 'user_contact = contact_id' );
	$q->addJoin ( 'companies', 'c', 'c.company_id = projects.project_company' );
	$q->leftJoin ( 'billingcode', '', 'billingcode_id = task_log_costcode' );
	$q->addJoin ( 'project_departments', '', 'project_departments.project_id = projects.project_id' );
	$q->addJoin ( 'departments', '', 'department_id = dept_id' );
	$q->addWhere ( 'task_log_task > 0' );
	
	if ($project_id) {
		$q->addWhere ( 'projects.project_id = ' . ( int ) $project_id );
	}
	if ($company_id) {
		$q->addWhere ( 'c.company_id = ' . ( int ) $company_id );
	}
	
	if (! $log_all) {
		$q->addWhere ( 'task_log_date >= \'' . $start_date->format ( FMT_DATETIME_MYSQL ) . '\'' );
		$q->addWhere ( 'task_log_date <= \'' . $end_date->format ( FMT_DATETIME_MYSQL ) . '\'' );
	}
	if ($log_ignore) {
		$q->addWhere ( 'task_log_hours > 0' );
	}
	if ($log_userfilter) {
		$q->addWhere ( 'task_log_creator = ' . ( int ) $log_userfilter );
	}
	
	$proj = new CProject ();
	$allowedProjects = $proj->getAllowedSQL ( $AppUI->user_id, 'task_project' );
	if (count ( $allowedProjects )) {
		$q->addWhere ( implode ( ' AND ', $allowedProjects ) );
	}
	
	$q->addOrder ( 'creator' );
	$q->addOrder ( 'company_name' );
	$q->addOrder ( 'project_name' );
	$q->addOrder ( 'task_log_date' );
	
	$logs = $q->loadList ();
	$rid=date('U',time());
	$source='<table class="table table-bordered table-hover" border="0" cellspacing="0" cellpadding="3"><tr><td>';
	$source.='<table border="0" cellspacing="0" cellpadding="5" width="100%">
	<tr>
	<td align="left">'.$AppUI->_ ( 'Owner' ).': '.$AppUI->user_first_name.' '.$AppUI->user_last_name.'<br>'.$AppUI->_ ( 'Report' ).' ID: '.$rid.'</td>
	<td align="right">'.$AppUI->_ ( 'Date' ).' '.date('Y-m-d H:i:s').'</td>
	</tr>
		
	<tr>
	<td align="center"><h1>'.$AppUI->_ ( 'Report' ).': '.$AppUI->_ ( $report_type . '_name' ).'</h1></td></tr>
	</table>';
	$source.='<table cellspacing="0" cellpadding="3" border="1">
	<tr>
		<th>'.$AppUI->_('Creator').'</th>
		<th>'.$AppUI->_('Company').'</th>
		<th>'.$AppUI->_('Project').'</th>
		<th>'.$AppUI->_('Task').'</th>
		<th>'.$AppUI->_('Date').'</th>
		<th>'.$AppUI->_('Billing Code').'</th>
		<th>'.$AppUI->_('Hours').'</th>
	</tr>';

	$hours = 0.00;
	$tamount = 0.00;
	$pdfdata = array ();
	
	foreach ( $logs as $log ) {
		$date = new apm_Utilities_Date ( $log ['task_log_date'] );
		$hours += $log ['task_log_hours'];
		$tamount += $log ['amount'];
		
		$pdfdata [] = array (
				$log ['creator'],
				$log ['company_name'],
				$log ['project_name'],
				$log ['task_name'],
				$date->format ( $df ),
				$log ['billingcode_name'],
				$log ['task_log_hours']  
		);
		$source.='<tr>
		<td>'.$log['creator'].'</td>
		<td>'.$log['company_name'].'</td>
		<td>'.$log['project_name'].'</td>
		<td>'.$log['task_name'].'</td>
		<td>'.$date->format($df).'</td>';
					
		$source.='<td>'.$log['billingcode_name'].'</td><td align="right">'.$log['task_log_hours'].'</td></tr>';

		}
		$pdfdata [] = array (
				'',
				'',
				'',
				'',
				'',
				$AppUI->_ ( 'Totals' ) . ':',
				$hours 
		);

	$source.='<tr><td align="right" colspan="6">'.$AppUI->_('Report Totals').':</td>
		<td align="right">'.$hours.'</td></tr></table>';

	if ($log_pdf) {
		// make the PDF file
		if ($project_id) {
			$project = new CProject ();
			$project->load ( $project_id );
			$pname = 'Project: ' . $project->project_name;
		} else {
			$pname = 'All Companies and All Projects';
		}
		
		if ($company_id) {
			$company = new CCompany ();
			$company->load ( $company_id );
			$cname = 'Company: ' . $company->company_name;
		} else {
			$cname = 'All Companies and All Projects';
		}
		
		if ($log_userfilter) {
			$q = new apm_Database_Query ();
			$q->addTable ( 'contacts' );
			$q->addQuery ( 'contact_display_name' );
			$q->addJoin ( 'users', '', 'user_contact = contact_id', 'inner' );
			$q->addWhere ( 'user_id =' . ( int ) $log_userfilter );
			$uname = 'User: ' . $q->loadResult ();
		} else {
			$uname = 'All Users';
		}

	}
	$source.='</td></tr></table>';
	
	//echo $source;
	
	require_once("./modules/reports/do_report_pdf.php");
}
else
{
	// Load the users
	$perms = &$AppUI->acl ();
	$users = $perms->getPermittedUsers ( 'reports' );
	$users[0]=$AppUI->_('All users');
	$form = new apm_Output_HTML_FormHelper ( $AppUI );
	$AppUI->getTheme ()->loadCalendarJS ();
?>
	<form name="editFrm" action="?m=<?php echo $m; ?>&report_type=<?php echo $report_type; ?>&project_id=<?php echo $project_id; ?>&suppressHeaders=true" method="post" accept-charset="utf-8">
	<input type="hidden" name="m" value="reports" /> 
	<input type="hidden" name="company_id" value=".'$company_id; ?>" /> 
	<input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
	<input type="hidden" name="report_type"	value="<?php echo $report_type; ?>" /> 
	<input type="hidden" name="datePicker" value="report" />
	<input type="hidden" name="log_all" value="1">
	<input type="hidden" name="do_report" value="1">						
	
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
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Users'); ?></td>
								<td>
								<?php echo $form->showField('log_owner', $users[$AppUI->user_id],  array(), $users); ?>
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

