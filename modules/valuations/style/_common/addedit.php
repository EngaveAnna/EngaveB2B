<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
?>
<form name="editFrm" action="?m=<?php echo $m; ?>" method="post" accept-charset="utf-8" class="addedit valuations">
	<input type="hidden" name="dosql" value="do_valuation_aed" /> 
	<input	type="hidden" name="valuation_id" value="<?php echo $object->getId(); ?>" />
	<!-- TODO: Right now, valuation owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="valuation_owner" value="<?php echo $object->valuation_owner; ?>" />
	<input type="hidden" name="datePicker" value="valuation" />
    <?php echo $form->addNonce(); ?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo strlen($object->valuation_name) == 0 ? $AppUI->__('New valuation') : $AppUI->__('Valuation').': '.$object->valuation_name; ?>
    </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Basic information'); ?></td>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation title'); ?></td>
								<td><?php echo $form->showField('valuation_name', $object->valuation_name); ?></td>
							</tr>
							<?php if($object->valuation_id)	{ ?>
								<tr><td class="apm-label"><?php echo $AppUI->_('Valuation type'); ?></td>
								<td><?php echo $form->showField('valuation_category', $valuationType[$object->valuation_category], array(), $valuationType); ?></td></tr>
							<?php } else { ?>
							<input type="hidden" name="valuation_category" value="0" />
							<?php } ?>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation owner'); ?></td>
								<td><?php echo $form->showField('valuation_owner', $AppUI->user_id, array(), $users); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation desc'); ?></td>
								<td><?php echo $form->showField('valuation_desc', $object->valuation_desc); ?></td>
							</tr>							
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Project'); ?></td>
								<td>
								<input type="hidden" name="valuation_project" id="valuation_project" value="<?php echo $object->valuation_project; ?>" />
								<?php
								$form->addAjaxModal($modId[0], $AppUI->_('Select project')); 
								$btnModal_Onclk[0] ='javascript:getModalAjaxData(\''.$modId[0].'\', \'./index.php?m=public&a=modalajax&suppressHeaders=true&t=selector&que=valuationproject&current=\'+getModalAjaxProcVar(\''.$procVar[0].'\',false)+\'&modId='.$modId[0].'&mult=false&procVar='.$procVar[0].'\'); modalAjaxLoading(\''.$AppUI->_('Loading').'\', \''.$modId[0].'\');';
								$btnModal[0] = '<a type="button" class="btn-default btn-module-nav" data-original-title="" data-container="body" data-toggle="tooltip" data-placement="right" value="'.$AppUI->_('Select project').'" onClick="'.$btnModal_Onclk[0].'">
								<span data-toggle="modal" data-target="#'.$modId[0].'">'.$AppUI->_('Select project').'</span></a>';
								?>
								<p style="float: left; width: 100%;"><?php echo $btnModal[0]; ?></p></br>							
								<span style="clear:both;" id="<?php echo $modId[0].'_area'; ?>">
								<?php $form->showField('_ajaxList', $ajaxList[0], array('id', array('prefix'=>'ID: ', 'field'=>'elemId'))); ?>
								</span>
								</td>
							</tr>
							<?php if($object->valuation_id) { ?>
								<tr><td class="apm-label"><?php echo $AppUI->_('Valuation days');?></td><td><?php echo $form->showField('valuation_days', $object->valuation_days); ?></td></tr>
								<tr><td class="apm-label"><?php echo $AppUI->_('Valuation amount');?></td><td><?php echo $form->showField( 'valuation_amount', $object->valuation_amount); ?></td></tr>
							<?php } ?>			

						</tbody>
					</table>
				</div>
	

	<?php $form->showCancelButton(); 
	$subTitle=(!$object->valuation_id)?$AppUI->_('save'):$AppUI->_('actualize');
	echo '<input class="btn btn-info" type="button" onclick="submitIt()" value="'.$subTitle.'">';?>	
    </div>
    
    		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<?php if($object->valuation_project) {?>

				<?php } else {?>
				<div class="alert alert-success">
				<span class="fa fa-alert fa-info-circle"></span>
				<a class="close" href="#" data-dismiss="alert">×</a>
				<?php echo $AppUI->_('Automated valuation system, requires that the project to estimate. Create a project and refer to quantify. The project should have the status "The proposed"');?>
				</div>
				<?php } ?>
			</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->
</form>
<script src="./js/modalAjax.js" language="javascript" type="text/javascript"></script>
<script language="javascript" charset="utf-8" type="text/javascript">
function submitIt(hid)
{
	editFrm.action='?m=<?php echo $m; ?>';
	editFrm.submit();
}
</script>