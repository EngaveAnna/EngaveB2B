<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="changecontact" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="contacts addedit">
	<input type="hidden" name="dosql" value="do_contact_aed" /> <input
		type="hidden" name="contact_project" value="0" /> <input type="hidden"
		name="contact_unique_update" value="<?php echo uniqid(''); ?>" /> <input
		type="hidden" name="contact_id"
		value="<?php echo $object->getId(); ?>" /> <input type="hidden"
		name="contact_owner"
		value="<?php echo $object->contact_owner ? $object->contact_owner : $AppUI->user_id; ?>" />
    <?php echo $form->addNonce(); ?>

     <div class="panel panel-default">
		<div class="panel-heading">
    <?php echo strlen($object->contact_display_name) == 0 ? $AppUI->__('New contact') : $object->contact_display_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('First Name'); ?></td>
								<td>
                <?php $form->showField('contact_first_name', $object->contact_first_name, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Last Name'); ?></td>
								<td>
                <?php
																$options = array (
																		'maxlength' => 50 
																);
																if ($object_id == 0) {
																	$options ['onBlur'] = "orderByName('name')";
																}
																?>
                <?php $form->showField('contact_last_name', $object->contact_last_name, $options); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Display Name'); ?></td>
								<td>
                <?php $form->showField('contact_display_name', $object->contact_display_name, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Title'); ?></td>
								<td>
                <?php $form->showField('contact_title', $object->contact_title, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
								<td>
                <?php $form->showField('contact_email', $object->contact_email, array('maxlength' => 60)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Phone'); ?></td>
								<td>
                <?php $form->showField('contact_phone', $object->contact_phone, array('maxlength' => 50)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Company'); ?></td>
								<td>
                <?php echo arraySelect($companies, 'contact_company', 'size="1" class="text company" onChange="companyChange()"', $object->contact_company); ?>
            </td>
							</tr>
            <?php if ($AppUI->isActiveModule('departments')) { ?>
                <tr>
								<td class="apm-label"><?php echo $AppUI->_('Department'); ?></td>
								<td><input type="text" class="form-control"
									name="contact_department_name" id="contact_department_name"
									value="<?php echo $dept_detail['dept_name']; ?>"
									maxlength="100" size="25" /> <input type='hidden'
									name='contact_department'
									value='<?php echo $dept_detail['dept_id']; ?>' /> <input
									type="button" class="btn btn-info"
									value="<?php echo $AppUI->_('select department...'); ?>"
									onclick="popDepartment()" /></td>
							</tr>
            <?php } ?>
            <tr>
								<td class="apm-label"><?php echo $AppUI->_('Job Title'); ?></td>
								<td>
                <?php $form->showField('contact_job', $object->contact_job, array('maxlength' => 100)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Contact Notes'); ?></td>
								<td>
                <?php $form->showField('contact_notes', $object->contact_notes); ?>
            </td>
							</tr>
            <?php
												$custom_fields = new apm_Core_CustomFields ( $m, $a, $object->contact_id, "edit" );
												echo $custom_fields->getHTML ();
												
												?>

            <tr>
								<td class="apm-label"><?php echo $AppUI->_('Address1'); ?></td>
								<td>
                <?php $form->showField('contact_address1', $object->contact_address1, array('maxlength' => 60)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Address2'); ?></td>
								<td>
                <?php $form->showField('contact_address2', $object->contact_address2, array('maxlength' => 60)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('City'); ?></td>
								<td>
                <?php $form->showField('contact_city', $object->contact_city, array('maxlength' => 30)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('State'); ?></td>
								<td>
                <?php $form->showField('contact_state', $object->contact_state, array('maxlength' => 30)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Zip'); ?></td>
								<td>
                <?php $form->showField('contact_zip', $object->contact_zip, array('maxlength' => 11)); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Country'); ?></td>
								<td>
                <?php $form->showField('contact_country', $object->contact_country, array(), $countries); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Birthday'); ?></td>
								<td>
                <?php $form->showField('contact_birthday', $object->contact_birthday, array('maxlength' => 10)); ?> (<?php echo $AppUI->_('yyyy-mm-dd'); ?>)
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Awaiting Update'); ?></td>
								<td>
                <?php
																$options = array (
																		'onclick' => 'updateVerify()' 
																);
																if ($object->contact_updatekey) {
																	$options ['checked'] = 'checked';
																}
																?>
                <?php $form->showField('contact_updateask', 1, $options); ?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Update Requested'); ?></td>
								<td>
                <?php $last_ask = new apm_Utilities_Date($object->contact_updateasked); ?>
                <?php
																echo $object->contact_updateasked ? $AppUI->formatTZAwareTime ( $object->contact_updateasked ) : '&nbsp;';
																?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Last Updated'); ?></td>
								<td>
                <?php
																
$lastupdated = new apm_Utilities_Date ( $object->contact_lastupdate );
																echo ($object->contact_lastupdate && ! ($object->contact_lastupdate == 0)) ? $AppUI->formatTZAwareTime ( $object->contact_lastupdate ) : '&nbsp;';
																?>
            </td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Private Entry'); ?></td>
								<td>
                <?php
																$options = array ();
																if ($object->contact_private) {
																	$options ['checked'] = 'checked';
																}
																?>
                <?php $form->showField('contact_private', $object->contact_private, $options); ?>
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