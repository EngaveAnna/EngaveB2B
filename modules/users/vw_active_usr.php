<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $apmconfig, $canEdit, $stub, $where, $orderby;

$users = apmgetUsersList ( $stub, $where, $orderby );
$canLogin = true;

require apm_BASE_DIR . '/modules/users/vw_usr.php';