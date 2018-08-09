<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="folderFrm" action="?m=<?php echo $m; ?>"
	enctype="multipart/form-data" method="post"
	class="addedit files-folder">
	<input type="hidden" name="dosql" value="do_folder_aed" /> <input
		type="hidden" name="del" value="0" /> <input type="hidden"
		name="file_folder_id" value="<?php echo $object_id; ?>" /> <input
		type="hidden" name="redirect" value="<?php echo $referrer; ?>" />
    <?php echo $form->addNonce(); ?>

	<div class="panel panel-default">
		<div class="panel-heading">
	<?php echo strlen($object->file_folder_name) == 0 ? $AppUI->__('New folder') : $object->file_folder_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Subfolder of'); ?></td>
								<td>
                <?php
																$parent_folder = ($object_id > 0) ? $object->file_folder_parent : $file_folder_parent;
																echo arraySelect ( $folders, 'file_folder_parent', 'style="width:175px;" class="form-control"', $parent_folder );
																?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Folder Name'); ?></td>
								<td>
                <?php $form->showField('file_folder_name', $object->file_folder_name, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                <?php $form->showField('file_folder_description', $object->file_folder_description); ?></td>
							</tr>
							</div>
						</tbody>
					</table>
				</div>
				<div><?php $form->showCancelButton(); ?><?php $form->showSaveButton(); ?>
	</div>
			</div>
			<!-- panel-body-->
		</div>
		<!-- panel-default -->

</form>