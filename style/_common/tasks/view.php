<?php
$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo strlen($obj->task_name) == 0 ? $AppUI->_('New task') : $AppUI->__('Task').': '.$obj->task_name; ?>
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
							<td class="apm-label"><?php echo $AppUI->_('Project'); ?>:</td>
            <?php echo $htmlHelper->createCell('task_project', $obj->task_project); ?>
        </tr>
        <?php
								
if ($obj->task_parent != $obj->task_id) {
									$obj_parent = new CTask ();
									$obj_parent->load ( $obj->task_parent );
									?>
            <tr>
							<td class="apm-label"><?php echo $AppUI->_('Task Parent'); ?>:</td>
							<td><a
								href="<?php echo "./index.php?m=tasks&a=view&task_id=" . $obj_parent->task_id; ?>"><?php echo $obj_parent->task_name; ?></a></td>
						</tr>
        <?php } ?>
        <tr>
							<td class="apm-label"><?php echo $AppUI->_('Owner'); ?>:</td>
            <?php echo $htmlHelper->createCell('task_owner', $obj->task_owner); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Priority'); ?>:</td>
							<td>
                <?php
																$task_priotities = apmgetSysVal ( 'TaskPriority' );
																echo $AppUI->_ ( $task_priotities [$obj->task_priority] );
																?>
            </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Web Address'); ?>:</td>
            <?php echo $htmlHelper->createCell('task_related_url', $obj->task_related_url); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Milestone'); ?>:</td>
							<td width="300">
                <?php
																
if ($obj->task_milestone) {
																	echo $AppUI->_ ( 'Yes' );
																} else {
																	echo $AppUI->_ ( 'No' );
																}
																?>
            </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Progress'); ?>:</td>
							<td width="300"><?php echo ($obj->task_percent_complete) ? $obj->task_percent_complete : 0; ?>%</td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Time Worked'); ?>:</td>
            <?php echo $htmlHelper->createCell('task_hours_worked', $obj->task_hours_worked . ' ' . $AppUI->_('hours')); ?>
        </tr>
					</tbody>
				</table>
			</div>


			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Dates and Targets'); ?></td>
						</tr>
					</thead>

					<tbody>

						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Start Date'); ?>:</td>
            <?php echo $htmlHelper->createCell('task_start_datetime', $obj->task_start_date); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Finish Date'); ?>:</td>
            <?php echo $htmlHelper->createCell('task_end_datetime', $obj->task_end_date); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Expected Duration'); ?>:</td>
							<td align="left"><?php echo $obj->task_duration . ' ' . $AppUI->_($durnTypes[$obj->task_duration_type]); ?></td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Task Type'); ?> :</td>
            <?php echo $htmlHelper->createCell('task_type', $AppUI->_($task_types[$obj->task_type])); ?>
        </tr>
					</tbody>
				</table>
			</div>       
        
        
        <?php if (apmgetConfig('budget_info_display', false)) { ?>
   			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Target Budgets'); ?></td>
						</tr>
					</thead>

					<tbody>    
                                   <?php
									$totalBudget = 0;
									foreach ( $billingCategory as $id => $category ) {
										$amount = $obj->budget [$id] ['budget_amount'];
										$totalBudget += $amount;
										?>
                                        <tr>
							<td class="apm-label">
                                                <?php echo $AppUI->_($category); ?>
                                            </td>
							<td nowrap="nowrap"
								style="text-align: right; padding-left: 40px;">
                                                <?php echo formatCurrency($amount, $AppUI->getPref('CURRENCYFORM')); ?>
                                                &nbsp;<?php echo $apmconfig['currency_symbol'] ?>                                                
                                            </td>
						</tr>
                                    <?php
									}
									?>
						<tr>
							<td class="apm-label">
                                            <?php echo $AppUI->_('Total Budget'); ?>
                                        </td>
							<td nowrap="nowrap"
								style="text-align: right; padding-left: 40px;">
                                            <?php echo formatCurrency($totalBudget, $AppUI->getPref('CURRENCYFORM')); ?>
                                            &nbsp;<?php echo $apmconfig['currency_symbol'] ?>                                            
                                        </td>
						</tr>
					</tbody>
				</table>
			</div>


			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Actual Costs'); ?></td>
						</tr>
					</thead>

					<tbody>    
                                   <?php
									$bcode = new CSystem_Bcode ();
									$results = $bcode->calculateTaskCost ( $task_id );
									foreach ( $billingCategory as $id => $category ) {
										?>
                                        <tr>
							<td class="apm-label">
                                                <?php echo $AppUI->_($category); ?>
                                            </td>
							<td nowrap="nowrap"
								style="text-align: right; padding-left: 40px;">
                                                <?php
										$amount = 0;
										if (isset ( $results [$id] )) {
											$amount = $results [$id];
										}
										echo formatCurrency ( $amount, $AppUI->getPref ( 'CURRENCYFORM' ) );
										?>
                                                &nbsp;<?php echo $apmconfig['currency_symbol']?>
                                            </td>
						</tr>
                                    <?php
									}
									?>
                                    <tr>
							<td class="apm-label">
                                            <?php echo $AppUI->_('Unidentified Costs'); ?>
                                        </td>
							<td nowrap="nowrap"
								style="text-align: right; padding-left: 40px;">
                                            <?php
									$otherCosts = 0;
									if (isset ( $results ['otherCosts'] )) {
										$otherCosts = $results ['otherCosts'];
									}
									echo formatCurrency ( $otherCosts, $AppUI->getPref ( 'CURRENCYFORM' ) );
									?>
                                            &nbsp;<?php echo $apmconfig['currency_symbol']?>
                                        </td>
						</tr>
						<tr>
							<td class="apm-label">
                                            <?php echo $AppUI->_('Total Cost'); ?>
                                        </td>
							<td nowrap="nowrap"
								style="text-align: right; padding-left: 40px;">
                                            <?php
									$totalCosts = 0;
									if (isset ( $results ['totalCosts'] )) {
										$totalCosts = $results ['totalCosts'];
									}
									echo formatCurrency ( $totalCosts, $AppUI->getPref ( 'CURRENCYFORM' ) );
									?>
                                            &nbsp;<?php echo $apmconfig['currency_symbol']?>
                                        </td>
						</tr>
					</tbody>
				</table>
			</div>       
 
 
 
 
 
 
 
        

                        <?php if (isset($results['uncountedHours']) && $results['uncountedHours']) { ?>
                            <tr>
				<td colspan="2" align="center">
                                    <?php echo '<span style="float:right; font-style: italic;">'.$results['uncountedHours'].' hours without billing codes</span>'; ?>
                                </td>
			</tr>
                        <?php } ?>

        <?php } ?>
</div>

		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Assigned Users'); ?></td>
						</tr>
					</thead>

					<tbody>
					
					        <?php
													$s = count ( $users ) == 0 ? '<tr><td bgcolor="#ffffff">' . $AppUI->_ ( 'none' ) . '</td></tr>' : '';
													foreach ( $users as $row ) {
														$htmlHelper->stageRowData ( $row );
														$s .= '<tr>';
														$s .= $htmlHelper->createCell ( 'user_name', $row ['contact_display_name'] );
														$s .= $htmlHelper->createCell ( 'perc_assignment', $row ['perc_assignment'] );
														$s .= '</tr>';
													}
													echo $s;
													?>
					</tbody>
				</table>
			</div>









			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="4"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Dependencies'); ?></td>
						</tr>
					</thead>

					<tbody>
					<?php
					$taskDep = $obj->getDependencyList ( $task_id );
					$s = count ( $taskDep ) == 0 ? '<tr><td>' . $AppUI->_ ( 'none' ) . '</td></tr>' : '<tr><th>' . $AppUI->_ ( 'Task' ) . '</th>' . '<th>' . $AppUI->_ ( 'Work' ) . '</th>' . '<th>' . $AppUI->_ ( 'Start Date' ) . '</th>' . '<th>' . $AppUI->_ ( 'End Date' ) . '</th></tr>';
					foreach ( $taskDep as $key => $array ) {
						$htmlHelper->stageRowData ( $array );
						$s .= '<tr>';
						$s .= $htmlHelper->createCell ( 'task_name', $array ['task_name'] );
						$s .= $htmlHelper->createCell ( 'task_percent_complete', $array ['task_percent_complete'] );
						$s .= $htmlHelper->createCell ( 'task_start_date', $array ['task_start_date'] );
						$s .= $htmlHelper->createCell ( 'task_end_date', $array ['task_end_date'] );
						$s .= '</tr>';
					}
					echo $s;
					?>
					</tbody>
				</table>
			</div>







			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="4"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Tasks depending on this Task'); ?></td>
						</tr>
					</thead>

					<tbody>
					        <?php
													$dependingTasks = $obj->getDependentTaskList ( $task_id );
													$s = count ( $dependingTasks ) == 0 ? '<tr><td>' . $AppUI->_ ( 'none' ) . '</td></tr>' : '<tr><th>' . $AppUI->_ ( 'Task' ) . '</th>' . '<th>' . $AppUI->_ ( 'Work' ) . '</th>' . '<th>' . $AppUI->_ ( 'Start Date' ) . '</th>' . '<th>' . $AppUI->_ ( 'End Date' ) . '</th></tr>';
													foreach ( $dependingTasks as $key => $array ) {
														$htmlHelper->stageRowData ( $array );
														$s .= '<tr>';
														$s .= $htmlHelper->createCell ( 'task_name', $array ['task_name'] );
														$s .= $htmlHelper->createCell ( 'task_percent_complete', $array ['task_percent_complete'] );
														$s .= $htmlHelper->createCell ( 'task_start_date', $array ['task_start_date'] );
														$s .= $htmlHelper->createCell ( 'task_end_date', $array ['task_end_date'] );
														$s .= '</tr>';
													}
													echo $s;
													?>
					</tbody>
				</table>
			</div>




			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="1"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Description'); ?></td>
						</tr>
					</thead>

					<tbody>
						<tr>
            <?php echo $htmlHelper->createCell('task_description', $obj->task_description); ?>
        </tr>

					</tbody>
				</table>
			</div>

        <?php
								$depts = $obj->getTaskDepartments ( null, $task_id );
								if (count ( $depts )) {
									?>					
								<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="3"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Departments'); ?></td>
						</tr>
					</thead>

					<tbody>

                    <?php
					foreach ( $depts as $dept_id => $dept_info ) {
					echo '<tr>';
					echo '<td><a href="?m=departments&a=view&dept_id=' . $dept_id . '">' . $dept_info ['dept_name'] . '</a></td>';
					echo '<td>'.$AppUI->_('Ph.');
					echo ($dept_info ['dept_phone'] != '') ? ' '. $dept_info ['dept_phone']:' -';
					echo '</td></tr>';
					}
					?>

					</tbody>
				</table>
			</div>
     <?php
								
}
								
								$contacts = $obj->getContacts ( null, $task_id );
								
								$project = new CProject ();
								$project->project_id = $obj->task_project;
								$pcontacts = $project->getContactList ();
								if (count ( $contacts ) || count ( $pcontacts )) {
									?>
						
	<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="3"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Contacts'); ?></td>
						</tr>
					</thead>

					<tbody>
		<?php
									if (count ( $contacts )) {
										echo '<tr><td colspan="3"><strong>' . $AppUI->_ ( 'Task Contacts' ) . '</strong></td></tr>';
										echo '<tr><td colspan="3">';
										echo $htmlHelper->renderContactTable ( 'tasks', $contacts );
										echo '</td></tr>';
									}
									
									if (count ( $pcontacts )) {
										echo '<tr><td colspan="3"><strong>' . $AppUI->_ ( 'Project Contacts' ) . '</strong></td></tr>';
										echo '<tr><td colspan="3">';
										echo $htmlHelper->renderContactTable ( 'projects', $pcontacts );
										echo '</td></tr>';
									}
									?>					
	</tbody>
				</table>
			</div>
		<?php } ?>			

        <tr>
				<td colspan="3">
                <?php
																$custom_fields = new apm_Core_CustomFields ( $m, $a, $obj->task_id, 'view' );
																$custom_fields->printHTML ();
																?>
            </td>
			</tr>
			</table>
		</div>
		<!-- second col -->
	</div>
	<!-- panel-body -->
</div>