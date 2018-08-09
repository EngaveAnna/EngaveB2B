<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $user_id, $user_name, $canEdit, $canDelete, $tab;

$perms = &$AppUI->acl ();
$user_roles = $perms->getUserRoles ( $user_id );
$crole = new CSystem_Role ();
$roles = $crole->getRoles ();
// Format the roles for use in arraySelect
$roles_arr = array ();
foreach ( $roles as $role ) {
	if ($role ['name'] != 'Administrator') {
		$roles_arr [$role ['id']] = $role ['name'];
	} else {
		if ($perms->checkModuleItem ( 'system', 'edit' )) {
			$roles_arr [$role ['id']] = $role ['name'];
		}
	}
}

?>

<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
	?>
function delIt(id) {
	if (confirm( '<?php echo $AppUI->_('Are you sure you want to delete this role?'); ?>')) {
		var f = document.frmRoles;
		f.del.value = 1;
		f.role_id.value = id;
		f.submit();
	}
}
function clearIt(){
	var f = document.frmRoles;
	f.sqlaction2.value = "<?php echo $AppUI->_('add'); ?>";
	f.user_role.selectedIndex = 0;
}
<?php
}
?>

</script>

<div class="panel panel-default">
	<div class="panel-heading">
<?php echo $AppUI->_('Form and list of permissions'); ?>
</div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">


<?php if ($canEdit) { ?>

<form name="frmRoles" method="post" action="?m=users"
				accept-charset="utf-8">
				<input type="hidden" name="del" value="0" /> <input type="hidden"
					name="dosql" value="do_userrole_aed" /> <input type="hidden"
					name="user_id" value="<?php echo $user_id; ?>" /> <input
					type="hidden" name="user_name" value="<?php echo $user_name; ?>" />
				<input type="hidden" name="role_id" value="" />


				<div class="table-responsive">
					<table class="table table-bordered table-striped table-static"
						width="100%" cellspacing="1" cellpadding="2" border="0">
						<thead>
							<tr>
								<td colspan="2"><span
									class="title-icon glyphicon glyphicon-th-list"></span>
<?php echo $AppUI->_('Add role'); ?>
</td>
							</tr>
						</thead>
						<tbody>



							<tr>
								<td><?php echo $AppUI->_('Add Role'); ?></td>
								<td><?php echo arraySelect($roles_arr, 'user_role', 'size="1" class="form-control"', '', true); ?></td>
							</tr>



						</tbody>
					</table>
				</div>





				<div style="padding-bottom: 20px;">
				<?php
	if (! count ( $user_roles )) {
		echo $AppUI->_ ( 'Notify New User Activation' );
		?> <input type='checkbox' name='notify_new_user' />&nbsp;&nbsp;&nbsp;&nbsp;<?php
	}
	?>
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
							<th id="rwd-table-col-0" data-priority="1"><?php echo $AppUI->_('Role'); ?></th>
							<th id="rwd-table-col-1" data-priority="1"><?php echo $AppUI->_('Options'); ?></th>
						</tr>
					</thead>
					<tbody>



                <?php
																
foreach ( $user_roles as $row ) {
																	$buf = "<td>" . $row ['name'] . "</td>";
																	$buf .= '<td nowrap>';
																	if ($canEdit) {
																		$buf .= '<a class="btn btn-xs btn-warning" role="button" href="javascript:delIt(' . $row ['id'] . ');" data-toggle="tooltip" data-placement="right" data-container="body" data-original-title="' . $AppUI->_ ( 'delete role' ) . '"><span class="glyphicon glyphicon-ban-circle"></span></a>';
																	}
																	$buf .= '</td>';
																	
																	echo "<tr>$buf</tr>";
                } ?>
</tbody>
				</table>
			</div>

		</div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->

