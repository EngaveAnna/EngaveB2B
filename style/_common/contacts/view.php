<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
?>
<div class="panel panel-default">
	<div class="panel-heading">
	<?php echo strlen($contact->contact_display_name) == 0 ? $AppUI->__('New contact') : $contact->contact_display_name; ?>
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
            <?php $view->showField('contact_firstname', $contact->contact_first_name); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Last Name'); ?></td>
							<td>
            <?php $view->showField('contact_lastname', $contact->contact_last_name); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Display Name'); ?></td>
							<td>
            <?php $view->showField('contact_displayname', $contact->contact_display_name); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Title'); ?></td>
							<td>
            <?php $view->showField('contact_title', $contact->contact_title); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Job Title'); ?></td>
							<td>
            <?php $view->showField('contact_job', $contact->contact_job); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Company'); ?></td>
							<td>
            <?php $view->showField('contact_company', $contact->contact_company); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Department'); ?></td>
							<td>
            <?php $view->showField('contact_department', $contact->contact_department); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Address'); ?></td>
							<td>
            <?php $view->showAddress('contact', $contact); ?>
        </td>
						</tr>

						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Birthday'); ?></td>
							<td>
            <?php $view->showField('contact_birthday', $contact->contact_birthday); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Phone'); ?></td>
							<td>
            <?php $view->showField('contact_phone', $contact->contact_phone); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
							<td>
            <?php $view->showField('contact_email', $contact->contact_email); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
							<td>
            <?php $view->showField('contact_notes', $contact->contact_notes); ?>
        </td>
						</tr>


        <tr>
							<td class="apm-label"><?php echo $AppUI->_('Waiting Update'); ?></td>
							<td><input type="checkbox" value="1" name="contact_updateasked"
								disabled="disabled"
								<?php echo $contact->contact_updatekey ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Last Updated'); ?></td>
							<td>
            <?php $view->showField('contact_lastupdate', $contact->contact_lastupdate); ?>
        </td>
						</tr>

					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->