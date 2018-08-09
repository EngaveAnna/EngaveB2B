<?php
$token = apmgetParam ( $_POST, 'token', '' );
$user_id = apmgetParam ( $_POST, 'user_id', '' );

$user = new CUser ();
$user->generateToken ( $user_id, $token );

if ($userId) {
	$redirect = 'm=users&a=view&user_id=' . $userId;
} else {
	$redirect = 'm=users';
}
$AppUI->redirect ( $redirect );