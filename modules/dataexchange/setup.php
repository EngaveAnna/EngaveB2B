<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
/**
 * Name: Data Exchange
 * Directory: dataexchange
 * Version: 1.1
 * Type: user
 * UI Name: Data Exchange
 * UI Icon: ?
 */

$config = array ();
$config ['mod_name'] = 'Data Exchange';
$config ['mod_version'] = '1.1';
$config ['mod_directory'] = 'dataexchange'; // the module path
$config ['mod_setup_class'] = 'CSetupProjectImporter'; // the name of the setup class
$config ['mod_type'] = 'user'; // 'core' for modules
$config ['mod_ui_name'] = $config ['mod_name']; // the name that is shown in the main menu of the User Interface
$config ['mod_ui_icon'] = 'projectimporter.png'; // name of a related icon
$config ['mod_description'] = 'Data Exchange using xCBL 4.0';

if (@$a == 'setup') {
	echo apmshowModuleConfig ( $config );
}