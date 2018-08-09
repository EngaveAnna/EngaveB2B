<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>"
	enctype="multipart/form-data" method="post" class="addedit files">
	<input type="hidden" name="dosql" value="do_file_aed" /> <input
		type="hidden" name="cancel" value="0" /> <input type="hidden"
		name="del" value="0" /> <input type="hidden" name="file_id"
		value="<?php echo $object->getId(); ?>" /> <input type="hidden"
		name="file_parent"
		value="<?php echo ($object->file_parent) ? $object->file_parent : $file_parent; ?>" />
	<input type="hidden" name="file_version_id"
		value="<?php echo $object->file_version_id; ?>" /> <input
		type="hidden" name="redirect" value="<?php echo $referrer; ?>" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
		<div class="panel-heading">
	<?php echo strlen($object->file_name) == 0 ? $AppUI->__('New file') : $AppUI->__('File').': '.$object->file_name; ?>
    </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-static"
						width="100%" cellspacing="1" cellpadding="2" border="0">

						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th"></span>
    <?php echo $AppUI->__('File params'); ?>
</td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td align="left">
		<?php echo $AppUI->_('File name'); ?></td>
								<td>
		<?php $form->showField('file_name', $object->file_name, array('maxlength' => 50)); ?>
	</td>
							</tr>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Folder'); ?></td>
								<td>
                <?php if ($object_id == 0 && !$ci) { ?>
                    <?php echo arraySelect($folders, 'file_folder', 'class="form-control"', $folder); ?>
                <?php } else { ?>
                    <?php echo arraySelect($folders, 'file_folder', 'class="form-control"', $object->file_folder); ?>
                <?php } ?>
            </td>
							</tr>
            <?php if ($object->file_id) { ?>
                <tr>
								<td align="left">
                    <?php echo $AppUI->_('File Name'); ?></td>
								<td>
                    <?php echo strlen($object->file_name) == 0 ? 'n/a' : $object->file_name; ?>
                </td>
							</tr>
							<tr>
								<td align="left">
                    <?php echo $AppUI->_('Type'); ?></td>
								<td>
                    <?php echo $object->file_type; ?>
                </td>
							</tr>
							<tr>
								<td align="left">
                    <?php echo $AppUI->_('Size'); ?></td>
								<td>
                    <?php echo $object->file_size; ?> <?php echo $AppUI->_('bytes'); ?>
                </td>
							</tr>
							<tr>
								<td align="left">
                    <?php echo $AppUI->_('Uploaded By'); ?></td>
                    <?php echo $htmlHelper->createCell('object_owner', $object->file_owner);?>
                    <!-- @TODO lookup this value -->
							</tr>
            <?php } ?>
            
            <?php echo file_show_attr($AppUI, $form); ?>
            <tr>
								<td align="left">
                <?php echo $AppUI->_('Description'); ?></td>
								<td>
                <?php $form->showField('file_description', $object->file_description); ?>
            </td>
							</tr>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Upload File'); ?></td>
								<td><input type="File" name="formfile" style="width: 270px" /></td>
							</tr>
            <?php if ($ci || ($canAdmin && $object->file_checkout == 'final')) { ?>
                <tr>
								<td align="left">
                    <?php echo $AppUI->_('Final Version'); ?></td>
								<td><input type="checkbox" name="final_ci" id="final_ci"
									onclick="finalCI()" /></td>
							</tr>
            <?php } ?>
            <tr>
								<td align="left">
                <?php echo $AppUI->_('Notify Assignees of Task or Project Owner by Email'); ?></td>
								<td><input type="checkbox" name="notify" id="notify"
									checked="checked" /></td>
							</tr>
            <?php if ($object->file_id && $object->file_checkout <> '' && ((int) $object->file_checkout == $AppUI->user_id || $canAdmin)) { ?>
                <tr>
								<td align="left">
                    <?php echo $AppUI->_('&nbsp;'); ?></td>
								<td><input type="button" class="btn btn-default"
									value="<?php echo $AppUI->_('cancel checkout'); ?>"
									onclick="cancelIt()" /></td>
							</tr>
            <?php } ?>

</tbody>
					</table>

				</div>
				<div>
                <?php $form->showCancelButton(); ?>
                <?php
																if (is_writable ( apm_BASE_DIR . '/files' )) {
																	$form->showSaveButton ();
																} else {
																	?><span class="error">File uploads not allowed.
						Please check permissions on the /files directory.</span><?php
                }
                ?>
            </div>
			</div>
			<!-- first col -->
		</div>
		<!-- panel-body -->

</form>
