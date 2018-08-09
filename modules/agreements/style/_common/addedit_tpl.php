<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$template_id=$object->getId();
?>
<form name="editFrm" action="?m=<?php echo $m; ?>&submod=template" method="post" accept-charset="utf-8" class="addedit agreements">
	<input type="hidden" name="dosql" value="do_agreement_aed_tpl" /> 
	<input type="hidden" name="template_id" value="<?php echo $template_id; ?>" />
	<!-- TODO: Right now, agreement owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="template_owner" value="<?php echo $object->template_owner; ?>" />
	<input type="hidden" name="datePicker" value="template" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->template_name) == 0 ? $AppUI->_('New template') : $AppUI->_('Template').': '.$object->template_name; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Template title'); ?></td>
								<td><?php $form->showField('template_name', $object->template_name, array('maxlength' => 255)); ?></td>
							</tr>

            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Template category'); ?></td>
								<td>
                					<?php $form->showField('template_category', $object->template_category, array(), $agreementTemplateCategory); ?>
            					</td>
							</tr>
							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Template date'); ?></td>
								<td class="input-group date">
									<?php $form->showField('template_date', $object->template_date); ?>
								</td>
							</tr>
							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td>
                					<?php $form->showField('template_description', $object->template_description); ?>
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
							<td>
							<span	class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Template creator'); ?>
							</td>
							</tr>
						</thead>

						<tbody>
						<tr>
						<td>
						<input type="hidden" name="template_source" id="template_source" value="<?php echo htmlspecialchars($object->template_source); ?>" />
						<textarea id="txtEditor"></textarea>
							<script type="text/javascript"> 
							<?php echo $lang_textEditor; ?>
							<?php echo $marker_textEditor; ?>
							var textEdBaseArea='<?php echo $textEdBaseArea;?>';
							var textEdSource='<?php echo $textEdSource;?>';
						
							function submitIt()
							{
								document.getElementById(textEdSource).value = document.getElementById(textEdBaseArea).innerHTML;
								editFrm.submit();
							}
							</script>
						</td>
						</tr>
						</tbody>
					</table>
				</div>
				
	            <div>
				<?php $form->showCancelButton(); $form->showSaveButton(); ?>     
	            </div>
	            
				</div>

		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
</form>