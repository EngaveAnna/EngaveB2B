<?php
function apmProject_autoload($className) {
	$library_name = 'apm_';
	
	/**
	 * This portion of the autoloader is non-standard and exists only to catch our deprecated classes.
	 */
	switch ($className) {
		case 'apm_API_iCalendar' :
		case 'apm_Core_Config' :
		case 'apm_Core_Dispatcher' :
		case 'apm_Core_Event' :
		case 'apm_Core_EventQueue' :
		case 'apm_Core_HookHandler' :
		case 'apm_Core_Module' :
		case 'apm_Core_Preferences' :
		case 'apm_Core_Setup' :
		case 'apm_Core_UpgradeManager' :
			return include apm_BASE_DIR . '/classes/deprecated.class.php';
		default :
		// fall through
	}
	
	if (substr ( $className, 0, strlen ( $library_name ) ) != $library_name) {
		return false;
	}
	$file = str_replace ( '_', '/', $className );
	$file = str_replace ( 'apm/', '', $file );
	return include dirname ( __FILE__ ) . "/$file.class.php";
}