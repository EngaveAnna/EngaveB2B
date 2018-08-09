<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
$invoice_id=( int ) apmgetParam ( $_GET, 'invoice_id', 0 );
?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->invoice_name) == 0 ? $AppUI->_('New invoice') : $AppUI->_('Invoice').': '.$object->invoice_name; ?>
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
								<td>
                				<?php echo $view->showField('invoice_total_pay', $object->invoice_total_pay); ?>
                				</td>
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

				
	            <div>

				<a class="btn btn-info" href="./index.php?m=invoices&a=do_invoice_pdf&id=<?php echo $object->invoice_id;?>&typePDF=tosign&suppressHeaders=true" /><span class="fa fa-file-pdf-o" aria-hidden="true" style="margin-right:5px;"></span><?php echo $AppUI->_ ( 'preview' );?></a>
	
	            </div>
				</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
