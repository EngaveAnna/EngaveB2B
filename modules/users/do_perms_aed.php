<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$del = ( int ) apmgetParam ( $_POST, 'del', 0 );

$user_id = ( int ) apmgetParam ( $_POST, 'user_id', 0 );

$controller = new apm_Controllers_Permissions ( $AppUI->acl (), $del, 'Permission', 'm=users&a=view&user_id=' . $user_id, 'm=users&a=view&user_id=' . $user_id );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );