<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$signature_id=( int ) apmgetParam ( $_GET, 'signature_id', 0 );
?>
<form name="editFrm" method="post" accept-charset="utf-8" class="addedit signatures">
	<input type="hidden" name="dosql" id="dosql" value="do_signature_aed" /> 
	<input type="hidden" name="signature_id" value="<?php echo $signature_id; ?>" />
	<!-- TODO: Right now, signature owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="signature_owner" value="<?php echo $object->signature_owner; ?>" />
	<input type="hidden" name="datePicker" value="signature" />
	<input type="hidden" name="pre" value="signature" />
	<input type="hidden" name="typePDF" id="typePDF" value="preview" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->signature_name) == 0 ? $AppUI->_('New signature') : $AppUI->_('Signature id').': '.$object->signature_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Signature title'); ?></td>
								<td><?php $form->showField('signature_name', $object->signature_name, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Signature place'); ?></td>
								<td><?php $form->showField('signature_place', $object->signature_place, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Signature date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('signature_date', $object->signature_date); ?>
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Duration of the contract'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Signature start date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('signature_start_date', $object->signature_start_date); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Signature end date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('signature_end_date', $object->signature_end_date); ?>
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                					<?php $form->showField('signature_description', $object->signature_description); ?>
            					</td>
							</tr>
		

							<?php if ($signature_id) { ?>
                			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td>
								<?php $form->showField('signature_owner', $object->signature_owner, array(), $users); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Created date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('signature_create_date', $object->signature_create_date); ?>
								</td>
							</tr>
            					<?php } ?>
						
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
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Signature source'); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Source'); ?></td>
								<td>
                					<?php $form->showField('signature_source', $object->signature_source); ?>
            					</td>
							</tr>
						</tbody>
					</table>
				</div>	
				

				
	            <div>
					<?php $form->showCancelButton(); $form->showSaveButton()
				?>
				</form>
					      
	            </div>
				</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->


<script src="./js/modalAjax.js" language="javascript" type="text/javascript"></script>
