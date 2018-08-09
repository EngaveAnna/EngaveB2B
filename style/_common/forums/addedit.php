<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="addedit forums">
	<input type="hidden" name="dosql" value="do_forum_aed" /> <input
		type="hidden" name="forum_unique_update"
		value="<?php echo uniqid(''); ?>" /> <input type="hidden"
		name="forum_id" value="<?php echo $object->getId(); ?>" />
    <?php echo $form->addNonce(); ?>

      <div class="panel panel-default">
		<div class="panel-heading">
<?php echo $AppUI->__('Forum').': '; ?> <?php echo strlen($object->forum_name) == 0 ? "n/a" : $object->forum_name; ?>
    </div>
		<div class="panel-body">



			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">



				<div class="table-responsive">
					<table class="table table-bordered table-striped table-static"
						width="100%" cellspacing="1" cellpadding="2" border="0">

						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th"></span>
    <?php echo $AppUI->__('Forum params'); ?>
</td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Forum name'); ?></td>
								<td>
                <?php $form->showField('forum_name', $object->forum_name, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Related Project'); ?></td>
								<td>
                <?php $form->showField('forum_project', $object->forum_project, array(), $projects); ?>
            </td>
							</tr>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Owner'); ?></td>
								<td>
                <?php $form->showField('forum_owner', $object->forum_owner, array(), $users); ?>
            </td>
							</tr>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Moderator'); ?></td>
								<td>
                <?php echo arraySelect($users, 'forum_moderated', 'size="1" class="form-control"', $object->forum_moderated); ?>
            </td>
							</tr>
            <?php if ($object_id) { ?>
                <tr>
								<td align="left">
                    <?php echo $AppUI->_('Message Count'); ?></td>
								<td>
                    <?php echo (int) $object->forum_message_count; ?>
                </td>
							</tr>
            <?php } ?>


            <tr>
								<td align="left">
                <?php echo $AppUI->_('Description'); ?></td>
								<td>
                <?php $form->showField('forum_description', $object->forum_description); ?>
            </td>
							</tr>
            <?php if ($object_id) { ?>
                <tr>
								<td align="left">
                    <?php echo $AppUI->_('Created On'); ?></td>
								<td>
                    <?php echo $AppUI->formatTZAwareTime($object->forum_create_date); ?>
                </td>
							</tr>
							<tr>
								<td align="left"> 
                    <?php  echo $AppUI->_('Last Post'); ?></td>
								<td>
                    <?php echo $AppUI->formatTZAwareTime($object->forum_last_date); ?>
			
                </td>
							</tr>
            <?php } ?>

</tbody>
					</table>
				</div>

				<div>
					<p><?php $form->showCancelButton();?><?php $form->showSaveButton();?></p>
				</div>
			</div>

		</div>
	</div>

</form>