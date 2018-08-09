<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$company_id = ( int ) apmgetParam ( $_POST, 'company_id', - 1 );
$billingcode_id = ( int ) apmgetParam ( $_GET, 'billingcode_id', 0 );

if (! canEdit ( 'system' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$bcode = new CSystem_Bcode ();
$bcode->load ( $billingcode_id );
$billingcodes = $bcode->getBillingCodes ( $company_id, false );

// get a list of permitted companies
$company = new CCompany ();
$companies = $company->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$companies = arrayMerge ( array (
		'0' => $AppUI->_ ( 'None specified' ) 
), $companies );
$companies = arrayMerge ( array (
		'-1' => $AppUI->_ ( 'All Codes' ) 
), $companies );
$billingCategory = apmgetSysVal ( 'BudgetCategory' );
$billingCategory = arrayMerge ( array (
		'0' => $AppUI->_ ( 'None specified' ) 
), $billingCategory );

$titleBlock = new apm_Theme_TitleBlock ( 'Edit Billing Codes', 'myevo-weather.png', $m );
$titleBlock->addCrumb ( '?m=system', 'system admin' );
$titleBlock->show ();
?>
<script language="javascript" type="text/javascript">
<!--
function submitIt(){
	var form = document.frmAddcode;
	form.submit();
}

function changeIt() {
	var f=document.changeMe;
    document.getElementById('company_filter').value = document.getElementById('company_id').value;
	var msg = '';
	f.submit();
}


function delIt2(id) {
	document.frmDel.billingcode_id.value = id;
	document.frmDel.submit();
}
-->
</script>




<form name="frmDel" action="./index.php?m=system" method="post"
	accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_billingcode_aed" /> <input
		type="hidden" name="del" value="1" /> <input type="hidden"
		name="company_id" value="<?php echo $company_id; ?>" /> <input
		type="hidden" name="billingcode_id" value="" />
</form>
<form name="changeMe" action="./index.php?m=system&amp;a=billingcode"
	method="post" accept-charset="utf-8">
	<input type="hidden" name="company_id" id="company_filter" value="" />
</form>
<?php

$form = new apm_Output_HTML_FormHelper ( $AppUI );

?>
<form name="frmAddcode" action="./index.php?m=system" method="post"
	accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_billingcode_aed" /> <input
		type="hidden" name="del" value="0" /> <input type="hidden"
		name="billingcode_status" value="0" />

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<th><?php echo $AppUI->_('Company'); ?></th>
								<th><?php echo $AppUI->_('Billing Code'); ?></th>
								<th><?php echo $AppUI->_('Value'); ?></th>
								<th><?php echo $AppUI->_('Description'); ?></th>
								<th><?php echo $AppUI->_('Billing Category'); ?></th>
								<th><?php echo $AppUI->_('Options'); ?></th>
							</tr>
						</thead>

						<tbody>
				        <?php
							foreach ( $billingcodes as $code ) { ?><tr>

							<td align="left">&nbsp;<?php echo (('' != $code['company_name']) ? $code['company_name'] : $AppUI->_('None specified')); ?></td>
							<td align="left">&nbsp;<?php echo $code['billingcode_name'] . ($code['billingcode_status'] == 1 ? ' (deleted)' : ''); ?></td>
							<td nowrap="nowrap" align="center"><?php echo $code['billingcode_value']; ?></td>
							<td nowrap="nowrap"><?php echo $code['billingcode_desc']; ?></td>
							<td nowrap="nowrap"><?php echo $AppUI->_($billingCategory[$code['billingcode_category']]); ?></td>
							<td>
							<a class="btn btn-xs btn-info" href="?m=system&a=billingcode&billingcode_id=<?php echo $code['billingcode_id']; ?>" role="button">
							<span class="glyphicon glyphicon-edit"></span>
							</a>							
				            <?php if (!$code['billingcode_status']) { ?>
				            <a class="btn btn-xs btn-default" onclick="if (confirm('Na pewno usunąć kod?')) {document.frm_remove_payment_195.submit()}" href="javascript:delIt2(<?php echo $code['billingcode_id']; ?>);" role="button">
							<span class="glyphicon glyphicon-remove text-danger"></span></a>
							<?php } ?>
				            </td>
							</tr>
									
							<?php } ?>
						</tbody>
					</table>
				</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
					<thead>
							<tr>
								<td><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Edit code'); ?></td>
							</tr>
					</thead>
					<tbody>
					<input type="hidden" name="billingcode_id" value="<?php echo $billingcode_id; ?>" />
					<tr>	
						<td>
	
			            <?php unset ( $companies [- 1] );
						echo arraySelect ( $companies, 'billingcode_company', 'size="1" class="form-control"', $bcode->billingcode_company, false );?>
			            </td>
			        </tr>
					<tr>
						<td><input type="text" class="form-control" name="billingcode_name"
							value="<?php echo $bcode->billingcode_name; ?>" /></td>
					</tr>
					<tr>
						<td align="center"><input type="text" class="form-control"
							name="billingcode_value"
							value="<?php echo $bcode->billingcode_value; ?>" size="7" /></td>
					</tr>
					<tr>
						<td><input type="text" class="form-control" name="billingcode_desc"
							value="<?php echo $bcode->billingcode_desc; ?>" /></td>
					</tr>
					<tr>
						<td>
			            <?php echo arraySelect ( $billingCategory, 'billingcode_category', 'size="1" class="form-control"', $bcode->billingcode_category, false );?>
			            </td>
					</tr>
					</tbody>
				</table>
				</div>
		
				<div>
					<td align="left">
		                <?php $form->showCancelButton(); ?>
		            </td>
					<td colspan="3" align="right"><input type="button" class="btn btn-info" value="<?php echo $AppUI->_('save'); ?>"
						onclick="submitIt()" /></td>
				</div>
				</div>

</form>

