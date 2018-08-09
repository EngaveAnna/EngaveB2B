<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// pull all the key types
$perms = &$AppUI->acl ();

// Get the permissions for this module
$canAccess = canAccess ( 'roles' );
if (! $canAccess) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canRead = canView ( 'roles' );
$canAdd = canAdd ( 'roles' );
$canEdit = canEdit ( 'roles' );
$canDelete = canDelete ( 'roles' );

$crole = new CSystem_Role ();
$roles = $crole->getRoles ();

$role_id = ( int ) apmgetParam ( $_GET, 'role_id', 0 );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Roles', 'main-settings.png', $m );
$titleBlock->addCrumb ( '?m=system', 'System Admin' );
$titleBlock->show ();

$crumbs = array ();
$crumbs ['?m=system'] = 'System Admin';

?>

<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
	?>
function delIt(id) {
	if (confirm( 'Are you sure you want to delete this?' )) {
		f = document.roleFrm;
		f.del.value = 1;
		f.role_id.value = id;
		f.submit();
	}
}
<?php } ?>
</script>

<div class="panel panel-default">
	<div class="panel-heading">
	<?php echo $AppUI->__('Role edit and list'); ?>
	</div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="table-responsive" data-add-focus-btn="false"
				data-pattern="priority-columns">
				<table id="rwd-table" class="table table-bordered table-striped "
					cellspacing="0">

					<thead>
						<tr>
							<th id="rwd-table-col-0" data-priority="1"><?php echo $AppUI->_('Role ID'); ?></th>
							<th id="rwd-table-col-1" data-priority="1"><?php echo $AppUI->_('Description'); ?></th>
							<th id="rwd-table-col-2" data-priority="1"><?php echo $AppUI->_('Options'); ?></th>
						</tr>
					</thead>
					<tbody>

<?php
// do the modules that are installed on the system
$s = '';
echo showRoleRows ( $roles );
// add in the new key row:
echo showRoleForm ( $roles, $role_id );
?>
</tbody>
				</table>
			</div>

		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->