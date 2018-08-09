<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
?>
    
   <div class="panel panel-default">
	<div class="panel-heading"><?php echo $AppUI->_('Payment').': '.$object->payment_id.'-'.$object->payment_name; ?>
    </div>
		<div class="panel-body">

			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Basic information'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment title'); ?></td>
								<td><?php echo $view->showField('payment_name', $object->payment_name); ?></td>
							</tr>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment type'); ?></td>
								<td><?php echo $view->showField('payment_type', $paymentType[$object->payment_type]); ?></td>
							</tr>							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment amount'); ?></td>
								<td><?php echo number_format( $object->payment_amount, 2, ',', '').' '.$apmconfig['currency_symbol'];?></td>
               				</tr>
               				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment paid'); ?></td>
								<td><?php echo number_format($object->payment_paid, 2, ',', '').' '.$apmconfig['currency_symbol'];?></td>
               				</tr>
               				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment status'); ?></td>
								<td><?php echo $view->showField('payment_category', $paymentStatus[$object->payment_category]); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment date'); ?></td>
								<td><?php echo $view->showField('payment_date', $object->payment_date); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment owner'); ?></td>
								<td><?php echo $view->showField('payment_ownerw', $payment_owner->contact_first_name.' '.$payment_owner->contact_last_name); ?></td>
							</tr>
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Invoice'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice title'); ?></td>
								<td><?php echo $view->showField('invoice_name', $invoice->invoice_name); ?></td>
							</tr>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice status'); ?></td>
								<td><?php echo $view->showField('invoice_category', $invoiceCategory[$invoice->invoice_category]); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('invoice_total_pay'); ?></td>
								<td><?php echo number_format($invoice->invoice_total_pay, 2, ',', '').' '.$apmconfig['currency_symbol'];?></td>
               				</tr>
               				<tr>
								<td class="apm-label"><?php echo $AppUI->_('invoice_parties_owner'); ?></td>
								<td><?php echo $view->showField( 'invoice_parties', $invoice_parties_owner[0]['company_name']);?></td>
               				</tr>
						</tbody>
					</table>
				</div>
			</div>
		
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->



