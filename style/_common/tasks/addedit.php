<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>&a=addedit&project_id=<?php echo $task_project; ?>"
	method="post" onSubmit="return submitIt(document.editFrm);"
	accept-charset="utf-8" class="addedit tasks">
	<input name="dosql" type="hidden" value="do_task_aed" /> <input
		name="task_id" type="hidden" value="<?php echo $object->getId(); ?>" />
	<input name="task_project" type="hidden"
		value="<?php echo $task_project; ?>" /> <input name="old_task_parent"
		type="hidden" value="<?php echo $object->task_parent; ?>" /> <input
		name='task_contacts' id='task_contacts' type='hidden'
		value="<?php echo implode(',', $selected_contacts); ?>" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
		<div class="panel-heading">
    <?php echo strlen($object->task_name) == 0 ? $AppUI->__('New task') : $AppUI->__('Task').': '.$object->task_name; ?>
    </div>
	<div class="panel-body">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Main data'); ?></td>
	</tr>
	</thead>
	<tbody>				
	<tr><td class="apm-label"><?php echo $AppUI->_('Name'); ?></td><td><?php $form->showField('task_name', $object->task_name, array('maxlength' => 255)); ?>
	</td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Priority'); ?></td><td>
                <?php $form->showField('task_priority', (int) $object->task_priority, array(), $priority); ?>
    </td></tr>

	<tr><td class="apm-label"><?php echo $AppUI->_('Status'); ?></td><td>
                <?php $form->showField('task_status', (int) $object->task_status, array(), $status); ?>
    </td></tr>
	<tr><td class="apm-label"><?php echo $AppUI->_('Progress'); echo " (%)"; ?></td><td>
                <?php echo arraySelect($percent, 'task_percent_complete', 'size="1" class="form-control"', $object->task_percent_complete); ?>
            </p>
	<tr><td class="apm-label"><?php echo $AppUI->_('Milestone'); ?></td><td>
                <input type="checkbox" value="1" name="task_milestone"
						id="task_milestone" <?php if ($object->task_milestone) { ?>
						checked="checked" <?php } ?> onClick="toggleMilestone()" />
	</td></tr>

			</div>
		</div>
	</div>
	
	</tbody>
	</table>
	</div>
	
	<div name="hiddenSubforms" id="hiddenSubforms" style="display: none;"></div>