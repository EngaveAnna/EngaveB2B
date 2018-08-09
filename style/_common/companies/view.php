<?php

// $view = new apm_Output_HTML_ViewHelper($AppUI);
$view = new apm_Output_HTMLHelper ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo $company->company_name; ?>
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
      		<?php echo $view->createCell('company_name', $company->company_name); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
            <?php echo $view->createCell('company_owner', $company->company_owner); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
            <?php echo $view->createCell('company_email', $company->company_email); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Phone'); ?></td>
            <?php echo $view->createCell('company_phone1', $company->company_phone1); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Phone2'); ?></td>
            <?php echo $view->createCell('company_phone2', $company->company_phone2); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Fax'); ?></td>
            <?php echo $view->createCell('company_fax', $company->company_fax); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Address'); ?></td>
            <?php echo $view->createCell('company', $company->company_address1); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('company_tin'); ?></td>
            <?php echo $view->createCell('company_tin', $company->company_tin); ?></tr>            
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('URL'); ?></td>
            <?php echo $view->createCell('company_primary_url', $company->company_primary_url); ?></tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Type'); ?></td>
            <?php echo $view->createCell('company_type', $AppUI->_($types[$company->company_type])); ?></tr>

        <?php
								$custom_fields = new apm_Core_CustomFields ( $m, $a, $company->company_id, 'view' );
								if ($custom - fields) {
									?>
        	<tr>
							<td colspan="2"><?php
									$custom_fields->printHTML ();
									?>
        	</td>
						</tr><?php
								}
								?>
    
					</tbody>
				</table>
			</div>

			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Description'); ?></td>
						</tr>
					</thead>

					<tbody>
     				      <tr><?php echo $view->createCell('company_description', $company->company_description); ?></tr>
					</tbody>
				</table>
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
						<?php echo $view->createCell('company_paymentid', $company->company_paymentid); ?>
					</tr>
					<tr>
						<td class="apm-label"><?php echo $AppUI->_('Account key'); ?></td>
						<?php echo $view->createCell('company_paymentkey', $company->company_paymentkey); ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>	
	</div>
	

	<!-- panel-body-->
</div>
<!-- panel-default -->