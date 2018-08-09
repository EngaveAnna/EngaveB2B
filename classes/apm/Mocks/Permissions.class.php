<?php

/**
 * Permissions system extends the phpgacl class.  Very few changes have
 * been made, however the main one is to provide the database details from
 * the main apm environment.
 *
 * @package     apmProject\mocks
 */
class apm_Mocks_Permissions extends apm_Extensions_Permissions {
	public function apmacl_nuclear($userid, $module, $item, $mod_class = array()) {
		return array (
				'access' => 1,
				'acl_id' => 'checked' 
		);
	}
	public function apmacl_check($application = 'application', $op, $user = 'user', $userid, $app = 'app', $module) {
		return true;
	}
	public function apmacl_query($application = 'application', $op, $user = 'user', $userid, $module, $item) {
		return array (
				'access' => 1,
				'acl_id' => 'checked' 
		);
	}
}