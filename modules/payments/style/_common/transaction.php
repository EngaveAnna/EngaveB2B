<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
$payconf=array('SID'=>'15141', 'MYSID'=>null, 'url_verif'=>'http://910works.pl/ucontrol/payment/result.php', 'url_powrotu'=>'http://910works.pl/zakupy/', 'KOD'=>'IKJeMp1mEvy0zav9');
?>

	<input type="hidden" name="dosql" value="do_payment_aed" /> <input	type="hidden" name="payment_id" value="<?php echo $object->getId(); ?>" />
	<!-- TODO: Right now, payment owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="payment_owner" value="<?php echo $object->payment_owner; ?>" />
    <?php echo $form->addNonce(); ?>

    
   <div class="panel panel-default">
	<div class="panel-heading"><?php echo $AppUI->_('Launching payment process'); ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Invoice title'); ?></td>
								<td><?php echo $view->showField('invoice_name', $object->invoice_name); ?></td>
							</tr>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice status'); ?></td>
								<td><?php echo $view->showField('invoice_category', $invoiceCategory[$object->invoice_category]); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('invoice_total_pay'); ?></td>
								<td><?php echo number_format($object->invoice_total_pay, 2, ',', '').' '.$apmconfig['currency_symbol']; ?></td>
               				</tr>
						</tbody>
					</table>
				</div>
				
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Invoice dates'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('invoice_issue_date'); ?></td>
								<td><?php echo $view->showField('invoice_date', $object->invoice_issue_date); ?></td>
							</tr>						
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('invoice_sale_date'); ?></td>
								<td><?php echo $view->showField('invoice_sale_date', $object->invoice_sale_date); ?></td>							</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('invoice_pay_date'); ?></td>
								<td><?php echo $view->showField('invoice_pay_date', $object->invoice_pay_date); ?></td>
							</tr>							
						</tbody>
					</table>
				</div>
				
		

				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	<?php if($epayment)
	{?>
				<div class="alert alert-warning">
				<span class="fa fa-alert fa-info-circle"></span>
				<a class="close" data-dismiss="alert" href="#">×</a>
				<?php echo $AppUI->_('The user accepts electronic payments').'. '; ?>
				<?php echo $AppUI->_('Message received shortly after returning from the payment provider, provides only about the status of the procedures necessary to initiate the transfer').'. ';?>
				<?php echo $AppUI->_('The actual status of the transaction is available in the tab').': '; ?>
				<a href="./index.php?m=payments"><?php echo $AppUI->_('Payments').'. '; ?></a>
				</div>
				
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Transaction amount'); ?></td>
							</tr>
						</thead>

						<tbody>
               				<tr>
								<td class="apm-label"><?php echo $topay_label; ?></td>
								<td><?php echo number_format(abs($topay), 2, ',', '').' '.$apmconfig['currency_symbol'];?></td>
							</tr>	
						</tbody>
					</table>
				</div>
				
				<?php
				if($topay>0)
				{
					?>
						<form name="payFrm" method="post" action="<?php echo $settingsPay['payment_action']; ?>">
						<input type="hidden" value="<?php echo $settingsPay['company_paymentid']; ?>" name="id">
						<input type="hidden" value="<?php echo $topay; ?>" name="kwota">
						<input type="hidden" value="<?php echo $AppUI->_('Invoice').' '.$object->invoice_name; ?>" name="opis">
						<input type="hidden" value="<?php echo $settingsPay['crc']; ?>" name="crc">
						<input type="hidden" value="<?php echo $settingsPay['md5sum']; ?>" name="md5sum">
						<input type="hidden" value="<?php echo $settingsPay['payment_verif']; ?>" name="wyn_url">
						<input type="hidden" value="<?php echo $settingsPay['payment_back']; ?>" name="pow_url">
						<input type="hidden" value="<?php echo $settingsPay['payment_back_error']; ?>" name="pow_url_blad">
						<input type="hidden" value="<?php echo $settingsPay['owner_firstname']; ?>" name="imie">
						<input type="hidden" value="<?php echo $settingsPay['owner_lastname']; ?>" name="nazwisko">
						<input type="hidden" value="<?php echo $settingsPay['owner_email']; ?>" name="email">

					
						<input class="btn btn-info" type="submit" value="<?php echo  $AppUI->_('Pay invoice'); ?>">
						</form>
						
						<form>
						<input class="btn btn-default" type="button" onclick="javascript:history.back(-1);" value="<?php echo $AppUI->_('Cancel transaction');?>">
						</form>
					<?php
				}
				else
				{
					?>
					<input class="btn btn-default" type="button" onclick="javascript:history.back(-1);" value="<?php echo $AppUI->_('Back');?>">
					<?php 	
				} ?>
				
	<?php }
	else 
	{?>
			<div class="alert alert-success">
			<span class="fa fa-alert fa-info-circle"></span>
			<a class="close" data-dismiss="alert" href="#">×</a>
			<?php echo $AppUI->_('You can not perform ePayment. Seller shall have access to the system ePayment.'); ?>
			</div>
	<?php }?>
				
			</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->



