<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="admin addedit">
	<input type="hidden" name="user_id"
		value="<?php echo $object->getId(); ?>" /> <input type="hidden"
		name="contact_id" value="<?php echo (int) $object->contact_id; ?>" />
	<input type="hidden" name="dosql" value="do_user_aed" /> <input
		type="hidden" name="username_min_len"
		value="<?php echo apmgetConfig('username_min_len'); ?>)" /> <input
		type="hidden" name="password_min_len"
		value="<?php echo apmgetConfig('password_min_len'); ?>)" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
		<div class="panel-heading">
    <?php echo $AppUI->__('User').': '; ?> <?php echo strlen($object->contact_first_name) && strlen($object->contact_last_name) == 0 ? "n/a" : $object->contact_first_name.' '.$object->contact_last_name; ?>
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
								<td class="apm-label">
                <?php echo $AppUI->_('Login Name'); ?></td>
								<td>
                <?php
																if ($object->user_username) {
																	echo '<input type="hidden" class="form-control" name="user_username" value="' . $object->user_username . '" />';
																	echo $object->user_username;
																} else {
																	echo '<input type="text" class="form-control" name="user_username" value="' . $object->user_username . '" maxlength="255" size="40" />';
																}
																?>
            </td>
							</tr>
            <?php if (!$object_id) { ?>
                <tr>
								<td class="apm-label">
                    <?php echo $AppUI->_('Password'); ?></td>
								<td><input type="password" class="form-control"
									name="user_password"
									value="<?php echo $object->user_password; ?>" maxlength="32"
									size="32" onKeyUp="checkPassword(this.value);" /></td>
							</tr>
							<tr>
								<td class="apm-label">
                    <?php echo $AppUI->_('Confirm Password'); ?></td>
								<td><input type="password" class="form-control"
									name="password_check"
									value="<?php echo $object->user_password; ?>" maxlength="32"
									size="32" /></td>
							</tr>
            <?php } ?>
            <tr>
								<td class="apm-label">
                <?php echo $AppUI->_('Name'); ?></td>
								<td>
                <?php $form->showField('contact_first_name', $object->contact_first_name, array('maxlength' => 50)); ?>
                          </td>
							</tr>
							<tr>
								<td class="apm-label">  
                <?php echo $AppUI->_('Last Name'); ?></td>
								<td>
                <?php $form->showField('contact_last_name', $object->contact_last_name, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label">
                <?php echo $AppUI->_('Company'); ?></td>
								<td>
                <?php
																echo arraySelect ( $companies, 'contact_company', 'class=text size=1', $object->contact_company );
																?>
            </td>
							</tr>
							<tr>
								<td class="apm-label">
                <?php echo $AppUI->_('Department'); ?></td>
								<td><input type="hidden" name="contact_department"
									value="<?php echo $object->contact_department; ?>" /> <input
									type="text" class="form-control" name="dept_name"
									value="<?php echo $object->dept_name; ?>" size="40"
									disabled="disabled" /> <input type="button"
									class="btn btn-default"
									value="<?php echo $AppUI->_('select dept'); ?>..."
									onclick="popDept()" /></td>
							</tr>

            <?php if ($canEdit && !$object_id) { ?>
                <tr>
								<td class="apm-label">
                    <?php echo $AppUI->_('User Role'); ?></td>
								<td>
                    <?php echo arraySelect($roles_arr, 'user_role', 'size="1" class="form-control"', '', true); ?>
                </td>
							</tr>
            <?php } ?>
            <?php if (!$object_id) { ?>
                <tr>
								<td class="apm-label">
                    <?php echo $AppUI->_('Password Strength'); ?></td>
								<td>
									<div id="password-strength" class="form-control">
										<div id="progressBar"></div>
									</div>
								</td>
							</tr>
            <?php } ?>
            <tr>
								<td class="apm-label">
                <?php echo $AppUI->_('Email'); ?></td>
								<td>
                <?php $form->showField('contact_email', $object->contact_email, array('maxlength' => 255)); ?>
            </td>
							</tr>
							<tr>
								<td align="left">
                <?php echo $AppUI->_('Icon'); ?></td>
								<td><input type="File" name="formfile" style="width: 270px" /></td>
							</tr>
							<tr>
								<td class="apm-label">
                <?php echo $AppUI->_('Email Signature'); ?></td>
								<td>
                <?php $form->showField('user_signature', $object->user_signature); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label">
                <?php echo $AppUI->_('Inform new user of account details?'); ?></td>
								<td><input type="checkbox" value="1" name="send_user_mail"
									id="send_user_mail" /></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div>
			<?php $form->showCancelButton(); ?><?php $form->showSaveButton(); ?>
		</div>
			</div>
		</div>
	</div>
</form>
