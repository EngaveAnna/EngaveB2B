<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

$date_reg = date ( 'Y-m-d' );
$start_date = intval ( $date_reg ) ? new apm_Utilities_Date ( apmgetParam ( $_REQUEST, 'log_start_date', date ( 'Y-m-d' ) ) ) : null;
$end_date = intval ( $date_reg ) ? new apm_Utilities_Date ( apmgetParam ( $_REQUEST, 'log_end_date', date ( 'Y-m-d' ) ) ) : null;
$user_id = ( int ) apmgetParam ( $_REQUEST, 'user_id', 0 );

global $AppUI, $currentTabId, $cal_sdf;
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
$currentTabId = ($AppUI->getState ( 'ProjIdxTab' ) !== null ? $AppUI->getState ( 'ProjIdxTab' ) : 0);

$a = ($user_id) ? '&a=view&user_id=' . $user_id : '';
$a .= '&tab=' . $currentTabId . '&showdetails=1';

$AppUI->getTheme ()->loadCalendarJS ();
?>
<script language="javascript" type="text/javascript">
function checkDate(){
    if (document.frmDate.log_start_date.value == '' || document.frmDate.log_end_date.value== ''){
        alert('<?php echo $AppUI->_('You must fill fields', UI_OUTPUT_JS) ?>');
        return false;
    }
    return true;
}
</script>



<div class="panel panel-default">
	<div class="panel-heading">
	<?php echo $AppUI->_('Form and list of logs'); ?>
    </div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Select log period'); ?></td>
						</tr>
					</thead>
					<tbody>


						<form action="index.php?m=users<?php echo $a; ?>" method="post"
							name="frmDate" accept-charset="utf-8">
							<input type="hidden" name="user_id" id="user_id"
								value="<?php echo $user_id; ?>" /> <input type="hidden"
								name="datePicker" value="log" />
							<tr>
								<td><?php echo $AppUI->_('Start Date'); ?></td>
								<td class="input-group date" align="left"><input type="hidden"
									name="log_start_date" id="log_start_date"
									value="<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
									<span class="input-group-addon"> <a href="javascript: void(0);"
										onclick="return showCalendar('start_date', '<?php echo $df ?>', 'frmDate', null, true, true)">
											<i class="glyphicon glyphicon-calendar btn-default"></i>
									</a></span> <input type="text" name="start_date"
									id="start_date"
									onchange="setDate_new('frmDate', 'start_date');"
									value="<?php echo $start_date ? $start_date->format($df) : ''; ?>"
									class="form-control" /></td>
							</tr>

							<tr>
								<td><?php echo $AppUI->_('End Date'); ?></td>
								<td class="input-group date" align="left"><input type="hidden"
									name="log_end_date" id="log_end_date"
									value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : ''; ?>" />
									<span class="input-group-addon"><a href="javascript: void(0);"
										onclick="return showCalendar('end_date', '<?php echo $df ?>', 'frmDate', null, true, true)">
											<i class="glyphicon glyphicon-calendar btn-default"></i>
									</a></span> <input type="text" name="end_date" id="end_date"
									onchange="setDate_new('frmDate', 'end_date');"
									value="<?php echo $end_date ? $end_date->format($df) : ''; ?>"
									class="form-control" /></td>
							</tr>


							<tr align="center">
								<td colspan="2"><input type="submit" class="btn btn-info"
									value="<?php echo $AppUI->_('Submit'); ?>"
									onclick="return checkDate('start','end')" /></td>
							</tr>
				
				</table>
				</form>


				</tbody>
				</table>
			</div>



<?php
if (apmgetParam ( $_REQUEST, 'showdetails', 0 ) == 1) {
	
	$fieldList = array (
			'user_username',
			'contact_last_name',
			'company_name',
			'date_time_in',
			'user_ip' 
	);
	$fieldNames = array (
			'First Name',
			'Last Name',
			'Internet Address',
			'Date Time IN',
			'Date Time OUT' 
	);
	
	$start_date = date ( 'Y-m-d', strtotime ( apmgetParam ( $_POST, 'log_start_date', date ( 'Y-m-d' ) ) ) );
	$start_date = $AppUI->convertToSystemTZ ( $start_date );
	$end_date = date ( 'Y-m-d 23:59:59', strtotime ( apmgetParam ( $_POST, 'log_end_date', date ( 'Y-m-d' ) ) ) );
	$end_date = $AppUI->convertToSystemTZ ( $end_date );
	$user_id = isset ( $user_id ) ? $user_id : 0;
	$user = new CUser ();
	
	$rows = $user->getLogList ( $user_id, $start_date, $end_date );
	$module = new apm_System_Module ();
	$listTable = new apm_Output_ListTable ( $AppUI );
	$fields = $module->loadSettings ( 'logs', 'index_list' );
	echo $listTable->startTable ();
	echo $listTable->buildHeader ( $fields, false, $m );
	
	?>
        <?php
	$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
	
	foreach ( $rows as $row ) {
		$htmlHelper->stageRowData ( $row );
		$item ['contact_first_name_na'] = $row ['contact_first_name'];
		$item ['contact_last_name_na'] = $row ['contact_last_name'];
		$item ['user_ip'] = $row ['user_ip'];
		$item ['log_in_datetime'] = $row ['date_time_in'];
		$item ['log_out_datetime'] = $row ['date_time_out'];
		$items [] = $item;
	}
	?>

    <?php
	
	echo $listTable->buildRows ( $items, $customLookups );
	echo $listTable->endTable ();
	
	?>
    
    
    <?php
}
// $form->showCancelButton(); $form->showSaveButton(); ?>
    </div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->
</form>