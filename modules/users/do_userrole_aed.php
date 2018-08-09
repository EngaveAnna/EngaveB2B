<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo refactor to use a core controller

$del = ( int ) apmgetParam ( $_POST, 'del', 0 );

$notify_new_user = apmgetParam ( $_POST, 'notify_new_user', 'off' );
$user_id = ( int ) apmgetParam ( $_POST, 'user_id', 0 );

$perms = &$AppUI->acl ();
// @todo shouldn't this check for the specific user?
if (! canEdit ( 'users' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg ( 'Roles' );

if ($user_id) {
	$user = new CUser ();
	$user->load ( $user_id );
	$contact = new CContact ();
	$contact->load ( $user->user_contact );
}

if ($del) {
	if ($perms->deleteUserRole ( apmgetParam ( $_POST, 'role_id', 0 ), apmgetParam ( $_POST, 'user_id', 0 ) )) {
		$AppUI->setMsg ( 'deleted', UI_MSG_ALERT, true );
	} else {
		$AppUI->setMsg ( 'failed to delete role', UI_MSG_ERROR );
	}
	$AppUI->redirect ( 'm=users&a=view&user_id=' . $user_id );
}

if (isset ( $_POST ['user_role'] ) && $_POST ['user_role']) {
	if ($perms->insertUserRole ( $_POST ['user_role'], $user_id )) {
		if ('on' == $notify_new_user) {
			notifyNewUser ( $contact->contact_email, $contact->contact_first_name );
		}
		$AppUI->setMsg ( 'added', UI_MSG_ALERT, true );
	} else {
		$AppUI->setMsg ( 'failed to add role', UI_MSG_ERROR );
	}
}
$AppUI->redirect ( 'm=users&a=view&user_id=' . $user_id );