<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
$invoice_id=( int ) apmgetParam ( $_GET, 'invoice_id', 0 );
?>
<form name="editFrm" method="post" accept-charset="utf-8" class="addedit invoices">
	<input type="hidden" name="dosql" id="dosql" value="do_invoice_aed" /> 
	<input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>" />
	<!-- TODO: Right now, invoice owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="invoice_owner" value="<?php echo $object->invoice_owner; ?>" />
	<input type="hidden" name="datePicker" value="invoice" />
	<input type="hidden" name="pre" value="invoice" />
	<input type="hidden" name="typePDF" id="typePDF" value="preview" />
    <?php echo $form->addNonce(); ?>

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
								<td><?php $form->showField('invoice_name', $object->invoice_name, array('maxlength' => 255)); ?></td>
							</tr>

							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice place'); ?></td>
								<td><?php $form->showField('invoice_place', $object->invoice_place, array('maxlength' => 255)); ?></td>
							</tr>
						</tbody>
					</table>
				</div>

				
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span
									class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Invoice parties'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice parties owner'); ?></td>
								<td>
								<input type="hidden" name="invoice_parties_owner" id="invoice_parties_owner" value="<?php echo $object->invoice_parties_owner; ?>" />
								<?php
								$form->addAjaxModal($modId[0], $AppUI->_('Select parties owner')); 
								$btnModal_Onclk[0] ='javascript:getModalAjaxData(\''.$modId[0].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=companies&current=\'+getModalAjaxProcVar(\''.$procVar[0].'\',false)+\'&modId='.$modId[0].'&mult=false&procVar='.$procVar[0].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[0].'\');';
								$btnModal[0] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('select parties owner').'" onClick="'.$btnModal_Onclk[0].'">
								<span data-toggle="modal" data-target="#'.$modId[0].'">'.$AppUI->_('select parties owner').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[0]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[0].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[0], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice parties client'); ?></td>
								<td>
								<input type="hidden" name="invoice_parties_client" id="invoice_parties_client" value="<?php echo $object->invoice_parties_client; ?>" />
								<?php
								$form->addAjaxModal($modId[1], $AppUI->_('select parties client')); 
								$btnModal_Onclk[1] ='javascript:getModalAjaxData(\''.$modId[1].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=companies&current=\'+getModalAjaxProcVar(\''.$procVar[1].'\',false)+\'&modId='.$modId[1].'&mult=false&procVar='.$procVar[1].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[1].'\');';
								$btnModal[1] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('select parties client').'" onClick="'.$btnModal_Onclk[1].'">
								<span data-toggle="modal" data-target="#'.$modId[1].'">'.$AppUI->_('select parties client').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[1]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[1].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[1], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
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
									<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Invoice dates'); ?></td>
								</tr>
							</thead>
	
							<tbody>
								<tr>
									<td class="apm-label"><?php echo $AppUI->_('Date issue'); ?></td>
									<td class="input-group date">
									<?php $form->showField('invoice_issue_date', $object->invoice_issue_date); ?>
									</td>
								</tr>
								<tr>
									<td class="apm-label"><?php echo $AppUI->_('Date sale'); ?></td>
									<td class="input-group date">
									<?php $form->showField('invoice_sale_date', $object->invoice_sale_date); ?>
									</td>
								</tr>
								<tr>
									<td class="apm-label"><?php echo $AppUI->_('Date pay'); ?></td>
									<td class="input-group date">
									<?php $form->showField('invoice_pay_date', $object->invoice_pay_date); ?>
									</td>
								</tr>							
							</tbody>
						</table>
					</div>				

				</div>

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="invoice-items">
					<div class="table-responsive " style="margin-bottom:5px;">
						<table class="table table-bordered table-striped table-responsive" id="tab_logic">
						<thead>
							<?php echo $tableItemHead;	?>
						</thead>
						<tbody>
							<?php echo $tableItemBody;	?>
						</tbody>
						</table>
					</div>
					
					
					<div style="width:100%;">
					<table >
					<tr>
					<td style="float:left;">
						<p style="float: left; width: 100%;">
						<a id="add_row" class="btn-default btn-module-nav"><?php echo $AppUI->_('Add empty row');?></a>
						</p>
					</td>
					</tr>
					</table>
					</div>
				</div>	
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%" class="table table-bordered table-striped table-static" >
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Payment details'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice payed'); ?></td>
								<td>
                				<?php $form->showField('invoice_payed', $object->invoice_payed); ?><button onclick="" id="payed" class="btn btn-default"><span class="glyphicon glyphicon-ok icon-left"></span><?php echo $AppUI->_('payed');?></button>
            					</td>
							</tr>	
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice payment amount'); ?></td>
								<td>
                				<?php $form->showField('invoice_payment_amount', $object->invoice_payment_amount, array('disabled'=>true)); ?>
            					</td>
							</tr>
						    <tr>
								<td class="apm-label"><?php echo $AppUI->_('Invoice payment type'); ?></td>
								<td>
                				<?php $form->showField('invoice_payment_type', $object->invoice_payment_type, array(), $invoicePaymnetType); ?>
            					</td>
							</tr>							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Bank account'); ?></td>
								<td>
                				<?php $form->showField('invoice_bank_account', $object->invoice_bank_account); ?>
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Payment details'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Person authorized to issue an invoice'); ?></td>
								<td>
								<input type="hidden" name="invoice_authorized_issue" id="invoice_authorized_issue" value="<?php echo $object->invoice_authorized_issue; ?>" />
								<?php
								$form->addAjaxModal($modId[2], $AppUI->_('Select user')); 
								$btnModal_Onclk[2] ='javascript:getModalAjaxData(\''.$modId[2].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=users&current=\'+getModalAjaxProcVar(\''.$procVar[2].'\',false)+\'&modId='.$modId[2].'&mult=false&procVar='.$procVar[2].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[2].'\');';
								$btnModal[2] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('select user').'" onClick="'.$btnModal_Onclk[2].'">
								<span data-toggle="modal" data-target="#'.$modId[2].'">'.$AppUI->_('select user').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[2]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[2].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[2], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Person authorized to receive invoices'); ?></td>
								<td>
								<input type="hidden" name="invoice_authorized_receive" id="invoice_authorized_receive" value="<?php echo $object->invoice_authorized_receive; ?>" />
								<?php
								$form->addAjaxModal($modId[3], $AppUI->_('Select user')); 
								$btnModal_Onclk[3] ='javascript:getModalAjaxData(\''.$modId[3].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=users&current=\'+getModalAjaxProcVar(\''.$procVar[3].'\',false)+\'&modId='.$modId[3].'&mult=false&procVar='.$procVar[3].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[3].'\');';
								$btnModal[3] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('select user').'" onClick="'.$btnModal_Onclk[3].'">
								<span data-toggle="modal" data-target="#'.$modId[3].'">'.$AppUI->_('select user').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[3]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[3].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[3], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Invoice template'); ?></td>
							</tr>
						</thead>
						<tbody>						
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Template'); ?></td>
								<td>
								<input type="hidden" name="invoice_template" id="invoice_template" value="<?php echo $object->invoice_template; ?>" />
								<?php
								$form->addAjaxModal($modId[4], $AppUI->_('Select template')); 
								$btnModal_Onclk[4] ='javascript:getModalAjaxData(\''.$modId[4].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=invoices_templates&current=\'+getModalAjaxProcVar(\''.$procVar[4].'\',false)+\'&modId='.$modId[4].'&mult=false&procVar='.$procVar[4].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[4].'\');';
								$btnModal[4] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('select template').'" onClick="'.$btnModal_Onclk[4].'">
								<span data-toggle="modal" data-target="#'.$modId[4].'">'.$AppUI->_('select template').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[4]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[4].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[4], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<?php if ($invoice_id) { ?>				
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%" class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Properties'); ?></td>
							</tr>
						</thead>
						<tbody>						
                			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td>
								<?php $view->showField('invoice_owner', $object->invoice_owner, array(), $users); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Created date'); ?></td>
								<td class="input-group date">
								<?php $view->showField('invoice_create_date', $object->invoice_create_date); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
            	<?php } ?>	
								
				<div>
					<?php $form->showCancelButton(); 
					echo '<input type="button" value="'.$AppUI->_ ( 'save' ) . '" class="btn btn-info" onclick="submitIt(\'\')" />';
					?>
				<button class="btn btn-default" onclick="submitIt('preview');" /><span class="fa fa-file-pdf-o" aria-hidden="true" style="margin-right:5px;"></span><?php echo $AppUI->_ ( 'preview' );?></button>
				</form>
	            </div>
	            
				</div>


				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Invoice description'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                					<?php $form->showField('invoice_description', $object->invoice_description); ?>
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


<script src="./js/modalAjax.js" language="javascript" type="text/javascript"></script>
<script language="javascript" charset="utf-8" type="text/javascript">

function submitIt(hid)
{
	if(hid=="preview")
	{
		editFrm.action='./index.php?m=invoices&a=do_invoice_pdf&suppressHeaders=true';
		document.getElementById('dosql').value="do_invoice_pdf";
		document.getElementById('typePDF').value="preview";
	}
	else
	{
		editFrm.action='?m=<?php echo $m; ?>';
		dosql.value='do_invoice_aed';
	}
	

	editFrm.submit();
}
</script>