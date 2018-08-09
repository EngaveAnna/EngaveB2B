<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo remove database query

global $AppUI, $min_view, $m, $a, $user_id, $tab, $tasks, $cal_sdf;
GLOBAL $gantt_map, $currentGanttImgSource, $filter_task_list, $caller;
$AppUI->getTheme ()->loadCalendarJS ();

$min_view = defVal ( $min_view, false );

$project_id = ( int ) apmgetParam ( $_GET, 'project_id', 0 );

$project = new CProject ();
$project->load ( $project_id );

// sdate and edate passed as unix time stamps
$sdate = apmgetParam ( $_POST, 'project_start_date', 0 );
$edate = apmgetParam ( $_POST, 'project_end_date', 0 );

// if set GantChart includes user labels as captions of every GantBar
$showLabels = apmgetParam ( $_POST, 'showLabels', '0' );
$showLabels = (($showLabels != '0') ? '1' : $showLabels);

$showWork = apmgetParam ( $_POST, 'showWork', '0' );
$showWork = (($showWork != '0') ? '1' : $showWork);

$showWork_days = apmgetParam ( $_POST, 'showWork_days', '0' );
$showWork_days = (($showWork_days != '0') ? '1' : $showWork_days);

$printpdf = apmgetParam ( $_POST, 'printpdf', '0' );
$printpdf = (($printpdf != '0') ? '1' : $printpdf);

$printpdfhr = apmgetParam ( $_POST, 'printpdfhr', '0' );
$printpdfhr = (($printpdfhr != '0') ? '1' : $printpdfhr);

$showMilestonesOnly = '';
$showNoMilestones = '';
$addLinksToGantt = '';
$ganttTaskFilter = '';
$monospacefont = '';
$showTaskNameOnly = '';
$showhgrid = '';

if ($a == 'todo') {
	if (isset ( $_POST ['show_form'] )) {
		$AppUI->setState ( 'TaskDayShowArc', apmgetParam ( $_POST, 'showArcProjs', 0 ) );
		$AppUI->setState ( 'TaskDayShowLow', apmgetParam ( $_POST, 'showLowTasks', 0 ) );
		$AppUI->setState ( 'TaskDayShowHold', apmgetParam ( $_POST, 'showHoldProjs', 0 ) );
		$AppUI->setState ( 'TaskDayShowDyn', apmgetParam ( $_POST, 'showDynTasks', 0 ) );
		$AppUI->setState ( 'TaskDayShowPin', apmgetParam ( $_POST, 'showPinned', 0 ) );
	}
	
	$showArcProjs = $AppUI->getState ( 'TaskDayShowArc', 0 );
	$showLowTasks = $AppUI->getState ( 'TaskDayShowLow', 1 );
	$showHoldProjs = $AppUI->getState ( 'TaskDayShowHold', 0 );
	$showDynTasks = $AppUI->getState ( 'TaskDayShowDyn', 0 );
	$showPinned = $AppUI->getState ( 'TaskDayShowPin', 0 );
} else {
	$showPinned = apmgetParam ( $_POST, 'showPinned', '0' );
	$showPinned = (($showPinned != '0') ? '1' : $showPinned);
	
	$showArcProjs = apmgetParam ( $_POST, 'showArcProjs', '0' );
	$showArcProjs = (($showArcProjs != '0') ? '1' : $showArcProjs);
	
	$showHoldProjs = apmgetParam ( $_POST, 'showHoldProjs', '0' );
	$showHoldProjs = (($showHoldProjs != '0') ? '1' : $showHoldProjs);
	
	$showDynTasks = apmgetParam ( $_POST, 'showDynTasks', '0' );
	$showDynTasks = (($showDynTasks != '0') ? '1' : $showDynTasks);
	
	$showLowTasks = apmgetParam ( $_POST, 'showLowTasks', '0' );
	$showLowTasks = (($showLowTasks != '0') ? '1' : $showLowTasks);
}

/**
 * prepare the array with the tasks to display in the task filter
 * (for the most part this is code harvested from gantt.php)
 */
$filter_task_list = array ();
$projectObject = new CProject ();
$projects = $projectObject->getAllowedProjects ( $AppUI->user_id );

$proTasks = __extract_from_tasks_viewgantt ( $project_id, $AppUI );

$filter_task_list = array ();
$orrarr [] = array (
		'task_id' => 0,
		'order_up' => 0,
		'order' => '' 
);
foreach ( $proTasks as $row ) {
	$projects [$row ['task_project']] ['tasks'] [] = $row;
}
unset ( $proTasks );
$parents = array ();

foreach ( $projects as $p ) {
	global $parents, $task_id;
	$parents = array ();
	$tnums = 0;
	if (isset ( $p ['tasks'] )) {
		$tnums = count ( $p ['tasks'] );
	}
	for($i = 0; $i < $tnums; $i ++) {
		$t = $p ['tasks'] [$i];
		if (! (isset ( $parents [$t ['task_parent']] ))) {
			$parents [$t ['task_parent']] = false;
		}
		if ($t ['task_parent'] == $t ['task_id']) {
			showfiltertask ( $t );
			findfiltertaskchild ( $p ['tasks'], $t ['task_id'] );
		}
	}
	// Check for ophans.
	foreach ( $parents as $id => $ok ) {
		if (! ($ok)) {
			findfiltertaskchild ( $p ['tasks'], $id );
		}
	}
}
/**
 * the results of the above bits are stored in $filter_task_list (array)
 */

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
	$titleBlock->addCrumb ( '?m=tasks', 'tasks list' );
	$titleBlock->addCrumb ( '?m=projects&a=view&project_id=' . $project_id, 'view this project' );
	$titleBlock->addCrumb ( '#" onclick="javascript:toggleLayer(\'displayOptions\');', 'show/hide display options' );
	$titleBlock->show ();
}
?>
<script language="javascript" type="text/javascript">
    var calendarField = "";

    function popCalendar(field) {
         calendarField = field;
         idate = eval("document.editFrm." + field + ".value");
         window.open("index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=" + idate,
                     "calwin", "width=250, height=230, scrollbars=no, status=no"); ////chaged height from 220
    }
    /**
     *     @param string Input date in the format YYYYMMDD
     *     @param string Formatted date
     */
    function setCalendar(idate, fdate) {
         fld_date = eval("document.editFrm." + calendarField);
         fld_fdate = eval("document.editFrm.show_" + calendarField);
         fld_date.value = idate;
         fld_fdate.value = fdate;
    }

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
         document.editFrm.printpdf.value = "0";
         document.editFrm.printpdfhr.value = "0";
        f.submit();
    }

    function showThisMonth() {
        document.editFrm.display_option.value = "this_month";
        document.editFrm.printpdf.value = "0";
        document.editFrm.printpdfhr.value = "0";
        document.editFrm.submit();
    }

    function showFullProject() {
         document.editFrm.display_option.value = "all";
         document.editFrm.printpdf.value = "0";
         document.editFrm.printpdfhr.value = "0";
         document.editFrm.submit();
    }

    function toggleLayer( whichLayer ) {
         var elem, vis;
         if( document.getElementById ) // this is the way the standards work
              elem = document.getElementById( whichLayer );
         else if( document.all ) // this is the way old msie versions work
              elem = document.all[whichLayer];
         else if( document.layers ) // this is the way nn4 works
              elem = document.layers[whichLayer];
         vis = elem.style;
         // if the style.display value is blank we try to figure it out here
         if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
              vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
              vis.display = (vis.display==''||vis.display=='block')?'none':'block';
    }

    function printPDFHR() {
         document.editFrm.printpdf.value = "0";
         document.editFrm.printpdfhr.value = "1";
         document.editFrm.submit();
    }

    function submitIt() {
         document.editFrm.printpdf.value = "0";
         document.editFrm.printpdfhr.value = "0";
         document.editFrm.submit();
    }
</script>

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="clear:both;">	
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Gantt params'); ?></td>
	</tr>
	</thead>
	<tbody>	
	
	
	<!-- start of div used to show/hide formatting options -->
	<form name="editFrm" method="post"
		action="?<?php echo "m=$m&a=$a&tab=$tab&project_id=$project_id"; ?>"
		accept-charset="utf-8">
		<input type="hidden" name="display_option"
			value="<?php echo $display_option; ?>" /> <input type="hidden"
			name="printpdf" value="<?php echo $printpdf; ?>" /> <input
			type="hidden" name="printpdfhr" value="<?php echo $printpdfhr; ?>" />
		<input type="hidden" name="caller" value="<?php echo $a; ?>" /> <input
			type="hidden" name="datePicker" value="project" />

<tr>
				<td class="apm-label"><?php echo $AppUI->_('From'); ?>:</td>
				<td align="left" class="input-group date" nowrap="nowrap"><input type="hidden"
					name="project_start_date" id="project_start_date"
					value="<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
					<input type="text" name="start_date" id="start_date"
					onchange="setDate_new('editFrm', 'start_date');"
					value="<?php echo $start_date ? $start_date->format($df) : ''; ?>"
					class="form-control" /> <span class="input-group-addon"><a href="javascript: void(0);"
					onclick="return showCalendar('start_date', '<?php echo $df ?>', 'editFrm', null, true, true)">
						<i class="glyphicon glyphicon-calendar btn-default"></i>
				</a></span></td>
</tr><tr>				
				<td class="apm-label"><?php echo $AppUI->_('To'); ?>:</td>
				<td align="left" class="input-group date" nowrap="nowrap"><input type="hidden"
					name="project_end_date" id="project_end_date"
					value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
					<input type="text" name="end_date" id="end_date"
					onchange="setDate_new('editFrm', 'end_date');"
					value="<?php echo $end_date ? $end_date->format($df) : ''; ?>"
					class="form-control" /> <span class="input-group-addon"><a href="javascript: void(0);"
					onclick="return showCalendar('end_date', '<?php echo $df ?>', 'editFrm', null, true, true)">
						<i class="glyphicon glyphicon-calendar btn-default"></i>
				</a></span></td>
</tr><tr>
				<td><?php echo $AppUI->_('Show captions'); ?></td>					
				<td><input type="checkbox" name="showLabels" id="showLabels" value="1"<?php echo (($showLabels == 1) ? 'checked="checked"' : ""); ?> />
</tr>
        <?php if ($a == 'todo') { ?>
					<input type="hidden" name="show_form" value="1" />

<tr>
						<input type="hidden" name="show_form" value="1" />

									<td class="apm-label"><?php echo $AppUI->_('Pinned Only'); ?></td>
										<td valign="bottom" nowrap="nowrap"><input type="checkbox"
											name="showPinned" id="showPinned"
											<?php echo $showPinned ? 'checked="checked"' : ''; ?> />
							</td>											 
</tr><tr>
									<td class="apm-label"><?php echo $AppUI->_('Archived Projects'); ?></td>										
										<td valign="bottom" nowrap="nowrap"><input type="checkbox"
											name="showArcProjs" id="showArcProjs"
											<?php echo $showArcProjs ? 'checked="checked"' : ''; ?> />
							</td>											 
</tr><tr>								
									<td class="apm-label"><?php echo $AppUI->_('Projects on Hold'); ?></td>		
										<td valign="bottom" nowrap="nowrap"><input type="checkbox"
											name="showHoldProjs" id="showHoldProjs"
											<?php echo $showHoldProjs ? 'checked="checked"' : ''; ?> />
							</td>											
</tr><tr>								
									<td class="apm-label"><?php echo $AppUI->_('Dynamic Tasks'); ?></td>		
										<td valign="bottom" nowrap="nowrap"><input type="checkbox"
											name="showDynTasks" id="showDynTasks"
											<?php echo $showDynTasks ? 'checked="checked"' : ''; ?> />
							</td>											 
</tr><tr>
									<td class="apm-label"><?php echo $AppUI->_('Low Priority Tasks'); ?></td>										
										<td valign="bottom" nowrap="nowrap"><input type="checkbox"
											name="showLowTasks" id="showLowTasks"
											<?php echo $showLowTasks ? 'checked="checked"' : ''; ?> />
							</td>											 
									</tr>

        <?php } ?>

</tbody></table></div>	
	
	
	
<div style="padding-bottom:30px;">

<input type="button" class="btn btn-info" value="<?php echo $AppUI->_('submit'); ?>" onclick='document.editFrm.display_option.value="custom";submitIt();' style="float: left;" /> 
<?php 
echo '<button class="btn btn-default" onclick="javascript:showThisMonth()">' . $AppUI->_('show this month') . '</button>  <button class="btn btn-default" onclick="javascript:showFullProject()">' . ($a == 'todo' ? $AppUI->_('show all') : $AppUI->_('show full project')) . '</button>'; ?>
</div>	
	
	</form>
<!-- end of div used to show/hide formatting options -->
</div>


	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="clear:both;">	
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="8">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Markers'); ?></td>
	</tr>
	</thead>
	<tbody>	

	<?php if ($showMilestonesOnly != 1) { ?>
	<tr>
		<td align="right"><?php echo $AppUI->_('Dynamic Task')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/task_dynamic.png"
			alt="" /></td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Task (planned)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/task_planned.png"
			alt="" /></td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Task (in progress)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/task_in_progress.png"
			alt="" /></td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Task (completed)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/task_completed.png"
			alt="" /></td>
	</tr>
	<?php } ?>
	<?php if ($showNoMilestones != 1) {	?>
	<tr>
		<td align="right"><?php echo $AppUI->_('Milestone (planned)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/milestone_planned.png"
			alt="" /></td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Milestone (completed)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/milestone_completed.png"
			alt="" /></td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Milestone (in progress)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/milestone_in_progress.png"
			alt="" /></td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Milestone (overdue)')?>&nbsp;</td>
		<td align="center"><img
			src="<?php echo apm_BASE_URL;?>/modules/tasks/images/milestone_overdue.png"
			alt="" /></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
</div>
</div>

<div style="clear:both; margin:15px 0; padding-top:20px;">
            <?php
												if ($a != 'todo') {
													$cnt [0] ['N'] = $project->project_task_count;
												} else {
													$cnt [0] ['N'] = ((empty ( $tasks )) ? 0 : 1);
												}
												if ($cnt [0] ['N'] > 0) {
													$src = ('?m=tasks&a=gantt&suppressHeaders=1&project_id=' . $project_id . (($display_option == 'all') ? '' : ('&start_date=' . $start_date->format ( '%Y-%m-%d' ) . '&end_date=' . $end_date->format ( '%Y-%m-%d' ))) . "&width=' + ((navigator.appName=='Netscape'" . "?window.innerWidth:document.body.offsetWidth)*0.95) + '" . '&showLabels=' . $showLabels . '&showWork=' . $showWork . '&showTaskNameOnly=' . $showTaskNameOnly . '&showhgrid=' . $showhgrid . '&showPinned=' . $showPinned . '&showArcProjs=' . $showArcProjs . '&showHoldProjs=' . $showHoldProjs . '&showDynTasks=' . $showDynTasks . '&showLowTasks=' . $showLowTasks . '&caller=' . $a . '&user_id=' . $user_id . '&printpdf=' . $printpdf . '&showNoMilestones=' . $showNoMilestones . '&showMilestonesOnly=' . $showMilestonesOnly . '&addLinksToGantt=' . $addLinksToGantt . '&ganttTaskFilter=' . $ganttTaskFilter . '&monospacefont=' . $monospacefont . '&showWork_days=' . $showWork_days);
													
													?>
                <script language="javascript" type="text/javascript"> document.write('<img alt="Please wait while the Gantt chart is generated... (this might take a minute or two)" src="<?php echo htmlspecialchars($src); ?>" />') </script>
                <?php
													
													// If we have a problem displaying this we need to display a warning.
													// Put it at the bottom just in case
													if (! apmcheckMem ( 32 * 1024 * 1024 )) {
														echo "</td>\n</tr>\n<tr>\n<td>";
														echo '<span style="color: red; font-weight: bold;">' . $AppUI->_ ( 'invalid memory config' ) . '</span>';
														echo "\n";
													}
												} else {
													echo $AppUI->_ ( 'No tasks to display' );
												}
												?>
</div>
<div style="clear:both; margin:15px 0; padding-top:20px;">
			<?php
			// POST of all necesary variables to generate gantt in PDF
			$_POST ['m'] = 'tasks';
			$_POST ['a'] = 'gantt_pdf';
			$_POST ['suppressHeaders'] = '1';
			$_POST ['start_date'] = $start_date->format ( '%Y-%m-%d' );
			$_POST ['end_date'] = $end_date->format ( '%Y-%m-%d' );
			$_POST ['display_option'] = $display_option;
			$_POST ['showLabels'] = $showLabels;
			$_POST ['showWork'] = $showWork;
			$_POST ['showTaskNameOnly'] = $showTaskNameOnly;
			$_POST ['showhgrid'] = $showhgrid;
			$_POST ['showPinned'] = $showPinned;
			$_POST ['showArcProjs'] = $showArcProjs;
			$_POST ['showHoldProjs'] = $showHoldProjs;
			$_POST ['showDynTasks'] = $showDynTasks;
			$_POST ['showLowTasks'] = $showLowTasks;
			$_POST ['caller'] = $a;
			$_POST ['user_id'] = $user_id;
			$_POST ['printpdfhr'] = $printpdfhr;
			$_POST ['showPinned'] = $showPinned;
			$_POST ['showArcProjs'] = $showArcProjs;
			$_POST ['showHoldProjs'] = $showHoldProjs;
			$_POST ['showDynTasks'] = $showDynTasks;
			$_POST ['showLowTasks'] = $showLowTasks;
			
			if ($printpdf == 1 || $printpdfhr == 1) {
				include 'gantt_pdf.php';
				$_POST ['printpdf'] = 0;
				$printpdf = 0;
				$_POST ['printpdfhr'] = 0;
				$printpdfhr = 0;
			}
			?>
</div>
