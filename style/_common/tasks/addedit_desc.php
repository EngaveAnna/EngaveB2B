
	<input type="hidden" name="dosql" value="do_task_aed" /> <input
		type="hidden" name="task_id" value="<?php echo $object->getId(); ?>" />

	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
	</tr>
	</thead>
	<tbody>	
	<tr><td class="apm-label"><?php echo $AppUI->_('Task Owner'); ?></td><td>
                <?php
																$owner = ($object->task_owner) ? $object->task_owner : $AppUI->user_id;
																$form->showField ( 'task_owner', $owner, array (), $users );
																?>
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Access'); ?></td><td>
                <?php echo arraySelect($task_access, 'task_access', 'class="form-control"', (int) $object->task_access, true); ?>
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Task Parent'); ?></td><td>
                <select name='task_parent' class='form-control'>
					<option value='<?php echo $object->task_id; ?>'><?php echo $AppUI->_('None'); ?></option>
                    <?php echo $task_parent_options; ?>
                </select>
			</p>
	<tr><td class="apm-label"><?php echo $AppUI->_('Move to project'); ?></td><td>
                <?php echo arraySelect($projects, 'new_task_project', 'size="1" class="form-control" id="medium" onchange="submitIt(document.editFrm)"', $task_project); ?> 
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('URL'); ?></td><td>
                <?php $form->showField('task_related_url', $project->task_related_url, array('maxlength' => 255)); ?>
	</td></tr>


	<tr><td class="apm-label"><?php echo $AppUI->_('Task Type'); ?></td><td>
                <?php
																$task_types = apmgetSysVal ( 'TaskType' );
																$form->showField ( 'task_type', $object->task_type, array (), $task_types );
																?>
	</td></tr>
            <?php if ($AppUI->isActiveModule('contacts') && canView('contacts')) { ?>
	<tr><td class="apm-label"><?php echo $AppUI->_('Contacts'); ?></td><td>
                    <input type="button" class="btn btn-default"
					value="<?php echo $AppUI->_('Select contacts...'); ?>"
					onclick="javascript:popContacts();" />
			</p>
            <?php } ?>
            <?php if (count($department_selection_list) > 1) { ?>
	<tr><td class="apm-label"><?php echo $AppUI->_('Department'); ?></td><td>
                    <?php echo arraySelect($department_selection_list, 'dept_ids[]', 'class="form-control" size="1"', $object->task_departments); ?>
	</td></tr>
            <?php } ?>
	<tr><td class="apm-label"><?php echo $AppUI->_('Description'); ?></td><td>
                <?php $form->showField('task_description', $object->task_description); ?>
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Additional Email Comments'); ?></td><td>
                <textarea name="email_comment" class="textarea"
					cols="60" rows="10"></textarea>
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('notifyChange'); ?></td><td>
                <input type="checkbox" name="task_notify"
					id="task_notify" value="1"
					<?php if ($object->task_notify != '0') echo 'checked="checked"' ?> />
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Allow users to add task logs for others'); ?></td><td>
                <input type="checkbox" value="1"
					name="task_allow_other_user_tasklogs"
					<?php echo $object->task_allow_other_user_tasklogs ? 'checked="checked"' : ''; ?> />
	</td></tr>            


			</tbody>
	</table>
	</div>

	
	            <?php if (apmgetConfig('budget_info_display', false)) { ?>

      
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Target Budgets'); ?></td>
	</tr>
	</thead>
	<tbody>                 
      
                <?php
													$billingCategory = apmgetSysVal ( 'BudgetCategory' );
													$totalBudget = 0;
													foreach ( $billingCategory as $id => $category ) {
														$amount = $object->budget [$id] ['budget_amount'];
														$totalBudget += $amount;
														?>
	<tr><td class="apm-label"><?php echo $AppUI->_($AppUI->_($category)); ?></td><td><span><p style="float:left; width:80%; margin-right:5px;">
	<?php $form->showField("budget_".$id, $amount, array('maxlength' => 15)); ?></p><p><?php echo '&nbsp;'. $apmconfig['currency_symbol']; ?></p></span> 
	</td></tr>
                <?php } ?>
	<tr><td class="apm-label"><?php echo $AppUI->_('Total Target Budget'); ?></td><td>
	<?php echo formatCurrency($totalBudget, $AppUI->getPref('CURRENCYFORM')); ?><?php echo '&nbsp;'.$apmconfig['currency_symbol'] ?> 
	</td></tr>
    	
 			</tbody>
	</table>
	</div>   	
    	
            <?php } ?>
            
	<?php $custom_fields = new apm_Core_CustomFields ( $m, $a, $object->task_id, 'edit' );
	echo $custom_fields->getHTML (); ?>
