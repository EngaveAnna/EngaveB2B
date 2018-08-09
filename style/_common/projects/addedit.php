<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post" accept-charset="utf-8" class="addedit projects">
	<input type="hidden" name="dosql" value="do_project_aed" />
	<input type="hidden" name="project_id" value="<?php echo $object->getId(); ?>" /> 
	<input type="hidden" name="project_creator" value="<?php echo is_null($object->project_creator) ? $AppUI->user_id : $object->project_creator; ?>" />
 	<input type="hidden" name="datePicker" value="project" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
		<div class="panel-heading">
    <?php echo strlen($object->project_name) == 0 ? $AppUI->__('New Project') : $AppUI->__('Project').': '.$object->project_name; ?>
    </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Name'); ?></td>
								<td>
									<?php
									$options = array ();
									$options ['maxlength'] = 255;
									$options ['onBlur'] = 'setShort()';
									$form->showField ( 'project_name', $object->project_name, $options );?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Parent Project'); ?></td>
								<td>
                <?php echo arraySelect($structprojects, 'project_parent', 'size="1" class="form-control"', $object->project_parent ? $object->project_parent : 0)?>
			</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Company'); ?></td>
								<td>
                <?php echo arraySelect($companies, 'project_company', 'class="form-control" size="1"', $object->project_company); ?></td>
							</tr>
	            <?php
												if ($AppUI->isActiveModule ( 'departments' ) && canAccess ( 'departments' )) {
													// Build display list for departments
													$company_id = $object->project_company;
													$selected_departments = array ();
													if ($object_id) {
														$myDepartments = $object->getDepartmentList ();
														$selected_departments = (count ( $myDepartments ) > 0) ? array_keys ( $myDepartments ) : array ();
													}
													$departments_count = 0;
													$department_selection_list = getDepartmentSelectionList ( $company_id, $selected_departments );
													if ($department_selection_list != '' || $object_id) {
														$department_selection_list = '<tr><td>' . $form->addLabel ( 'Departments' ) . '</td><td> <select name="project_departments[]" multiple="multiple" class="form-control"><option value="0"></option>' . $department_selection_list . '</select></td></tr>';
													} else {
														$department_selection_list = '<tr><td>' . $form->addLabel ( 'Departments' ) . '</td><td><input type="button" class="btn btn-default" value="' . $AppUI->_ ( 'Select department...' ) . '" onclick="javascript:popDepartment();" /><input type="hidden" name="project_departments"</td></tr>';
													}
													// Let's check if the actual company has departments registered
													if ($department_selection_list != '') {
														echo $department_selection_list;
													}
												}
												?>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project Owner'); ?></td>
								<td>
               					<?php	// pull users
								$perms = &$AppUI->acl ();
								$users = $perms->getPermittedUsers ( 'projects' );
								?>
                				<?php $form->showField('project_owner', $object->project_owner, array(), $users); ?>
                				</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Contacts'); ?></td>
								<td>
								<input type="hidden" name="project_contacts" id="project_contacts" value="<?php echo $project_contacts; ?>" />
								<?php
								$form->addAjaxModal($modId, $AppUI->_('Select contacts')); 
								$btnModal1_Onclk ='javascript:getModalAjaxData(\''.$modId.'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=contact_selector&dialog=1&call_back=setContacts&selected_contacts_id=\'+getModalAjaxProcVar(\''.$procVar.'\',false)+\'&company_id='.$company_id.'&modId='.$modId.'&procVar='.$procVar.'\')';
								$btnModal1 = '<a type="button" class="btn-info btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select contacts').'" onClick="'.$btnModal1_Onclk.'">
								<span data-toggle="modal" data-target="#'.$modId.'">'.$AppUI->_('Select contacts').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal1; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId.'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxListContacts); ?>
								</span>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Start Date'); ?></td>
								<td class="input-group date">
                				<?php $form->showField('project_start_date', $object->project_start_date); ?>
                				</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Target Finish Date'); ?></td>
								<td class="input-group date">
                				<?php $form->showField('project_end_date', $object->project_end_date); ?>
                				</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Actual Finish Date'); ?></td>
								<td>
                				<?php
																if ($object_id) {
																	echo $actual_end_date ? '<a href="?m=tasks&a=view&task_id=' . $criticalTasks [0] ['task_id'] . '">' : '';
																	echo $actual_end_date ? '<span ' . $style . '>' . $actual_end_date->format ( $df ) . '</span>' : '-';
																	echo $actual_end_date ? '</a>' : '';
																} else {
																	echo $AppUI->_ ( 'Dynamically calculated' );
																}
																?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project Location'); ?></td>
								<td>
                <?php $form->showField('project_location', $object->project_location, array('maxlength' => 50)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Priority'); ?></td>
								<td>
                <?php $form->showField('project_priority', (int) $object->project_priority, array(), $projectPriority); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Short Name'); ?></td>
								<td>
                <?php $form->showField('project_short_name', $object->project_short_name, array('maxlength' => 10)); ?></td>
							</tr>

							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project Type'); ?></td>
								<td>
                <?php $form->showField('project_type', (int) $object->project_type, array(), $ptype); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Status'); ?> *</td>
								<td><?php $form->showField('project_status', $object->project_status, array(), $pstatus); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Progress'); ?></td>
								<td><?php echo sprintf("%.1f%%", $object->project_percent_complete); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Active'); ?> ?</td>
								<td><input type="checkbox" value="1" name="project_active"
									<?php echo $object->project_active || $object_id == 0 ? 'checked="checked"' : ''; ?> /></td>
							</tr>


							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Import tasks from'); ?></td>
								<td>
                <?php
																$templates = $object->loadAll ( 'project_name', 'project_status = ' . apmgetConfig ( 'template_projects_status_id' ) );
																$templateProjects [] = '';
																foreach ( $templates as $key => $data ) {
																	$templateProjects [$key] = $data ['project_name'];
																}
																echo arraySelect ( $templateProjects, 'import_tasks_from', 'size="1" class="form-control"', - 1, false );
																?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                <?php $form->showField('project_description', $object->project_description); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Notify by Email'); ?></td>
								<td><input type="checkbox" name="email_project_owner_box"
									id="email_project_owner_box"
									<?php echo ($tt ? 'checked="checked"' : '');?> />
                <?php echo $AppUI->_('Project Owner'); ?>
                <input type="hidden" name="email_project_owner"
									id="email_project_owner"
									value="<?php echo ($object->project_owner ? $object->project_owner : '0');?>" />
									<input type='checkbox' name='email_project_contacts_box'
									id='email_project_contacts_box'
									<?php echo ($tp ? 'checked="checked"' : ''); ?> />
                <?php echo $AppUI->_('Project Contacts'); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project URL'); ?></td>
								<td>
                <?php $form->showField('project_url', $object->project_url, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Staging URL'); ?></td>
								<td>
                <?php $form->showField('project_demo_url', $object->project_demo_url, array('maxlength' => 255)); ?></td>
							</tr>

						</tbody>
					</table>
				</div>
                
    <?php if (apmgetConfig('budget_info_display', false)) { ?> 
    
    </div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
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
					$billingCategory = apmgetSysVal ( 'BudgetCategory' );
					$totalBudget = 0;
					foreach ( $billingCategory as $id => $category ) {
						$amount = 0;
						if (isset ( $object->budget [$id] )) {
							$amount = $object->budget [$id] ['budget_amount'];
						}
						$totalBudget += $amount;
						?>
                    <tr>
								<td class="apm-label"><?php echo $AppUI->_($AppUI->_($category)); ?></td>
								<td><span><p style="width: 80%; margin: 0 5% 0 0; float: left;"><?php $form->showField("budget_".$id, $amount, array('maxlength' => 15)); ?></p>
										<p style="float: left;"><?php echo $apmconfig['currency_symbol']; ?></p></span></td>
							</tr>
                <?php
					}
					?>
                <tr>
								<td class="apm-label"><?php echo $AppUI->_('Total Target Budget'); ?></td>
								<td><?php echo formatCurrency($totalBudget, $AppUI->getPref('CURRENCYFORM')); ?><?php echo '&nbsp;'.$apmconfig['currency_symbol'] ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Actual Budget'); ?></td>
								<td>
                    <?php
					if ($object_id > 0) {
						echo formatCurrency ( $object->project_actual_budget, $AppUI->getPref ( 'CURRENCYFORM' ) ) . '&nbsp;' . $apmconfig ['currency_symbol'];
                    } else {
                        echo $AppUI->_('Dynamically calculated');
                    }
                    ?></td>
							</tr>
            <?php } ?>

            
            <?php
            $custom_fields = new apm_Core_CustomFields($m, $a, $object->project_id, 'edit');
            echo $custom_fields->getHTML();
            ?>

       	</div>
						</tbody>
					</table>
				</div>
				<div><?php $form->showCancelButton(); ?><?php $form->showSaveButton(); ?>
	</div>
	
	
			</div>
			<!-- panel-body-->
		</div>
		
</div><!-- panel-default -->
</form>