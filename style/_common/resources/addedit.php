<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="addedit resources">
	<input type="hidden" name="dosql" value="do_resource_aed" /> <input
		type="hidden" name="resource_id"
		value="<?php echo $object->getId(); ?>" />
    <?php echo $form->addNonce(); ?>

	<div class="panel panel-default">
		<div class="panel-heading">
    <?php echo strlen($obj->resource_name) == 0 ? $AppUI->__('New resource') : $obj->resource_name; ?>
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
								<td></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Resource Identifier'); ?></td>
								<td>
                <?php $form->showField('resource_key', $object->resource_key, array('maxlength' => 64)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Resource Name'); ?></td>
								<td>
                <?php $form->showField('resource_name', $object->resource_name, array('maxlength' => 255)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Type'); ?></td>
								<td>
                <?php $form->showField('resource_type', $object->resource_type, array(), $typelist); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Max Allocation'); ?></td>
								<td>
                <?php $form->showField('resource_max_allocation', $object->resource_max_allocation, array(), $percent); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Notes'); ?></td>
								<td>
                <?php $form->showField('resource_note', $object->resource_description); ?>
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