<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$perms = &$AppUI->acl ();
$role_id = ( int ) apmgetParam ( $_GET, 'role_id', 0 );
$role = $perms->getRole ( $role_id );

$tab = $AppUI->processIntState ( 'RoleVwTab', $_GET, 'tab', 0 );

if (! is_array ( $role )) {
	$titleBlock = new apm_Theme_TitleBlock ( 'Invalid Role', 'main-settings.png', $m );
	$titleBlock->addCrumb ( '?m=system&u=roles', 'role list' );
	$titleBlock->show ();
} else {
	$titleBlock = new apm_Theme_TitleBlock ( 'View Role', 'main-settings.png', $m );
	$titleBlock->addCrumb ( '?m=system&u=roles', 'role list' );
	$titleBlock->show ();
	// Now onto the display of the user.
	?>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-static"
			width="100%" cellspacing="1" cellpadding="2" border="0">
			<thead>
				<tr>
					<td colspan="2"><span
						class="title-icon glyphicon glyphicon-th-list"></span>
<?php echo $AppUI->_('Edit role permissions'); ?>
</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="apm-label"><?php echo $AppUI->_('Role ID'); ?>:</td>
					<td class="hilite" width="100%"><?php echo $role["value"]; ?></td>
				</tr>
				<tr>
					<td class="apm-label"><?php echo $AppUI->_('Description'); ?>:</td>
					<td class="hilite" width="100%"><?php echo $AppUI->_($role["name"]); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	
	$tabBox = new CTabBox ( '?m=system&u=roles&a=viewrole&role_id=' . $role_id, apm_BASE_DIR . '/modules/system/roles/', $tab );
	$tabBox->add ( 'vw_role_perms', 'Permissions');
	$tabBox->show();
} // End of check for valid role