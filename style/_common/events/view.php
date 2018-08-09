<?php
$view = new apm_Output_HTMLHelper ( $AppUI );
?>

<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo $event->event_name; ?>
    </div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">



			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
						</tr>
					</thead>
					<tbody>

						<tr>
							<td class="apm-label"><?php $view->showLabel('Event name'); ?></td>
            <?php echo $view->createCell('event_name', $event->event_name); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php $view->showLabel('Type'); ?></td>
            <?php echo $view->createCell('event_type', $types[$event->event_type]); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php $view->showLabel('Project'); ?></td>
            <?php echo $view->createCell('event_project', $event->event_project); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php $view->showLabel('Starts'); ?></td>
            <?php echo $view->createCell('event_start_datetime', $event->event_start_date); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php $view->showLabel('Ends'); ?></td>
            <?php echo $view->createCell('event_end_datetime', $event->event_end_date); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php $view->showLabel('Recurs'); ?></td>
            <?php echo $view->createCell('event_recurs', $recurs[$event->event_recurs]); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php $view->showLabel('Attendees'); ?></td>
							<td><?php
							if (is_array ( $assigned )) {
								$start = false;
								foreach ( $assigned as $user ) {
									if ($start)
										echo '<br/>';
									else
										$start = true;
									echo $user;
								}
							}
							?></td>
						</tr>

                <?php
																$custom_fields = new apm_Core_CustomFields ( $m, $a, $event->event_id, 'view' );
																if ($custom - fields) {
																	?>
        	<tr>
							<td colspan="2"><?php
																	$custom_fields->printHTML ();
																	?>
        	</td>
						</tr><?php
																}
																?>

		</tbody>
				</table>
			</div>

			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Description'); ?></td>
						</tr>
					</thead>
					<tbody>
            			<?php echo $view->createCell('event_description', $event->event_description); ?>					
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>