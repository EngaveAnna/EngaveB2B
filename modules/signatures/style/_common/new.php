<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
$pid=md5(apmgetConfig ( 'sign_pid' ).$signature_mod_name.'m'.$signature_row.'c'.$AppUI->user_id);

?>
<form name="editFrm" method="post" accept-charset="utf-8" class="addedit signatures">
	<input type="hidden" name="signature_id" value="<?php echo $signature_id; ?>" />
	<!-- TODO: Right now, signature owner is hard coded, we should make this a select box like elsewhere. -->
	<input type="hidden" name="signature_owner" value="<?php echo $object->signature_owner; ?>" />
	<input type="hidden" name="datePicker" value="signature" />
    <?php echo $form->addNonce(); ?>
    <div class="panel panel-default">
	<div class="panel-heading"><?php echo $AppUI->_('Signing document'); ?> </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="alert alert-success">
				<span class="fa fa-alert fa-info-circle"></span>
				<a class="close" data-dismiss="alert" href="#">×</a>
				<?php echo $AppUI->_('Activate hardware and push in sign card. Select right certificate and accept process using button').' \''.$AppUI->_('Sign it').'\'. ';?>
				</div>			
   								<script  language="javascript" type="text/javascript" src="http://www.java.com/js/deployJava.js"></script>
							    <script>
							        var attributes = { code:'com.signapplet.SignApplet.class', archive:'<?php echo apm_BASE_URL;?>/modules/<?php echo $m;?>/safesignatures/xmlsignappletmvn-2.46.jar', width:400, height:100} ;
							        var parameters = {
							         jnlp_href: '<?php echo apm_BASE_URL;?>/modules/<?php echo $m;?>/safesignatures/SignApplet.jnlp',
							         buttonName: '<?php echo $AppUI->_('Sign it') ?>',
							         isEnveloped: '1',
							         getURL: '<?php echo apm_BASE_URL;?>/index.php?m=<?php echo $m;?>&a=do_get_signdata&mod=<?php echo $signature_mod_name;?>&id=<?php echo $signature_row;?>&oid=<?php echo $AppUI->user_id;?>&suppressHeaders=true&pid=<?php echo $pid; ?>',
							         postURL: '<?php echo apm_BASE_URL;?>/index.php?m=<?php echo $m;?>&a=do_post_signdata&mod=<?php echo $signature_mod_name;?>&id=<?php echo $signature_row;?>&oid=<?php echo $AppUI->user_id;?>&suppressHeaders=true&pid=<?php echo $pid; ?>',
							         signerType: 'DEFAULT'
							         };
							        deployJava.runApplet(attributes, parameters, '1.8');
							    </script>
							    
							    <script>
							        function redirect(val1, val2) {
							            document.myForm.val1.value = val1;
							            document.myForm.val2.value = val2;
							            document.myForm.submit();
							        }
							    </script>
							
							    <form name='myForm' id='myForm' action="http://posttestserver.com/post.php?dir=signiningTest1" method="post">
							        <Input id='val1' name='val1' type='hidden'/>
							        <Input id='val2' name='val2' type='hidden'/>
							    </form>								


				</div>
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
								<td class="apm-label"><?php echo $AppUI->_('Module'); ?></td>
								<td><?php echo $view->showField('signature_mod', $AppUI->_($signature_mod_name)); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td><?php echo $view->showField('signature_owner', $AppUI->user_id); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div>
				<a class="btn btn-info" href="./index.php?m=<?php echo $signature_mod_name; ?>&a=do_<?php echo $signature_mod_prefix; ?>_pdf&id=<?php echo $signature_row;?>&typePDF=tosign&suppressHeaders=true" /><span class="fa fa-file-pdf-o" aria-hidden="true" style="margin-right:5px;"></span><?php echo $AppUI->_ ( 'preview' );?></a>
	            </div>

				</div>

				

				
				</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->


<script src="./js/modalAjax.js" language="javascript" type="text/javascript"></script>
