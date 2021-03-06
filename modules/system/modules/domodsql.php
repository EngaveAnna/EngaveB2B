<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo remove database query

$perms = &$AppUI->acl ();
if (! canEdit ( 'system' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

// #
// # Activate or move a module entry
// #
$cmd = apmgetParam ( $_GET, 'cmd', '0' );
$mod_id = ( int ) apmgetParam ( $_GET, 'mod_id', '0' );
$mod_directory = apmgetParam ( $_GET, 'mod_directory', '0' );

$obj = new apm_System_Module ();
if ($mod_id) {
	$obj->load ( $mod_id );
} else {
	$obj->mod_directory = $mod_directory;
}

// check for a setup file
$ok = file_exists ( apm_BASE_DIR . '/modules/' . $obj->mod_directory . '/setup.php' );
if (! $ok && $obj->mod_type != 'core') {
	$AppUI->setMsg ( 'Module setup file could not be found', UI_MSG_ERROR );
	if ($cmd == 'remove') {
		$obj->remove ();
		$AppUI->setMsg ( 'Module has been removed from the modules list - please check your database for additional tables that may need to be removed', UI_MSG_ERROR );
	}
	$AppUI->redirect ( 'm=system&u=modules' );
}

if (file_exists ( apm_BASE_DIR . '/modules/' . $obj->mod_directory . '/setup.php' )) {
	include apm_BASE_DIR . '/modules/' . $obj->mod_directory . '/setup.php';
	$setupclass = $config ['mod_setup_class'];
	if (! $setupclass) {
		if ($obj->mod_type != 'core') {
			$AppUI->setMsg ( 'Module does not have a valid setup class defined', UI_MSG_ERROR );
			$AppUI->redirect ( 'm=system&u=modules' );
		}
	} else {
		$setup = new $setupclass ( $AppUI, $config, new apm_Database_Query () );
	}
}

switch ($cmd) {
	case 'moveup' :
	case 'movedn' :
	case 'movefirst' :
	case 'movelast' :
		$obj->move ( $cmd );
		$AppUI->setMsg ( 'Module re-ordered', UI_MSG_OK );
		break;
	case 'toggle' :
		// just toggle the active state of the table entry
		$obj->mod_active = 1 - $obj->mod_active;
		$obj->mod_ui_active = $obj->mod_active;
		$obj->store ();
		$AppUI->setMsg ( 'Module state changed', UI_MSG_OK );
		break;
	case 'toggleMenu' :
		// just toggle the active state of the table entry
		$obj->mod_ui_active = 1 - $obj->mod_ui_active;
		$obj->store ();
		$AppUI->setMsg ( 'Module menu state changed', UI_MSG_OK );
		break;
	case 'install' :
		$result = $setup->install ( $config );
		
		if (! $result) {
			$AppUI->setMsg ( $setup->getErrors (), UI_MSG_ERROR );
		} else {
			$obj->bind ( $config );
			// add to the installed modules table
			$obj->install ();
			$AppUI->setMsg ( 'Module installed', UI_MSG_OK, true );
		}
		break;
	case 'remove' :
		$result = $setup->remove ();
		
		if (! $result) {
			$AppUI->setMsg ( $setup->getErrors (), UI_MSG_ERROR );
		} else {
			$obj->bind ( $config );
			// remove from the installed modules table
			$obj->remove ();
			$AppUI->setMsg ( 'Module removed', UI_MSG_OK, true );
		}
		break;
	case 'upgrade' :
		$result = $setup->upgrade ( $obj->mod_version );
		
		if (! $result) {
			$AppUI->setMsg ( $setup->getErrors (), UI_MSG_ERROR );
		} else {
			$obj->bind ( $config );
			$obj->store ();
			$AppUI->setMsg ( 'Module upgraded', UI_MSG_OK, true );
		}
		break;
	case 'configure' :
		$result = $setup->configure ();
		
		if (! $result) { // returns true if configure succeeded
			$AppUI->setMsg ( 'Module configuration failed', UI_MSG_ERROR );
		}
		break;
	default :
		$AppUI->setMsg ( 'Unknown Command', UI_MSG_ERROR );
		break;
}
$AppUI->redirect ( 'm=system&u=modules' );