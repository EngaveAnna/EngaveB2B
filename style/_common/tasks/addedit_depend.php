
	<input name="dosql" type="hidden" value="do_task_aed" /> <input
		name="task_id" type="hidden" value="<?php echo $object->getId(); ?>" />
	<input type="hidden" name="hdependencies" />

	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Dependencies'); ?></td>
	</tr>
	</thead>
	<tbody>	
	
	<tr><td class="apm-label" style="width:50%;"><?php echo $AppUI->_('Dynamic Task'); ?></td><td>
                <input type="checkbox" name="task_dynamic"
					id="task_dynamic" value="1"
					<?php if ($object->task_dynamic == "1") { echo 'checked="checked"'; } ?> />
			</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Do not track this task'); ?></td><td>
                <input type="checkbox" name="task_dynamic_nodelay"
					id="task_dynamic_nodelay" value="1"
					<?php if (($object->task_dynamic > '10') && ($object->task_dynamic < 30)) { echo 'checked="checked"'; } ?> />
			</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Dependency Tracking'); ?></td><td>
                <?php echo $AppUI->_('On'); ?><input type="radio"
					name="task_dynamic" value="31"
					<?php if ($object->getId() == 0 || $object->task_dynamic > '20') { echo "checked"; } ?> />
                <?php echo $AppUI->_('Off'); ?><input type="radio"
					name="task_dynamic" value="0"
					<?php if ($object->getId() && ($object->task_dynamic == '0' || $object->task_dynamic == '11')) { echo "checked"; } ?> />
			</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Set task start date based on dependency'); ?></td><td>
                <input type="checkbox" name="set_task_start_date"
					id="set_task_start_date"
					<?php if ($object->getId() == 0 || $object->task_dynamic > '20') { echo "checked"; } ?> />
			</td></tr>			
			
			
	<tr><td class="apm-label" >

			<p>
                <?php $form->showLabel('All Tasks'); ?>
                <select name="all_tasks" class="form-control"
					style="width:100%" size="10" class="form-control"
					multiple="multiple">
                    <?php echo str_replace('selected', '', $task_parent_options); // we need to remove selected added from task_parent options ?>
                </select>
			</p>
			<p>
				<input type="button" class="btn btn-default" value="&gt;"
					onclick="addTaskDependency(document.editFrm, document.editFrm)" />
			</p>
</td><td >

			<p>
                <?php $form->showLabel('Dependencies'); ?>
                <?php echo arraySelect($taskDep, 'task_dependencies', 'style="width:100%" size="10" class="form-control" multiple="multiple" ', null); ?>
            </p>
			<p>
				<input type="button" class="btn btn-default" value="&lt;"
					onclick="removeTaskDependency(document.editFrm, document.editFrm)" />
			</p>

		</td></tr>
			</tbody>
	</table>
	</div>
	
		<div><?php $form->showCancelButton(); ?>
    <input class="btn btn-info" type="button" onclick="submitIt(document.editFrm)" name="btnFuseAction" value="<?php echo $AppUI->_('save'); ?>" />
	</div>	
	
</form>

</div></div></div>