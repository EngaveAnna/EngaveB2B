<?php
$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo $AppUI->__('Project').': '; ?> <?php echo strlen($project->project_name) && strlen($project->project_name) == 0 ? $AppUI->_('New') : $project->project_name; ?>
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
							<td class="apm-label"><?php echo $AppUI->_('Project name'); ?>:</td>    
        <?php echo $htmlHelper->createCell('project_name', $project->project_name); ?>
	</tr>


						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Company'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_company', $project->project_company); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Short Name'); ?>:</td>
            <?php
												
												// TODO Need to rename field to avoid confusing HTMLhelper
												echo $htmlHelper->createCell ( 'project_shortname', $project->project_short_name );
												?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Start Date'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_start_date', $project->project_start_date); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Target End Date'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_end_date', $project->project_end_date); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Actual End Date'); ?>:</td>
							<td>
                <?php
																if ($project_id) {
																	echo $actual_end_date ? '<a href="?m=tasks&a=view&task_id=' . $criticalTasks [0] ['task_id'] . '">' : '';
																	echo $actual_end_date ? '<span ' . $style . '>' . $actual_end_date->format ( $df ) . '</span>' : '-';
																	echo $actual_end_date ? '</a>' : '';
																} else {
																	echo $AppUI->_ ( 'Dynamically calculated' );
																}
																?>
            </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Project Owner'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_owner', $project->project_owner); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('URL'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_url', $project->project_url); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Staging URL'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_demo_url', $project->project_demo_url); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Project Location'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_location', $project->project_location); ?>
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
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Description'); ?></td>
						</tr>
					</thead>

					<tbody>
						<tr>
              <?php echo $htmlHelper->createCell('project_description', $project->project_description); ?>
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
				$amount = 0;
				if (isset ( $project->budget [$id] )) {
					$amount = $project->budget [$id] ['budget_amount'];
				}
				$totalBudget += $amount;
				?>
                <tr>
							<td class="apm-label"><?php echo $AppUI->_($category); ?></td>
							<td nowrap="nowrap"	style="text-align: right; padding-left: 40px;">
                            <?php echo formatCurrency($amount, $AppUI->getPref('CURRENCYFORM')); ?>&nbsp;<?php echo $apmconfig['currency_symbol']?>
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
                                                &nbsp;<?php echo $apmconfig['currency_symbol']?>
                                            </td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span>
							<?php echo $AppUI->_('Actual Costs'); ?>
							</td>
						</tr>
					</thead>

					<tbody>	
                                        <?php
			$bcode = new CSystem_Bcode ();
			$results = $bcode->calculateProjectCost ( $project_id );
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
				?>&nbsp;<?php echo $apmconfig['currency_symbol']?>
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
			?>&nbsp;<?php echo $apmconfig['currency_symbol'];?>
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
			?>&nbsp;<?php echo $apmconfig['currency_symbol'];?>
                                            </td>
						</tr>
					</tbody>
					</tbody>
				</table>

			</div>


		</div>
		<!-- End left col -->
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Summary'); ?></td>
						</tr>
					</thead>

					<tbody>

						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Status'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_status', $AppUI->_($pstatus[$project->project_status])); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Type'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_type', $AppUI->_($ptype[$project->project_type])); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Priority'); ?>:</td>
							<td width="100%"><?php echo $AppUI->_($projectPriority[$project->project_priority]); ?></td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Progress'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_percent_complete', $project->project_percent_complete); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Active'); ?>:</td>
							<td width="100%"><?php echo $project->project_active ? $AppUI->_('Yes') : $AppUI->_('No'); ?></td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Scheduled Hours'); ?>:</td>
            <?php echo $htmlHelper->createCell('total_hours', $project->project_scheduled_hours); ?>
        </tr>
		<tr>
			<td class="apm-label"><?php echo $AppUI->_('Worked Hours'); ?>:</td>
            <?php echo $htmlHelper->createCell('project_worked_hours', $project->project_worked_hours); ?>
        </tr>

		
				
				
				<?php if (isset($results['uncountedHours']) && $results['uncountedHours']) { ?>
			<tr>
				<td class="apm-label"><?php echo $AppUI->_('hours without billing codes');?></td><td>
				<?php echo $results['uncountedHours']; ?></td>
			</tr>
			
                            <?php } ?>
					</tbody>
				</table>
			</div>                            
                            
                            
        <?php } ?>
        <?php
								$depts = $project->getDepartmentList ();
								if (count ( $depts ) > 0) {
									?>
			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Departments'); ?>
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

			</table>
			</tbody>
			</table>
			</div>
        <?php
								}
								
								$contacts = $project->getContactList ();
								if (count ( $contacts )) {?>
									<div class="table-responsive">
									<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
									<thead>
									<tr>
									<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_ ( 'Contacts' );?>
									</tr>
									</thead>
									<tbody>
									<?php 
										if (count ( $contacts )) {
										echo '<tr><td colspan="4">';
										echo $htmlHelper->renderContactTable ( 'projects', $contacts );
										echo '</td></tr>';
									}
									?>
									</tbody>
									</table>
									</div><?php 
								}
								?>

<?php
// lets add the subprojects table
$canReadMultiProjects = canView ( 'projects' );
if ($project->hasChildProjects ( $project_id ) && $canReadMultiProjects) {
	?>
    <tr>
				<td colspan="2">
            <?php
	echo apmtoolTip ( 'Multiproject', 'Click to Show/Hide Structure', true ) . '<a href="javascript: void(0);" onclick="expand_collapse(\'multiproject\', \'tblProjects\')"><img id="multiproject_expand" src="' . apmfindImage ( 'icons/expand.gif' ) . '" /><img id="multiproject_collapse" src="' . apmfindImage ( 'icons/collapse.gif' ) . '" style="display:none"></a>&nbsp;' . apmendTip ();
	echo '<strong>' . $AppUI->_ ( 'This Project is Part of the Following Multi-Project Structure' ) . ':<strong>';
	?>
        </td>
			</tr>
			<tr id="multiproject" style="visibility: collapse; display: none;">
				<td colspan="2" class="hilite">
            <?php
	require apm_BASE_DIR . '/modules/projects/vw_sub_projects.php';
	?>
        </td>
			</tr>
<?php
}
// here finishes the subproject structure
?>
</div>
	</div>
</div>