<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="addedit links">
	<input type="hidden" name="dosql" value="do_link_aed" /> <input
		type="hidden" name="link_id" value="<?php echo $object->getId(); ?>" />
	<!-- TODO: Right now, link owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="link_owner"
		value="<?php echo $object->link_owner; ?>" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
		<div class="panel-heading">
     <?php echo strlen($object->link_name) == 0 ? $AppUI->_('New link') : $AppUI->_('Link').': '.$object->link_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Link Name'); ?></td>
								<td>
                <?php $form->showField('link_name', $object->link_name, array('maxlength' => 255)); ?>
                <?php if ($object_id) { ?>
                    <a href="<?php echo $object->link_url; ?>"
									target="_blank"><?php echo $AppUI->_('go'); ?></a>
                <?php } ?>
            </td>
							</tr>
            <?php if ($link_id) { ?>
                <tr>
								<td class="apm-label"><?php echo $AppUI->_('Created By'); ?></td>
								<td>
                    <?php $form->showField('link_owner', $object->link_owner, array(), $users); ?>
                </td>
							</tr>
            <?php } ?>
            <tr>
								<td class="apm-label"><?php echo $AppUI->_('Category'); ?></td>
								<td>
                <?php $form->showField('link_category', $object->link_category, array(), $types); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project'); ?></td>
								<td>
                <?php $form->showField('link_project', $object->link_project, array(), $projects); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Task'); ?></td>
								<td><input type="hidden" name="link_task"
									value="<?php echo $object->link_task; ?>" /> <input type="text"
									class="form-control" name="task_name"
									value="<?php echo isset($object->task_name) ? $object->task_name : ''; ?>"
									size="40" disabled="disabled" /> <input type="button"
									class="btn btn-default"
									value="<?php echo $AppUI->_('select task'); ?>..."
									onclick="popTask()" /></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                <?php $form->showField('link_description', $object->link_description); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('URL'); ?></td>
								<td>
                <?php $form->showField('link_url', $object->link_url, array()); ?>
            </td>
							</tr>

						</tbody>
					</table>
				</div>
	<?php	$form->showCancelButton();	$form->showSaveButton();	?>	
    </div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
</form>