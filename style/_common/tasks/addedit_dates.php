<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	<input name="dosql" type="hidden" value="do_task_aed" /> <input
		name="task_id" type="hidden" value="<?php echo $object->getId(); ?>" />
	<input type="hidden" name="datePicker" value="task" />

	
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Dates'); ?></td>
	</tr>
	</thead>
	<tbody>	
	
	
	

	<?php if ($can_edit_time_information) { ?>
	<input type='hidden' id='task_start_date' name='task_start_date' value='<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : ''; ?>' />
	<tr><td class="apm-label"><?php echo $AppUI->_('Start Date'); ?></td><td class="input-group date">
	<input type='text' onchange="setDate_new('editFrm', 'start_date');" class='form-control' id='start_date' name='start_date' value='<?php echo $start_date ? $start_date->format($df) : ''; ?>' />
		<span class="input-group-addon"><a onclick="return showCalendar('start_date', '<?php echo $df ?>', 'editFrm', null, true, true)" href="javascript: void(0);"><i class="glyphicon glyphicon-calendar btn-default"></i>
				</a></span></td></tr>
				<tr><td class="apm-label"><?php echo $AppUI->_('Start Time'); ?></td><td>
                    <?php
													echo arraySelect ( $hours, 'start_hour', 'size="1" onchange="setAMPM(this)" class="form-control"', $start_date ? $start_date->getHour () : $start );
													echo arraySelect ( $minutes, 'start_minute', 'size="1" class="form-control"', $start_date ? $start_date->getMinute () : '00' );
													if (stristr ( $AppUI->getPref ( 'TIMEFORMAT' ), '%p' )) {
														echo '<input type="text" name="start_hour_ampm" id="start_hour_ampm" value="' . ($start_date ? $start_date->getAMPM () : ($start > 11 ? 'pm' : 'am')) . '" disabled="disabled" class="form-control" size="2" />';
													}
													
													?>
                </td></tr>
                <input type='hidden' id='task_end_date'	name='task_end_date' value='<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : ''; ?>' />
				<tr><td class="apm-label"><?php echo $AppUI->_('Finish Date'); ?></td><td class="input-group date">
                    
				<input type='text' onchange="setDate_new('editFrm', 'end_date');" class='form-control' id='end_date' name='end_date' value='<?php echo $end_date ? $end_date->format($df) : ''; ?>' />
				<span class="input-group-addon"> <a onclick="return showCalendar('end_date', '<?php echo $df ?>', 'editFrm', null, true, true)"	href="javascript: void(0);"> <i class="glyphicon glyphicon-calendar btn-default"></i>
				</a></span></td></tr>
				<tr><td class="apm-label"><?php echo $AppUI->_('Finish Time'); ?></td><td>
				<?php echo arraySelect ( $hours, 'end_hour', 'size="1" onchange="setAMPM(this)" class="form-control"', $end_date ? $end_date->getHour () : $end );
				echo arraySelect ( $minutes, 'end_minute', 'size="1" class="form-control"', $end_date ? $end_date->getMinute () : '00' );
				if (stristr ( $AppUI->getPref ( 'TIMEFORMAT' ), '%p' )) {
				echo '<input type="text" name="end_hour_ampm" id="end_hour_ampm" value="' . ($end_date ? $end_date->getAMPM () : ($end > 11 ? 'pm' : 'am')) . '" disabled="disabled" class="form-control" size="2" />';
				}?>
                </td></tr>
                
				<tr><td class="apm-label"><?php echo $AppUI->_('Calculate'); ?></td><td>
				<input type="button" value="<?php echo $AppUI->_('Duration'); ?>" onclick="xajax_calcDuration(document.editFrm.task_start_date.value,document.editFrm.start_hour.value,document.editFrm.start_minute.value,document.editFrm.task_end_date.value,document.editFrm.end_hour.value,document.editFrm.end_minute.value,document.editFrm.task_duration_type.value);" class="btn btn-default btn" /> <input type="button" value="<?php echo $AppUI->_('Finish Date'); ?>"	onclick="xajax_calcFinish(document.editFrm.task_start_date.value,document.editFrm.start_hour.value,document.editFrm.start_minute.value,document.editFrm.task_duration_type.value,document.editFrm.task_duration.value)"	class="btn btn-default btn" />
    </td></tr>
            <?php } ?>

	<tr><td class="apm-label"><?php echo $AppUI->_('Expected Duration'); ?></td><td>
                <input type="text" class="form-control"
					name="task_duration" id="task_duration" maxlength="8" size="6"
					value="<?php echo $object->task_duration; ?>" />
                <?php
																echo arraySelect ( $durnTypes, 'task_duration_type', 'class="form-control"', $object->task_duration_type, true );
																?>
    </td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Daily Working Hours'); ?></td><td>
                <?php echo $apmconfig['daily_working_hours']; ?>
    </td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Working Days'); ?></td><td>
                <?php echo $cwd_hr; ?>
    </td></tr>
		</tbody>
	</table>
	</div>
