<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
global $AppUI, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();

$budget_id = ( int ) apmgetParam ( $_GET, 'budget_id', 0 );

$canEdit = canEdit ( 'system' );
$canDelete = canView ( 'system' );
if (! $canEdit) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

// get a list of permitted companies
$company = new CCompany ();
$companies = $company->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$companies = arrayMerge ( array (
		'0' => $AppUI->_ ( 'None specified' ) 
), $companies );

// load the record data
$budget = new CSystem_Budget ();
$budget->load ( $budget_id );

$titleBlock = new apm_Theme_TitleBlock ( 'Setup Budgets', 'myevo-weather.png', $m );
$titleBlock->addCrumb ( '?m=system', 'system admin' );
$titleBlock->show ();
?>
<script language="javascript" type="text/javascript">
	function submitIt(){
		document.frmAddcode.submit();
	}
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
	?>
function delIt(input) {
	if (confirm( '<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS) . ' ' . $AppUI->_('Budget', UI_OUTPUT_JS) . '?'; ?>' )) {
		document.frmDelete.budget_id.value = input;
        document.frmDelete.submit();
	}
}
<?php } ?>
</script>
<form name="frmDelete" action="./index.php?m=system" method="post" accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_budgeting_aed" /> <input
		type="hidden" name="del" value="1" /> <input type="hidden"
		name="budget_id" value="0" />
</form>
<?php $form = new apm_Output_HTML_FormHelper ( $AppUI ); ?>
<form name="frmAddcode" action="./index.php?m=system" method="post"	accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_budgeting_aed" /> <input
		type="hidden" name="budget_id" value="<?php echo $budget_id; ?>" /> <input
		type="hidden" name="datePicker" value="budget" />
    <?php
				$fieldList = array (
						'company_name',
						'budget_start_date',
						'budget_end_date',
						'budget_amount',
						'budget_category' 
				);
				$fieldNames = array (
						'Company',
						'Start Date',
						'End Date',
						'Amount',
						'Billing Category' 
				);
				
				$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
				$budgetCategory = apmgetSysVal ( 'BudgetCategory' );
				$customLookups = array (
						'budget_category' => $budgetCategory 
				);
				
				?>
				
				
				
				
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
						<thead>
						<tr>
	            			<?php foreach ($fieldNames as $index => $name) { ?>
	                			<th><?php echo $AppUI->_($fieldNames[$index]); ?></th>
	            			<?php } ?>
	            			<th><?php echo $AppUI->_('Options');?></th>
            			</tr>
						</thead>				
						<tbody>
				        <?php
								$budgets = $budget->getBudgetAmounts ();
								foreach ( $budgets as $row )
								{
									echo '<tr>';
									
									$htmlHelper->stageRowData ( $row );
									foreach ( $fieldList as $index => $column ) {
										echo $htmlHelper->createCell ( $fieldList [$index], $row [$fieldList [$index]], $customLookups );
									}
									
									echo '<td>';
									if ($canEdit) {
										echo '<a class="btn btn-xs btn-info" href="?m=system&a=budgeting&budget_id=' . $row ['budget_id'] . '" role="button"><span class="glyphicon glyphicon-edit"></span></a>';
									}
									if ($canDelete) {
										echo '<a class="btn btn-xs btn-default" href="javascript:delIt(' . $row ['budget_id'] . ')" role="button"><span class="glyphicon glyphicon-remove text-danger"></span></a>';
									}
									echo '</td>';
									echo '</tr>';
								}
						?>						

						</tbody>
					</table>
				</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
					<thead>
							<tr>
								<td><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Edit budgeting amount'); ?></td>
							</tr>
					</thead>
					<tbody>
						<tr>
						<td align="center">
			                <?php echo arraySelect ( $companies, 'budget_company', 'size="1" class="form-control"', $budget->budget_company, false ); ?>
						</td>
						</tr>
						<tr>
						<td align="input-group date" class="input-group date"><input type="hidden" name="budget_start_date"
							id="budget_start_date"
							value="<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
							<input type="text" name="start_date" id="start_date"
							onchange="setDate_new('frmAddcode', 'start_date');"
							value="<?php echo $start_date ? $start_date->format($df) : ''; ?>"
							class="form-control" /> <span class="input-group-addon"><a href="javascript: void(0);"
							onclick="return showCalendar('start_date', '<?php echo $df ?>', 'frmAddcode', null, true, true)">
								<i class="glyphicon glyphicon-calendar btn-default"></i></a></span>
						</td>
						</tr>
						<tr>
						<td align="input-group date" class="input-group date"><input type="hidden" name="budget_end_date"
							id="budget_end_date"
							value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
							<input type="text" name="end_date" id="end_date"
							onchange="setDate_new('frmAddcode', 'end_date');"
							value="<?php echo $end_date ? $end_date->format($df) : ''; ?>"
							class="form-control" /> <span class="input-group-addon"><a href="javascript: void(0);"
							onclick="return showCalendar('end_date', '<?php echo $df ?>', 'frmAddcode', null, true, true)">
								<i class="glyphicon glyphicon-calendar btn-default"></i>
							
						</a></span></td>
						</tr>
						<tr>						
						<td align="center"><input type="text" class="form-control"
							name="budget_amount" value="<?php echo $budget->budget_amount; ?>"
							size="10" /></td>
						</tr>
						<tr>							
						<td align="center">
			                <?php echo arraySelect ( $budgetCategory, 'budget_category', 'size="1" class="form-control"', $budget->budget_category, false ); ?>
						</td>
						</tr>
					</tbody>
				</table>
				</div>
		
				<div>
					<td align="left">
		                <?php $form->showCancelButton(); ?>
		            </td>
					<td align="right" width="20"><input class="btn btn-info" type="button" value="<?php echo $AppUI->_('save'); ?>" onclick="submitIt()" /></td>
				</div>
				</div>

</form>						
