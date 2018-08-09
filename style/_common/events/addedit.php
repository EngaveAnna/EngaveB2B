<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="addedit events">
	<input type="hidden" name="dosql" value="do_event_aed" /> <input
		type="hidden" name="event_id" value="<?php echo $object->getId(); ?>" />
	<input type="hidden" name="event_assigned" value="" /> <input
		type="hidden" name="datePicker" value="event" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
		<div class="panel-heading">
    <?php echo strlen($object->company_name) == 0 ? $AppUI->__('New event') : $AppUI->__('Event').': '.$object->event_name; ?>
    </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
	</tr>
	</thead>
	<tbody>	
				
			<tr><td class="apm-label"><?php echo $AppUI->_('Event name'); ?></td><td>
                <?php $form->showField('event_name', $object->event_name, array('maxlength' => 255)); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Type'); ?></td><td>
                <?php $form->showField('event_type', $object->event_type, array(), $types); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Project'); ?></td><td>
                <?php $form->showField('event_project', $object->event_project, array(), $projects); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Event Owner'); ?></td><td>
                <?php
																$owner = ($object->event_owner) ? $object->event_owner : $AppUI->user_id;
																$form->showField ( 'event_owner', $owner, array (), $users );
																?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Private Entry'); ?></td><td>
                <input type="checkbox" value="1" name="event_private"
						id="event_private"
						<?php echo ($object->event_private ? 'checked="checked"' : ''); ?> />
				</p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Start Date'); ?></td><td class="input-group date">
                <input type="hidden" name="event_start_date"
						id="event_start_date"
						value="<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
					<input type="text" name="start_date" id="start_date"
						onchange="setDate_new('editFrm', 'start_date');"
						value="<?php echo $start_date ? $start_date->format($df) : ''; ?>"
						class="form-control" /> <span class="input-group-addon"> <a href="javascript: void(0);"
						onclick="return showCalendar('start_date', '<?php echo $df ?>', 'editFrm', null, true, true)">
						<i class="glyphicon glyphicon-calendar btn-default"></i>
					</a></span>
                <?php echo arraySelect($times, 'start_time', 'size="1" class="form-control"', $AppUI->formatTZAwareTime($object->event_start_date, '%H%M%S')); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('End Date'); ?></td><td class="input-group date">
                <input type="hidden" name="event_end_date"
						id="event_end_date"
						value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
					<input type="text" name="end_date" id="end_date"
						onchange="setDate_new('editFrm', 'end_date');"
						value="<?php echo $end_date ? $end_date->format($df) : ''; ?>"
						class="form-control" /> <span class="input-group-addon"><a href="javascript: void(0);"
						onclick="return showCalendar('end_date', '<?php echo $df ?>', 'editFrm', null, true, true)">
						<i class="glyphicon glyphicon-calendar btn-default"></i>
					
					</a></span>
                <?php echo arraySelect($times, 'end_time', 'size="1" class="form-control"', $AppUI->formatTZAwareTime($object->event_end_date, '%H%M%S')); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Recurs'); ?></td><td>
                <?php echo arraySelect($recurs, 'event_recurs', 'size="1" class="form-control"', $object->event_recurs, true); ?>
                <input type="text" class="form-control"
						name="event_times_recuring"
						value="<?php echo ((isset($object->event_times_recuring)) ? ($object->event_times_recuring) : '1'); ?>"
						maxlength="2" size="3" /> <?php echo $AppUI->_('times'); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Description'); ?></td><td>
                <?php $form->showField('event_description', $object->event_description); ?>
            </p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Only on Working Days'); ?></td><td>
                <input type="checkbox" value="1" name="event_cwd"
						id="event_cwd"
						<?php echo ($object->event_cwd ? 'checked="checked"' : ''); ?> />
				</p>
			<tr><td class="apm-label"><?php echo $AppUI->_('Mail Attendees'); ?></td><td>
                <input type="checkbox" name="mail_invited"
						id="mail_invited" checked="checked" />
				</p>
			<tr><td colspan="2" class="apm-label">
				
				
				<table>
					<tr>
						<td align="right"><?php echo $AppUI->_('People'); ?>:</td>
						<td></td>
						<td align="left"><?php echo $AppUI->_('Invited to Event'); ?>:</td>
						<td></td>
					</tr>
					<tr>
						<td width="50%" colspan="2" align="right">
                    <?php echo arraySelect($users, 'resources', 'style="width:220px" size="10" class="form-control" multiple="multiple" ', null); ?>
                </td>
						<td width="50%" colspan="2" align="left">
                    <?php echo arraySelect($assigned, 'assigned', 'style="width:220px" size="10" class="form-control" multiple="multiple" ', null); ?>
                </td>
					</tr>
					<tr>
						<td width="50%" colspan="2" align="right"><input type="button"
							class="btn btn-info" value="&gt;" onclick="addUser()" /></td>
						<td width="50%" colspan="2" align="left"><input type="button"
							class="btn btn-info" value="&lt;" onclick="removeUser()" /></td>
					</tr>
					<tr>
						<td colspan="2" align="right">
                    <?php
																				$custom_fields = new apm_Core_CustomFields ( 'events', 'addedit', $object->event_id, 'edit' );
																				$custom_fields->printHTML ();
					?>
                </td>
				<tr>
				</table>
				</td></tr>

						</tbody>
					</table>
				</div>
				<div><?php $form->showCancelButton(); ?><?php $form->showSaveButton(); ?>
	</div>
			</div>			<!-- panel-body-->
		</div>		<!-- panel-default -->


</form>