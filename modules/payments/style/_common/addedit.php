<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post" accept-charset="utf-8" class="addedit payments">
	<input type="hidden" name="dosql" value="do_payment_aed" /> <input	type="hidden" name="payment_id" value="<?php echo $object->getId(); ?>" />
	<!-- TODO: Right now, payment owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="payment_owner" value="<?php echo $object->payment_owner; ?>" />
	<input type="hidden" name="datePicker" value="payment" />
	<input type="hidden" name="payment_id" value="<?php echo $object->payment_id; ?>" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->payment_name) == 0 ? $AppUI->__('New payment') : $AppUI->__('Payment').': '.$object->payment_name; ?>
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
								<td><?php echo $form->showField('payment_name', $object->payment_name); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment order identifier'); ?></td>
								<td><?php echo $form->showField('payment_order', $object->payment_order); ?></td>
							</tr>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment type'); ?></td>
								<td><?php echo $form->showField('payment_type',  $paymentType[$object->payment_type], array(), $paymentType ); ?></td>
							</tr>							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment amount'); ?></td>
								<td><?php echo $form->showField( 'payment_amount', $object->payment_amount);?></td>
               				</tr>
               				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment paid'); ?></td>
								<td><?php echo $form->showField('payment_paid', $object->payment_paid); ?></td>
               				</tr>
							<tr>
									<td class="apm-label"><?php echo $AppUI->_('Payment date'); ?></td>
									<td class="input-group date">
									<?php echo $form->showField('payment_date', $object->payment_date); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Payment owner'); ?></td>
								<td><?php echo $form->showField('payment_owner', $users[$object->payment_owner],  array(), $users); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
	

	<?php $form->showCancelButton(); $form->showSaveButton(); ?>	
    </div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
</form>