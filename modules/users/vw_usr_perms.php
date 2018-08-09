<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
// @todo remove database query

global $AppUI, $user_id;

$canEdit = canEdit ( 'users' );
$perms = &$AppUI->acl ();
$module_list = $perms->getModuleList ();

$pgos = array ();
$pgo_list = $AppUI->getPermissionableModuleList ();

// Build an intersection array for the modules and their listing
$modules = array ();
$offset = 0;
foreach ( $module_list as $module ) {
	$modName = $module ['name'];
	$addToList = false;
	
	if (strpos ( $modName, 'Admin' ) === false && strpos ( $modName, 'All Modules' ) === false) {
		// non-admin modules
		$addToList = canView ( $module ['value'] ) && canEdit ( $module ['value'] );
	} else {
		// admin module
		if ($perms->checkModuleItem ( 'system', 'edit' )) {
			$addToList = true;
		}
	}
	if ($addToList) {
		$modules [$module ['type'] . ',' . $module ['id']] = $module ['name'];
		if ($module ['type'] == 'mod' && isset ( $pgo_list [$module ['name']] )) {
			$pgos [$offset] = $pgo_list [$module ['name']] ['permissions_item_table'];
		}
		$offset ++;
	}
}
$count = 0;

// Pull User perms
$user_acls = $perms->getUserACLs ( $user_id );
$user_acls = (is_array ( $user_acls )) ? $user_acls : array ();
$perm_list = $perms->getPermissionList ();

?>

<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
	?>
function editPerm( id, gon, it, vl, nm ) {
/*
	id = Permission_id
	gon =permission_grant_on
	it =permission_item
	vl =permission_value
	nm = text representation of permission_value
*/
	var f = document.frmPerms;

	f.sqlaction2.value = "<?php echo $AppUI->_('edit'); ?>";
	
	f.permission_id.value = id;
	f.permission_item.value = it;
	f.permission_item_name.value = nm;
	for(var i=0, n=f.permission_grant_on.options.length; i < n; i++) {
		if (f.permission_module.options[i].value == gon) {
			f.permission_module.selectedIndex = i;
			break;
		}
	}
	f.permission_value.selectedIndex = vl+1;
	f.permission_item_name.value = nm;
}

function clearIt(){
	var f = document.frmPerms;
	f.sqlaction2.value = "<?php echo $AppUI->_('add'); ?>";
	f.permission_id.value = 0;
	f.permission_grant_on.selectedIndex = 0;
}

function delIt(id) {
	if (confirm('<?php echo $AppUI->_('Are you sure you want to delete this permission?'); ?>')) {
		var f = document.frmPerms;
		f.del.value = 1;
		f.permission_id.value = id;
		f.submit();
	}
}

var tables = new Array;
<?php
	foreach ( $pgos as $key => $value ) {
		// Find the module id in the modules array
		echo "tables['$key'] = '$value';\n";
	}
	?>

function popPermItem() {
	var f = document.frmPerms;
	var pgo = f.permission_module.selectedIndex;

	if (!(pgo in tables)) {
		alert( '<?php echo $AppUI->_('No list associated with this Module'); ?>' );
		return;
	}
	f.permission_table.value = tables[pgo];
	window.open('./index.php?m=public&a=selector&dialog=1&callback=setPermItem&table=' + tables[pgo], 'selector', 'left=50,top=50,height=250,width=400,resizable')
}

// Callback function for the generic selector
function setPermItem( key, val ) {
	var f = document.frmPerms;
	if (val != '') {
		f.permission_item.value = key;
		f.permission_item_name.value = val;
		f.permission_name.value = val;
	} else {
		f.permission_item.value = '0';
		f.permission_item_name.value = 'all';
		f.permission_table.value = '';
	}
}
<?php } ?>
</script>


<div class="panel panel-default">
	<div class="panel-heading">
<?php echo $AppUI->_('Form and list of permissions'); ?>
</div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
<?php if ($canEdit) { ?>
                <form name="frmPerms" method="post" action="?m=users"
				accept-charset="utf-8">
				<input type="hidden" name="del" value="0" /> <input type="hidden"
					name="dosql" value="do_perms_aed" /> <input type="hidden"
					name="user_id" value="<?php echo $user_id; ?>" /> <input
					type="hidden" name="permission_user"
					value="<?php echo $perms->get_object_id('user', $user_id, 'aro'); ?>" />
				<input type="hidden" name="permission_id" value="0" /> <input
					type="hidden" name="permission_item" value="0" /> <input
					type="hidden" name="permission_table" value="" /> <input
					type="hidden" name="permission_name" value="" />

				<div class="table-responsive">
					<table class="table table-bordered table-striped table-static"
						width="100%" cellspacing="1" cellpadding="2" border="0">
						<thead>
							<tr>
								<td colspan="2"><span
									class="title-icon glyphicon glyphicon-th-list"></span>
<?php echo $AppUI->_('Add Permissions'); ?>
</td>
							</tr>
						</thead>
						<tbody>

							<tr>
								<td><?php echo $AppUI->_('Module'); ?></td>
								<td width="100%"><?php echo arraySelect($modules, 'permission_module', 'size="1" class="form-control"', 'grp,all', true); ?></td>
							</tr>
                        <?php //echo '<tr><td>'.$AppUI->_('Item').'</td>    <td><input type="text" name="permission_item_name" class="form-control" size="30" value="all" disabled="disabled" /><input type="button" name="" class="btn btn-default" value="'.$AppUI->_('Select').'" onclick="popPermItem();" />                            </td>                        </tr>'; ?>
                        <tr>
								<td><?php echo $AppUI->_('Permission type'); ?></td>
								<td><select name="permission_access" class="form-control">
										<option value='1'><?php echo $AppUI->_('allow'); ?></option>
										<option value='0'><?php echo $AppUI->_('deny'); ?></option>
								</select></td>
							</tr>
                        <?php
	foreach ( $perm_list as $perm_id => $perm_name ) {
		?>
                            <tr>
								<td nowrap="nowrap"><?php echo $AppUI->_($perm_name); ?></td>
								<td><input type="checkbox" name="permission_type[]"
									value="<?php echo $perm_id; ?>" /></td>
							</tr>
                            <?php
	}
	?>
 

</tbody>
					</table>
				</div>

				<div style="padding-bottom: 20px;">
					<input type="reset" value="<?php echo $AppUI->_('clear'); ?>"
						class="btn btn-default" name="sqlaction" onclick="clearIt();" /> <input
						type="submit" value="<?php echo $AppUI->_('add'); ?>"
						class="btn btn-info" name="sqlaction2" />
				</div>

			</form>
            <?php } ?>

<div class="table-responsive" data-add-focus-btn="false"
				data-pattern="priority-columns">
				<table id="rwd-table" class="table table-bordered table-striped "
					cellspacing="0">

					<thead>
						<tr>
							<th id="rwd-table-col-0" data-priority="1"><?php echo $AppUI->_('Item'); ?></th>
							<th id="rwd-table-col-1" data-priority="1"><?php echo $AppUI->_('Type'); ?></th>
							<th id="rwd-table-col-2" data-priority="1"><?php echo $AppUI->_('Status'); ?></th>
							<th id="rwd-table-col-3" data-priority="1"><?php echo $AppUI->_('Options'); ?></th>
						</tr>
					</thead>
					<tbody>

			<?php
			if (is_array ( $user_acls ) && ! empty ( $user_acls )) {
				foreach ( $user_acls as $acl ) {
					$buf = '';
					$permission = $perms->get_acl ( $acl );
					
					// TODO: Do we want to make the colour depend on the allow/deny/inherit flag?
					// Module information.
					if (is_array ( $permission )) {
						$buf .= "<td>";
						$modlist = array ();
						$itemlist = array ();
						if (is_array ( $permission ['axo_groups'] )) {
							foreach ( $permission ['axo_groups'] as $group_id ) {
								$group_data = $perms->get_group_data ( $group_id, 'axo' );
								$modlist [] = $AppUI->_ ( $group_data [3] );
							}
						}
						$_canEdit = true;
						$_canView = true;
						if (is_array ( $permission ['axo'] )) {
							foreach ( $permission ['axo'] as $key => $section ) {
								foreach ( $section as $id ) {
									$mod_data = $perms->get_object_full ( $id, $key, 1, 'axo' );
									if (is_numeric ( $mod_data ['value'] )) {
										$module = $pgo_list [ucfirst ( $key )];
										$data = __extract_from_vw_usr_perms ( $module, $mod_data );
										
										$modlist [] = $AppUI->_ ( ucfirst ( $key ) ) . ': ' . apmHTMLDecode ( $data );
										if (! canView ( $mod_data ['section_value'], $mod_data ['value'] )) {
											$_canView = false;
										}
										if (! canEdit ( $mod_data ['section_value'], $mod_data ['value'] )) {
											$_canEdit = false;
										}
									} else {
										$modlist [] = $AppUI->_ ( ucfirst ( $key ) ) . ': ' . apmHTMLDecode ( $mod_data ['name'] );
										if (! canView ( $mod_data ['value'] )) {
											$_canView = false;
										}
										if (! canEdit ( $mod_data ['value'] )) {
											$_canEdit = false;
										}
									}
								}
							}
						}
						if (! $_canView) {
							continue;
						}
						$buf .= implode ( '<br />', $modlist );
						$buf .= '</td>';
						// Item information TODO: need to figure this one out.
						// $buf .= "<td></td>";
						// Type information.
						$buf .= '<td>';
						$perm_type = array ();
						if (is_array ( $permission ['aco'] )) {
							foreach ( $permission ['aco'] as $key => $section ) {
								foreach ( $section as $value ) {
									$perm = $perms->get_object_full ( $value, $key, 1, 'aco' );
									$perm_type [] = $AppUI->_ ( $perm ['name'] );
								}
							}
						}
						$buf .= implode ( '<br />', $perm_type );
						$buf .= '</td>';
						
						// Allow or deny
						$buf .= '<td>' . $AppUI->_ ( $permission ['allow'] ? 'allow' : 'deny' ) . '</td>';
						$buf .= '<td nowrap="nowrap">';
						$canDelete = (canEdit ( 'users' ) && $_canEdit);
						if ($canDelete) {
							$buf .= '<a class="btn btn-xs btn-warning" role="button" href="javascript:delIt(' . $acl . ');" data-toggle="tooltip" data-placement="right" data-container="body" data-original-title="' . $AppUI->_ ( 'delete instruction' ) . '"><span class="glyphicon glyphicon-ban-circle"></span></a>';
						}
						$buf .= '</td>';
						
						echo "<tr>$buf</tr>";
					}
				}
			} else {
				echo '<tr><td colspan="4">Dane niedostępne</td></tr>';
			}
			?>
            </tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->

