<?php
$form = new apm_Output_HTML_FormHelper ( $AppUI );
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
$signature_id=( int ) apmgetParam ( $_GET, 'signature_id', 0 );
?>

    <div class="panel panel-default">
	<div class="panel-heading"><?php echo $AppUI->_('View signature').' ID: '.$object->signature_id; ?>
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
								<td class="apm-label"><?php echo $AppUI->_('Signature name'); ?></td>
								<td><?php echo $view->showField('signature_name', $object->signature_name); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Date'); ?></td>
								<td><?php echo $view->showField('signature_date', $object->signature_date); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
								<td><?php echo $view->showField('signature_owner', $object->signature_owner); ?></td>
							</tr>
						</tbody>
					</table>
				</div>

				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

				<div class="alert alert-success">
				<span class="fa fa-alert fa-info-circle"></span>
				<a class="close" data-dismiss="alert" href="#">×</a>
				<?php echo $AppUI->_('If you would verify the sign you need sign source. You can download it here').': '; ?>
				<a href="./index.php?m=signatures&a=do_verification_file&signature_id=<?php echo $object->signature_id; ?>&suppressHeaders=true"><?php echo $AppUI->_('Sign source'); ?></a>
				<?php echo '. '.$AppUI->_('Safe the file and click button').' \''.$AppUI->_('Verify sign').'\'. ';?>
				</div>
				
			    <script src="http://www.java.com/js/deployJava.js"></script>
			    <script>
			        var attributes = { code:'com.signapplet.SignApplet.class', archive:'<?php echo apm_BASE_URL;?>/modules/<?php echo $m;?>/safesignatures/xmlsignappletmvn-2.46.jar', width:300, height:100} ;
			        var parameters = {
			        jnlp_href: '<?php echo apm_BASE_URL;?>/modules/<?php echo $m;?>/safesignatures/VerifyApplet.jnlp',
			        buttonName: '<?php echo $AppUI->_('Verify sign');?>',
			        isEnveloped: '1',
			        getURL: '',
			        postURL: '',
			        signerType: 'DEFAULT'
			        } ;
			        deployJava.runApplet(attributes, parameters, '1.8');
			    </script>
				
				</div>
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->


