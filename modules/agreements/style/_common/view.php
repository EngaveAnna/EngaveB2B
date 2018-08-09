<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
$agreement_id=( int ) apmgetParam ( $_GET, 'agreement_id', 0 );
?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->agreement_name) == 0 ? $AppUI->_('New agreement') : $AppUI->_('Agreement').': '.$object->agreement_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Agreement title'); ?></td>
								 <td><?php echo $view->showField('agreement_name', $object->agreement_name); ?></td>
							</tr>

            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement status'); ?></td>
								<td><?php echo $view->showField('agreement_category', $agreementCategory[$object->agreement_category]); ?></td>
							</tr>
							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement place'); ?></td>
								<td><?php echo $view->showField('agreement_place', $object->agreement_place, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement date'); ?></td>
								<td><?php echo $view->showField('agreement_date', $object->agreement_date); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
							
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Duration of the contract'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement start date'); ?></td>
								<td><?php echo $view->showField('agreement_start_date', $object->agreement_start_date); ?></td>							</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement end date'); ?></td>
								<td><?php echo $view->showField('agreement_end_date', $object->agreement_end_date); ?></td>
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
							</tr>
						</thead>
						<tbody>
						    <tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement payment type'); ?></td>
								<td>
                				<?php echo $view->showField('agreement_payment_type', $agreementPaymnetType[$object->agreement_payment_type]); ?>
            					</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement payment amount'); ?></td>
								<td>
                				<?php echo $view->showField('agreement_payment_amount', $object->agreement_payment_amount); ?>
            					</td>
							</tr>																											
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Priority'); ?></td>
								<td>
                				<?php echo $view->showField('agreement_priority', $projectPriority[$object->agreement_priority]); ?>
                				</td>
							</tr>
		
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                					<?php echo $view->showField('agreement_description', $object->agreement_description); ?>
            					</td>
							</tr>
							

							<?php if ($agreement_id) { ?>
                			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td>
								<?php $view->showField('agreement_owner', $object->agreement_owner, array(), $users); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Created date'); ?></td>
								<td class="input-group date">
								<?php $view->showField('agreement_create_date', $object->agreement_create_date); ?>
								</td>
							</tr>
            					<?php } ?>
					
						</tbody>
					</table>
				</div>

				
	            <div>

				<a class="btn btn-info" href="./index.php?m=agreements&a=do_agreement_pdf&id=<?php echo $object->agreement_id;?>&typePDF=tosign&suppressHeaders=true" /><span class="fa fa-file-pdf-o" aria-hidden="true" style="margin-right:5px;"></span><?php echo $AppUI->_ ( 'preview' );?></a>
	
	            </div>
				</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
