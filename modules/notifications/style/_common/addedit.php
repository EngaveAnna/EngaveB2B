<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="addedit notifications">
	<input type="hidden" name="dosql" value="do_notification_aed" /> <input	type="hidden" name="notification_id" value="<?php echo $object->getId(); ?>" />
	<!-- TODO: Right now, notification owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="notification_owner" value="<?php echo $object->notification_owner; ?>" />

    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->notification_name) == 0 ? $AppUI->__('New notification') : $AppUI->__('Notification').': '.$object->notification_name; ?>
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
								<td><?php $form->showField('notification_name', $object->notification_name, array('maxlength' => 255)); ?></td>
							</tr>
            				<?php if ($notification_id) { ?>
                			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Created By'); ?></td>
								<td>
								<?php $form->showField('notification_owner', $object->notification_owner, array(), $users); ?>
								</td>
							</tr>
            					<?php } ?>
            					<tr>
								<td class="apm-label"><?php echo $AppUI->_('Category'); ?></td>
								<td>
                				<?php $form->showField('notification_category', $object->notification_category, array(), $types); ?>
            					</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Priority'); ?></td>
								<td>
                				<?php $form->showField('notification_priority', (int) $object->notification_priority, array(), $projectPriority); ?>
                				</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project'); ?></td>
								<td>
                				<?php $form->showField('notification_project', $object->notification_project, array(), $projects); ?>
            					</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Task'); ?></td>
								<td>
								<input type="hidden" name="notification_task" id="notification_task" value="<?php echo $object->notification_task; ?>" />
								<?php
								$form->addAjaxModal($modId, $AppUI->_('select task')); 
								$btnModal1_Onclk ='javascript:getModalAjaxData(\''.$modId.'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=tasks&task_project=\'+getModalAjaxProcVar(\'notification_project\', true)+\'&'.$procVar.'=\'+getModalAjaxProcVar(\''.$procVar.'\', false)+\'&modId='.$modId.'&procVar='.$procVar.'\')';
								$btnModal1 = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('select task').'" onClick="'.$btnModal1_Onclk.'">
								<span data-toggle="modal" data-target="#'.$modId.'">'.$AppUI->_('select task').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal1; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId.'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList); ?>
								</span>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                					<?php $form->showField('notification_description', $object->notification_description); ?>
            					</td>
							</tr>
							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Status'); ?></td>
								<td>
                    				<?php $form->showField('notification_status',  (int) $object->notification_status, array(), $status_array); ?>
                				</td>
							</tr>							
						</tbody>
					</table>
				</div>
	<?php $form->showCancelButton(); $form->showSaveButton(); ?>	
    </div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
</form>