<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$agreement_id=( int ) apmgetParam ( $_GET, 'agreement_id', 0 );
?>
<form name="editFrm" method="post" accept-charset="utf-8" class="addedit agreements">
	<input type="hidden" name="dosql" id="dosql" value="do_agreement_aed" /> 
	<input type="hidden" name="agreement_id" value="<?php echo $agreement_id; ?>" />
	<!-- TODO: Right now, agreement owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="agreement_owner" value="<?php echo $object->agreement_owner; ?>" />
	<input type="hidden" name="datePicker" value="agreement" />
	<input type="hidden" name="pre" value="agreement" />
	<input type="hidden" name="typePDF" id="typePDF" value="preview" />
    <?php echo $form->addNonce(); ?>

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
								<td><?php $form->showField('agreement_name', $object->agreement_name, array('maxlength' => 255)); ?></td>
							</tr>

							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement place'); ?></td>
								<td><?php $form->showField('agreement_place', $object->agreement_place, array('maxlength' => 255)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('agreement_date', $object->agreement_date); ?>
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
								<td colspan="2"><span
									class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Agreement parties'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement parties owner'); ?></td>
								<td>
								<input type="hidden" name="agreement_parties_owner" id="agreement_parties_owner" value="<?php echo $object->agreement_parties_owner; ?>" />
								<?php
								$form->addAjaxModal($modId[2], $AppUI->_('Select parties owner')); 
								$btnModal_Onclk[2] ='javascript:getModalAjaxData(\''.$modId[2].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=companies&current=\'+getModalAjaxProcVar(\''.$procVar[2].'\',false)+\'&modId='.$modId[2].'&mult=true&procVar='.$procVar[2].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[2].'\');';
								$btnModal[2] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select company').'" onClick="'.$btnModal_Onclk[2].'">
								<span data-toggle="modal" data-target="#'.$modId[2].'">'.$AppUI->_('Select company').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[2]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[2].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[2], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement parties client'); ?></td>
								<td>
								<input type="hidden" name="agreement_parties_client" id="agreement_parties_client" value="<?php echo $object->agreement_parties_client; ?>" />
								<?php
								$form->addAjaxModal($modId[3], $AppUI->_('Select tasks')); 
								$btnModal_Onclk[3] ='javascript:getModalAjaxData(\''.$modId[3].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=companies&current=\'+getModalAjaxProcVar(\''.$procVar[3].'\',false)+\'&modId='.$modId[3].'&mult=true&procVar='.$procVar[3].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[3].'\');';
								$btnModal[3] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select company').'" onClick="'.$btnModal_Onclk[3].'">
								<span data-toggle="modal" data-target="#'.$modId[3].'">'.$AppUI->_('Select company').'</span></a>';
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
								<td colspan="2"><span
									class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Subject of the contract'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Projects'); ?></td>
								<td>
								<input type="hidden" name="agreement_project" id="agreement_project" value="<?php echo $object->agreement_project; ?>" />
								<?php
								$form->addAjaxModal($modId[0], $AppUI->_('Select projects')); 
								$btnModal_Onclk[0] ='javascript:getModalAjaxData(\''.$modId[0].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=projects&current=\'+getModalAjaxProcVar(\''.$procVar[0].'\',false)+\'&modId='.$modId[0].'&mult=true&procVar='.$procVar[0].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[0].'\');';
								$btnModal[0] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select projects').'" onClick="'.$btnModal_Onclk[0].'">
								<span data-toggle="modal" data-target="#'.$modId[0].'">'.$AppUI->_('Select projects').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[0]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[0].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[0], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Tasks'); ?></td>
								<td>
								<input type="hidden" name="agreement_task" id="agreement_task" value="<?php echo $object->agreement_task; ?>" />
								<?php
								$form->addAjaxModal($modId[1], $AppUI->_('Select tasks')); 
								$btnModal_Onclk[1] ='javascript:getModalAjaxData(\''.$modId[1].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=tasks&current=\'+getModalAjaxProcVar(\''.$procVar[1].'\',false)+\'&modId='.$modId[1].'&mult=true&procVar='.$procVar[1].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[1].'\');';
								$btnModal[1] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select tasks').'" onClick="'.$btnModal_Onclk[1].'">
								<span data-toggle="modal" data-target="#'.$modId[1].'">'.$AppUI->_('Select tasks').'</span></a>';
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
								<td class="input-group date">
								<?php $form->showField('agreement_start_date', $object->agreement_start_date); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement end date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('agreement_end_date', $object->agreement_end_date); ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Agreement payment type'); ?></td>
								<td>
                				<?php $form->showField('agreement_payment_type', $object->agreement_payment_type, array(), $agreementPaymnetType); ?>
            					</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Agreement payment amount'); ?></td>
								<td>
                				<?php $form->showField('agreement_payment_amount', $object->agreement_payment_amount); ?>
            					</td>
							</tr>																											
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Priority'); ?></td>
								<td>
                				<?php $form->showField('agreement_priority', (int) $object->agreement_priority, array(), $projectPriority); ?>
                				</td>
							</tr>
		
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                					<?php $form->showField('agreement_description', $object->agreement_description); ?>
            					</td>
							</tr>
							

							<?php if ($agreement_id) { ?>
                			<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td>
								<?php $form->showField('agreement_owner', $object->agreement_owner, array(), $users); ?>
								</td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Created date'); ?></td>
								<td class="input-group date">
								<?php $form->showField('agreement_create_date', $object->agreement_create_date); ?>
								</td>
							</tr>
            					<?php } ?>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Approval'); ?></td>
								<td>
								<input type="hidden" name="sign_u" id="sign_u" value="<?php echo $object->sign_u; ?>" />
								<?php
								$form->addAjaxModal($modId[4], $AppUI->_('Select users')); 
								$btnModal_Onclk[4] ='javascript:getModalAjaxData(\''.$modId[4].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=users&current=\'+getModalAjaxProcVar(\''.$procVar[4].'\',false)+\'&modId='.$modId[4].'&mult=true&procVar='.$procVar[4].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[4].'\');';
								$btnModal[4] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select users').'" onClick="'.$btnModal_Onclk[4].'">
								<span data-toggle="modal" data-target="#'.$modId[4].'">'.$AppUI->_('Select users').'</span></a>';
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
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Agreement template'); ?></td>
							</tr>
						</thead>
						<tbody>						
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Template'); ?></td>
								<td>
								<input type="hidden" name="agreement_template" id="agreement_template" value="<?php echo $object->agreement_template; ?>" />
								<?php
								$form->addAjaxModal($modId[5], $AppUI->_('Select template')); 
								$btnModal_Onclk[5] ='javascript:getModalAjaxData(\''.$modId[5].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=agreements_templates&current=\'+getModalAjaxProcVar(\''.$procVar[5].'\',false)+\'&modId='.$modId[5].'&mult=false&procVar='.$procVar[5].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[5].'\');';
								$btnModal[5] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select template').'" onClick="'.$btnModal_Onclk[5].'">
								<span data-toggle="modal" data-target="#'.$modId[5].'">'.$AppUI->_('Select template').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[5]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[5].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[5], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
							<tr>
								
								<td class="apm-label"><?php echo $AppUI->_('Compatibility'); ?></td>
								<td style="width:50%;">
									<input type="hidden" name="template_source" id="template_source" />
									<span><button type="button" class="btn btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" onClick="checkCompatibility(document.getElementById('template_source').value, 'agreement_template', document.getElementById('<?php echo $textEdBaseArea; ?>').innerHTML, 'compatibility');"><i class="fa fa-refresh"></i></button><p id="compatibility" style="float:left; margin-left:5px;"></p></span>
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
							<td>
							<span	class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Agreement creator'); ?>
							</td>
							</tr>
						</thead>

						<tbody>
						<tr>
						<td>
						<input type="hidden" name="agreement_source" id="agreement_source" value="<?php echo htmlspecialchars($object->agreement_source); ?>" />
						<textarea id="txtEditor"></textarea>
						<script type="text/javascript"> 
						<?php echo $lang_textEditor; ?>
						<?php echo $marker_textEditor; ?>
						var textEdBaseArea='<?php echo $textEdBaseArea;?>';
						var textEdSource='<?php echo $textEdSource;?>';

						</script>
						</td>
						</tr>
						</tbody>
					</table>
				</div>
				
	            <div>
					<?php $form->showCancelButton(); 
					echo '<input type="button" value="'.$AppUI->_ ( 'save' ) . '" class="btn btn-info" onclick="submitIt(\'\')" />';
					?>


				<button class="btn btn-default" onclick="submitIt('preview');" /><span class="fa fa-file-pdf-o" aria-hidden="true" style="margin-right:5px;"></span><?php echo $AppUI->_ ( 'preview' );?></button>
				</form>
					      
	            </div>
				</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->


<script src="./js/modalAjax.js" language="javascript" type="text/javascript"></script>
<script language="javascript" charset="utf-8" type="text/javascript">
function checkCompatibility(operand1, isset, operand2, elId)
{
	if(document.getElementById(isset).value != 0)
	{
		if(operand1==operand2)
		document.getElementById(elId).innerHTML="<label class=\"label label-default \"><i class=\"fa fa-check-circle fa-micro\"></i>"+"<?php echo $AppUI->_('Consistent data'); ?>"+"</label>";
		else
		document.getElementById(elId).innerHTML="<label class=\"label label-default\"><i class=\"fa fa-times fa-micro\"></i>"+"<?php echo $AppUI->_('Inconsistent data'); ?>"+"</label>";
	}
	else
	document.getElementById(elId).innerHTML="<?php echo $AppUI->_('No template to compare'); ?>";
	
	window.setTimeout('hideCompatibilityStatus(\''+elId+'\')', 2000);	
}

function hideCompatibilityStatus(elId)
{
	document.getElementById(elId).innerHTML="";
}

function submitIt(hid)
{
	if(hid=="preview")
	{
		editFrm.action='./index.php?m=agreements&a=do_agreement_pdf&suppressHeaders=true';
		document.getElementById('dosql').value="do_agreement_pdf";
		document.getElementById('typePDF').value="preview";
	}
	else
	{
		editFrm.action='?m=<?php echo $m; ?>';
		dosql.value='do_agreement_aed';
	}
	
	document.getElementById('<?php echo $textEdSource; ?>').value = document.getElementById('<?php echo $textEdBaseArea; ?>').innerHTML;
	editFrm.submit();
}
</script>