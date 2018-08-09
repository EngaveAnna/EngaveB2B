<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'UserIdxTab', $_GET, 'tab', 0 );

$perms = &$AppUI->acl ();
if (! canView ( 'users' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

if (isset ( $_GET ['stub'] )) {
	$AppUI->setState ( 'UserIdxStub', apmgetParam ( $_GET, 'stub', null ) );
	$AppUI->setState ( 'UserIdxWhere', '' );
} elseif (isset ( $_POST ['search_string'] )) {
	$AppUI->setState ( 'UserIdxWhere', $_POST ['search_string'] );
	$AppUI->setState ( 'UserIdxStub', '' );
}
$stub = $AppUI->getState ( 'UserIdxStub' );
$where = $AppUI->getState ( 'UserIdxWhere' );
$where = apmformSafe ( $where, true );

if (isset ( $_GET ['orderby'] )) {
	$AppUI->setState ( 'UserIdxOrderby', apmgetParam ( $_GET, 'orderby', null ) );
}
$orderby = $AppUI->getState ( 'UserIdxOrderby' ) ? $AppUI->getState ( 'UserIdxOrderby' ) : 'user_username';
$orderby = ($tab == 3 || ($orderby != 'date_time_in' && $orderby != 'user_ip')) ? $orderby : 'user_username';

// Pull First Letters
$letters = CUser::getFirstLetters ();
$letters = $letters . CContact::getFirstLetters ( $AppUI->user_id, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'User Management', 'icon.png', $m );
$titleBlock->addSearchCell ( $where );
// $titleBlock->addCell($a2z);
$titleBlock->addCrumb ( '?m=users&a=addedit', 'New user' );
$titleBlock->show ();

?>
<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
	?>
function delMe( x, y ) {
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS) . ' ' . $AppUI->_('User', UI_OUTPUT_JS); ?> " + y + "?" )) {
		document.frmDelete.user_id.value = x;
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<?php
$tabBox = new CTabBox ( '?m=users', apm_BASE_DIR . '/modules/users/', $tab );
$tabBox->add ( 'vw_active_usr', 'Active Users' );
$tabBox->add ( 'vw_inactive_usr', 'Inactive Users' );
$tabBox->add ( 'vw_usr_log', 'User Log' );
$tabBox->add ( 'vw_usr_sessions', 'Active Sessions' );
$tabBox->show ();

?>

<form name="frmDelete" action="./index.php?m=users" method="post"
	accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_user_aed" /> <input
		type="hidden" name="del" value="1" /> <input type="hidden"
		name="user_id" value="0" />
</form>