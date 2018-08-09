<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="addedit departments">
	<input type="hidden" name="dosql" value="do_dept_aed" /> <input
		type="hidden" name="dept_id" value="<?php echo $object->getId(); ?>" />
	<input type="hidden" name="dept_company"
		value="<?php echo $company_id; ?>" />
    <?php echo $form->addNonce(); ?>

     <div class="panel panel-default">
		<div class="panel-heading">
    <?php echo strlen($object->dept_name) == 0 ? $AppUI->__('New department') : $object->dept_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Company Name'); ?></td>
								<td>
                <?php echo $companyName; ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Name'); ?></td>
								<td>
                <?php $form->showField('dept_name', $object->dept_name, array('maxlength' => 255)); ?>
            </td>
							</tr>
            <?php
												if (count ( $depts ) > 0) {
													?>
                <tr>
								<td class="apm-label"><?php echo $AppUI->_('Parent'); ?></td>
								<td>
                    <?php $form->showField('dept_parent', $object->dept_parent, array(), $depts); ?>
                </td>
							</tr>
            <?php
												} else {
													echo '<input type="hidden" name="dept_parent" value="0">';
												}
												?>
            <tr>
								<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
								<td>
                <?php $form->showField('dept_email', $object->dept_email, array('maxlength' => 255)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Phone'); ?></td>
								<td>
                <?php $form->showField('dept_phone', $object->dept_phone, array('maxlength' => 30)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('URL'); ?></td>
								<td>
                <?php $form->showField('dept_url', $object->dept_url, array('maxlength' => 255)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                <?php $form->showField('dept_desc', $object->dept_desc); ?>
            </td>
							</tr>


							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Address1'); ?></td>
								<td>
                <?php $form->showField('dept_address1', $object->dept_address1, array('maxlength' => 255)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Address2'); ?></td>
								<td>
                <?php $form->showField('dept_address2', $object->dept_address2, array('maxlength' => 255)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('City'); ?></td>
								<td>
                <?php $form->showField('dept_city', $object->dept_city, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('State'); ?></td>
								<td>
                <?php $form->showField('dept_state', $object->dept_state, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Zip'); ?></td>
								<td>
                <?php $form->showField('dept_zip', $object->dept_zip, array('maxlength' => 15)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Country'); ?></td>
								<td>
                <?php $form->showField('dept_country', $object->dept_country, array(), $countries); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Fax'); ?></td>
								<td>
                <?php $form->showField('dept_fax', $object->dept_fax, array('maxlength' => 30)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td>
                <?php
																$perms = &$AppUI->acl ();
																$users = $perms->getPermittedUsers ( 'departments' );
																?>
                <?php $form->showField('dept_owner', $object->dept_owner, array(), $users); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Type'); ?></td>
								<td>
                <?php $form->showField('dept_type', $object->dept_type, array(), $types); ?>
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