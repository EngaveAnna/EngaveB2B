<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// check permissions
if (! canEdit ( 'system' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$reset = ( int ) apmgetParam ( $_GET, 'reset', 0 );
if ($reset == 1) {
	$obj = &$AppUI->acl ();
	$obj->recalcPermissions ();
}

$apmcfg = new apm_System_Config ();

// retrieve the system configuration data
$rs = $apmcfg->loadAll ( 'config_group' );

$tab = $AppUI->processIntState ( 'ConfigIdxTab', $_GET, 'tab', 0 );

$active = intval ( ! $AppUI->getState ( 'ConfigIdxTab' ) );

$titleBlock = new apm_Theme_TitleBlock ( 'System Configuration', 'control-center.png', $m );
$titleBlock->addCrumb ( '?m=system', 'system admin' );
$titleBlock->addCrumb ( '?m=system&a=addeditpref', 'default user preferences' );
$titleBlock->show ();

// prepare the automated form fields based on db system configuration data
$output = null;
$last_group = '';
$i = 0;
$k = floor ( count ( $rs ) / 2 );
$secCol = true;

// echo "<pre>";
// print_r($rs);
// echo "</pre>";

foreach ( $rs as $c ) {
	$i ++;
	$tooltip = $AppUI->_ ( $c ['config_name'] . '_tooltip' );
	// extraparse the checkboxes and the select lists
	$extra = '';
	$value = '';
	switch ($c ['config_type']) {
		case 'select' :
			// Build the select list.
			if ($c ['config_name'] == 'system_timezone') {
				$timezones = apmgetSysVal ( 'Timezones' );
				$entry = arraySelect ( $timezones, 'apmcfg[system_timezone]', 'class=text size=1', apmgetConfig ( 'system_timezone' ), true );
			} else {
				$entry = '<select class="form-control" name="apmcfg[' . $c ['config_name'] . ']">';
				// Find the detail relating to this entry.
				$children = $apmcfg->getChildren ( $c ['config_id'] );
				foreach ( $children as $child ) {
					$entry .= '<option value="' . $child ['config_list_name'] . '"';
					if ($child ['config_list_name'] == $c ['config_value']) {
						$entry .= ' selected="selected"';
					}
					$entry .= '>' . $AppUI->_ ( $child ['config_list_name'] . '_item_title' ) . '</option>';
				}
				$entry .= '</select>';
			}
			break;
		case 'checkbox' :
			$extra = ($c ['config_value'] == 'true') ? 'checked="checked"' : '';
			$value = 'true';
		// allow to fallthrough
		default :
			if (! $value) {
				$value = $c ['config_value'];
			}
			if (strpos ( $c ['config_name'], '_pass' ) !== false) {
				$c ['config_type'] = 'password';
				$value = str_repeat ( 'x', strlen ( $value ) );
				$entry = '<input class="form-control" type="password" name="apmcfg[' . $c ['config_name'] . ']" value="' . $value . '" ' . $extra . ' onChange="document.getElementById(\'' . $c ['config_name'] . '_mod\').value=\'1\';" />';
				$entry .= '<input type="hidden" name="' . $c ['config_name'] . '_mod" id="' . $c ['config_name'] . '_mod" value="" />';
			} else {
				$entry = '<input class="form-control" type="' . $c ['config_type'] . '" name="apmcfg[' . $c ['config_name'] . ']" id="apmcfg[' . $c ['config_name'] . ']" value="' . $value . '" ' . $extra . '/>';
			}
			break;
	}
	
	if ($c ['config_group'] != $last_group) {
		if ($last_group != '') {
			$output .= '</tbody></table></div>';
			if ($i > $k && $secCol) {
				$output .= '</div><div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
				$secCol = false;
			}
		}
		
		$output .= '<div class="table-responsive"><table class="table table-bordered table-striped table-static" width="100%" cellspacing="1" cellpadding="2" border="0"><thead><tr><td colspan="2"><span class="title-icon glyphicon glyphicon-th"></span>' . $AppUI->_ ( $c ['config_group'] . '_group_title' ) . '</td></tr></thead><tbody>';
		$last_group = $c ['config_group'];
	}
	
	$output .= '<tr>
                    <td><span data-original-title="' . $tooltip . '" data-container="body" data-toggle="tooltip" data-placement="right" name="' . $c ['config_name'] . '"> ' . $AppUI->_ ( $c ['config_name'] . '_title' ) . '<span class="glyphicon glyphicon-guestion-sign"></span></span></td>' . '<td align="left">' . $entry . '<input class="btn btn-default" type="hidden"  name="apmcfgId[' . $c ['config_name'] . ']" value="' . $c ['config_id'] . '" />' . '</td>

                </tr>';
}
?>
<form name="cfgFrm" action="index.php?m=system&a=systemconfig"
	method="post" accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_systemconfig_aed" />
	<div class="panel panel-default">
		<div class="panel-heading">
    <?php echo $AppUI->__('Edit system data'); ?>
    </div>
		<div class="panel-body">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<?php
		
$output .= '</tbody></table></div>';
		echo $output;
		?>

		<tr>
					<span> <input class="btn btn-info" type="submit" name="do_save_cfg"
						value="<?php echo $AppUI->_('save'); ?>" />
					</span>
				</tr>
			</div>
		</div>
	</div>
</form>

<script language="javascript" type="text/javascript">
        $(document).ready(function(){
            $("#apmcfg\\[system_timezone\\]").wrap("<div class='selectborder' />")
            $("#apmcfg\\[admin_email\\]").wrap("<div class='selectborder' />")
        });
</script>