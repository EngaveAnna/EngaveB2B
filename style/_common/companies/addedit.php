<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post"
	accept-charset="utf-8" class="form-horizontal addeidt companies">
	<input type="hidden" name="dosql" value="do_company_aed" /> <input
		type="hidden" name="company_id"
		value="<?php echo $object->getId(); ?>" />
	<?php echo $form->addNonce(); ?>

	<div class="panel panel-default">
		<div class="panel-heading">
	<?php echo strlen($object->company_name) == 0 ? $AppUI->__('New company') : $object->company_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Name'); ?></td>
								<td>
			   <?php $form->showField('company_name', $object->company_name, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
								<td>
			   <?php $form->showField('company_email', $object->company_email, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Phone1'); ?></td>
								<td>
			   <?php $form->showField('company_phone1', $object->company_phone1, array('maxlength' => 30)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Phone2'); ?></td>
								<td>
			   <?php $form->showField('company_phone2', $object->company_phone2, array('maxlength' => 50)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('URL'); ?></td>
								<td>
			   <?php $form->showField('company_primary_url', $object->company_primary_url, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
			   <?php $form->showField('company_description', $object->company_description); ?></td>
							</tr>
			<?php
			$custom_fields = new apm_Core_CustomFields ( $m, $a, $object->company_id, "edit" );
			echo $custom_fields->getHTML ();
			
			?>

			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Address1'); ?></td>
								<td>
			   <?php $form->showField('company_address1', $object->company_address1, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Address2'); ?></td>
								<td>
			   <?php $form->showField('company_address2', $object->company_address2, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('City'); ?></td>
								<td>
			   <?php $form->showField('company_city', $object->company_city, array('maxlength' => 50)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('State'); ?></td>
								<td>
			   <?php $form->showField('company_state', $object->company_state, array('maxlength' => 50)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Zip'); ?></td>
								<td>
			   <?php $form->showField('company_zip', $object->company_zip, array('maxlength' => 15)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Country'); ?></td>
								<td>
			   <?php $form->showField('company_country', $object->company_country, array(), $countries); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Fax'); ?></td>
								<td>
			   <?php $form->showField('company_fax', $object->company_fax, array('maxlength' => 30)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('company_tin'); ?></td>
								<td>
			   <?php $form->showField('company_tin', $object->company_tin, array('maxlength' => 50)); ?></td>
							</tr>
							<tr>
			<?php
			$perms = &$AppUI->acl ();
			$users = $perms->getPermittedUsers ( 'companies' );
			?>
			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td>
			   <?php $form->showField('company_owner', $object->company_owner, array(), $users); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Type'); ?></td>
								<td>
			   <?php $form->showField('company_type', $object->company_type, array(), $types); ?></td>
							</tr>
							</div>
						</tbody>
					</table>
				</div>
				<div><?php $form->showCancelButton(); ?><?php $form->showSaveButton(); ?>
	</div>
			</div>
			
			
			
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Payment settings'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Account id'); ?></td>
								<td><?php $form->showField('company_paymentid', $object->company_paymentid, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Account key'); ?></td>
								<td><?php $form->showField('company_paymentkey', $object->company_paymentkey, array('maxlength' => 255)); ?></td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>			
			
			<!-- panel-body-->
		</div>
		<!-- panel-default -->

</form>