<?php 
$loginFromPage = 'index.php';
require_once 'base.php';

clearstatcache ();
if (is_file ( apm_BASE_DIR . '/includes/config.php' ) && filesize ( apm_BASE_DIR . '/includes/config.php' ) > 0) {
	require_once apm_BASE_DIR . '/includes/config.php';
	if (isset ( $dPconfig )) {
		echo '<html><head><meta http-equiv="refresh" content="5; URL=' . apm_BASE_URL . '/install/index.php"></head><body>';
		echo 'Fatal Error. It appears you\'re converting from Softmag.<br/><a href="./install/index.php">' . 'Click Here To Start the Conversion!</a> (forwarded in 5 sec.)</body></html>';
		exit ();
	}
} else {
	echo '<html><head><meta http-equiv="refresh" content="5; URL=' . apm_BASE_URL . '/install/index.php"></head><body>';
	echo 'Fatal Error. You haven\'t created a config file yet.<br/><a href="./install/index.php">' . 'Click Here To Start Installation and Create One!</a> (forwarded in 5 sec.)</body></html>';
	exit ();
}

require_once apm_BASE_DIR . '/includes/main_functions.php';
require_once apm_BASE_DIR . '/includes/db_adodb.php';

$defaultTZ = apmgetConfig ( 'system_timezone', 'UTC' );
$defaultTZ = ('' == $defaultTZ) ? 'UTC' : $defaultTZ;
date_default_timezone_set ( $defaultTZ );

// don't output anything. Usefull for fileviewer.php, gantt.php, etc.
$suppressHeaders = apmgetParam ( $_GET, 'suppressHeaders', false );

$session = new apm_System_Session ();
$session->start ();

// write the HTML headers
if (! $suppressHeaders) {
	header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
	header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
	header ( 'Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0' ); // HTTP/1.1
	header ( 'Pragma: no-cache' ); // HTTP/1.0
	header ( "Content-type: text/html; charset=UTF-8" );
}

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale ( LC_CTYPE, 'C' );

// check if session has previously been initialised
if (! isset ( $_SESSION ['AppUI'] ) || isset ( $_GET ['logout'] )) {
	if (isset ( $_GET ['logout'] ) && isset ( $_SESSION ['AppUI']->user_id )) {
		$AppUI = &$_SESSION ['AppUI'];
		$user_id = $AppUI->user_id;
		addHistory ( 'login', $AppUI->user_id, 'logout', $AppUI->user_first_name . ' ' . $AppUI->user_last_name );
	}
	
	$_SESSION ['AppUI'] = new apm_Core_CAppUI ();
}
$AppUI = &$_SESSION ['AppUI'];
$last_insert_id = $AppUI->last_insert_id;

$AppUI->setStyle ();

// Function for update lost action in user_access_log
$AppUI->updateLastAction ( $last_insert_id );
// load default preferences if not logged in
if ($AppUI->doLogin ()) {
	$AppUI->loadPrefs ( 0 );
}

// Function register logout in user_acces_log
if (isset ( $user_id ) && isset ( $_GET ['logout'] )) {
	$AppUI->registerLogout ( $user_id );
}

// set the default ui style
$uistyle = $AppUI->getPref ( 'UISTYLE' ) ? $AppUI->getPref ( 'UISTYLE' ) : apmgetConfig ( 'host_style' );
include apm_BASE_DIR . '/style/' . $uistyle . '/overrides.php';
$uiName = str_replace ( '-', '', $uistyle );

$uiClass = 'style_' . $uiName;
$theme = new $uiClass ( $AppUI );

// check is the user needs a new password
if (apmgetParam ( $_POST, 'lostpass', 0 )) {
	$AppUI->setUserLocale ();
	include apm_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php';
	include apm_BASE_DIR . '/locales/core.php';
	setlocale ( LC_TIME, $AppUI->user_lang );
	if (apmgetParam ( $_POST, 'sendpass', 0 )) {
		sendNewPass ();
		$AppUI->setMsg ( 'Password has been sent to email', UI_MSG_OK );
	} else {
		$AppUI->setMsg ( 'Remind password error', UI_MSG_ERROR );
		include $theme->resolveTemplate ( 'login' );
	}
	exit ();
}

// check if the user is trying to log in
// Note the change to REQUEST instead of POST. This is so that we can
// support alternative authentication methods such as the PostNuke
// and HTTP auth methods now supported.
if (isset ( $_POST ['login'] )) {
	$username = apmgetParam ( $_POST, 'username', '' );
	$password = apmgetParam ( $_POST, 'password', '' );
	$redirect = apmgetParam ( $_POST, 'redirect', '' );
	$AppUI->setUserLocale ();
	include apm_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php';
	include apm_BASE_DIR . '/locales/core.php';
	$ok = $AppUI->login ( $username, $password );
	if (! $ok) {
		$AppUI->setMsg ( 'Login Failed', UI_MSG_ERROR );
	} else {
		// Register login in user_acces_log
		$AppUI->registerLogin ();
	}
	addHistory ( 'login', $AppUI->user_id, 'login', $AppUI->user_first_name . ' ' . $AppUI->user_last_name );
	$AppUI->redirect ( '' . $redirect );
}

// clear out main url parameters
$m = '';
$a = '';
$u = '';

//APM todo sign applet must store system cookie
if(!($_GET['m']=='signatures' AND ($_GET['a']=='do_get_signdata'||$_GET['a']=='do_post_signdata') AND (md5(apmgetConfig('sign_pid').$_GET['mod'].'m'.$_GET['id'].'c'.$_GET['oid'])==$_GET['pid']))&&!($_GET['m']=='payments' && $_GET['a']=='do_transaction_verif'))
{	
	
//APM thi is default check if we are logged in
if ($AppUI->doLogin()) {
		// load basic locale settings
		$AppUI->setUserLocale ();
		include './locales/' . $AppUI->user_locale . '/locales.php';
		include './locales/core.php';
		setlocale ( LC_TIME, $AppUI->user_lang );
		$redirect = $_SERVER ['QUERY_STRING'] ? strip_tags ( $_SERVER ['QUERY_STRING'] ) : '';
		if (strpos ( $redirect, 'logout' ) !== false) {
			$redirect = '';
		}
	
		if (isset ( $locale_char_set )) {
			header ( 'Content-type: text/html;charset=' . $locale_char_set );
		}
	
		include $theme->resolveTemplate ( 'login' );
		// destroy the current session and output login page
		session_unset ();
		session_destroy ();
		exit ();
	}
$AppUI->setUserLocale ();
//bring in the rest of the support and localisation files
$perms = &$AppUI->acl ();
}

$loader = new apm_FileSystem_Loader ();
/**
 * TODO: We should validate that the module identified by $m is actually
 * installed & active.
 * If not, we should go back to the defaults.
 */
$def_a = 'index';
if (! isset ( $_GET ['m'] ) && ! empty ( $apmconfig ['default_view_m'] )) {
	/*
	 * if (!$perms->checkModule($apmconfig['default_view_m'], 'view', $AppUI->user_id)) {
	 * $m = 'public';
	 * $def_a = 'welcome';
	 * } else {
	 * $m = $apmconfig['default_view_m'];
	 * $def_a = !empty($apmconfig['default_view_a']) ? $apmconfig['default_view_a'] : $def_a;
	 * $tab = $apmconfig['default_view_tab'];
	 * $_GET['tab'] = $tab;
	 * }
	 */
	
	$m = $apmconfig ['default_view_m'];
	$def_a = ! empty ( $apmconfig ['default_view_a'] ) ? $apmconfig ['default_view_a'] : $def_a;
	$tab = $apmconfig ['default_view_tab'];
	$_GET ['tab'] = $tab;
} else {
	// set the module from the url
	$m = $loader->checkFileName ( apmgetParam ( $_GET, 'm', getReadableModule () ) );
}
$m = preg_replace ( "/[^a-z0-9_]/", "", $m );
// set the action from the url
$a = $loader->checkFileName ( apmgetParam ( $_GET, 'a', $def_a ) );
$a = preg_replace ( "/[^a-z0-9_]/", "", $a );
if ($m == 'projects' && $a == 'view' && $apmconfig ['projectdesigner_view_project'] && ! apmgetParam ( $_GET, 'bypass' ) && ! (isset ( $_GET ['tab'] ))) {
	if ($AppUI->isActiveModule ( 'projectdesigner' )) {
		$m = 'projectdesigner';
		$a = 'index';
	}
}

/*
 * This check for $u implies that a file located in a subdirectory of higher depth than 1
 * in relation to the module base can't be executed. So it would'nt be possible to
 * run for example the file module/directory1/directory2/file.php
 * Also it won't be possible to run modules/module/abc.zyz.class.php for that dots are
 * not allowed in the request parameters.
 */

$u = $loader->checkFileName ( apmgetParam ( $_GET, 'u', '' ) );
$u = preg_replace ( "/[^a-z0-9_]/", "", $u );

// load module based locale settings
include apm_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php';
include apm_BASE_DIR . '/locales/core.php';

setlocale ( LC_TIME, $AppUI->user_lang );
$m_config = apmgetConfig ( $m );

if (! $suppressHeaders) {
	// output the character set header
	if (isset ( $locale_char_set )) {
		header ( 'Content-type: text/html;charset=' . $locale_char_set );
	}
}

if ($u && file_exists ( apm_BASE_DIR . '/modules/' . $m . '/' . $u . '/' . $u . '.class.php' )) {
	includeapm_BASE_DIR . '/modules/' . $m . '/' . $u . '/' . $u . '.class.php';
}

// include the module ajax file - we use file_exists instead of @ so that any parse errors in the file are reported,
// rather than errors further down the track.
$modajax = $AppUI->getModuleAjax ( $m );
if (file_exists ( $modajax )) {
	include $modajax;
}
if ($u && file_exists ( apm_BASE_DIR . '/modules/' . $m . '/' . $u . '/' . $u . '.ajax.php' )) {
	include apm_BASE_DIR . '/modules/' . $m . '/' . $u . '/' . $u . '.ajax.php';
}

// do some db work if dosql is set
// TODO - MUST MOVE THESE INTO THE MODULE DIRECTORY
if (isset ( $_POST ['dosql'] )) {
	require apm_BASE_DIR . '/modules/' . $m . '/' . ($u ? ($u . '/') : '') . $loader->checkFileName ( $_POST ['dosql'] ) . '.php';
}

// start output proper
if (isset ( $_POST ['dosql'] ) && $_POST ['dosql'] == 'do_file_co') {
	ob_start ();
} else {
	if (! ob_start ( 'ob_gzhandler' )) {
		ob_start ();
	}
}

if (! $suppressHeaders) {
	include $theme->resolveTemplate ( 'header' );
}

if (apm_PERFORMANCE_DEBUG) {
	$apm_performance_setuptime = (array_sum ( explode ( ' ', microtime () ) ) - $apm_performance_time);
}

$pageHandler = new apm_Output_PageHandler ();
$all_tabs = $pageHandler->loadExtras ( $_SESSION, $AppUI, $m, 'tabs' );
$all_crumbs = $pageHandler->loadExtras ( $_SESSION, $AppUI, $m, 'crumbs' );

$module_file = apm_BASE_DIR . '/modules/' . $m . '/' . ($u ? ($u . '/') : '') . $a . '.php';
if (file_exists ( $module_file )) {
	require $module_file;
} else {
	// TODO: make this part of the public module?
	$titleBlock = new apm_Theme_TitleBlock ( $AppUI->_ ( 'Warning' ), 'log-error.gif' );
	$titleBlock->show ();
	include $theme->resolveTemplate ( 'missing_module' );
}
if (! $suppressHeaders) {
	echo '<iframe name="thread" src="' . apm_BASE_URL . '/modules/index.html" width="0" height="0" frameborder="0"></iframe>';
	echo '<iframe name="thread2" src="' . apm_BASE_URL . '/modules/index.html" width="0" height="0" frameborder="0"></iframe>';
	// Theme footer goes before the performance box
	include $theme->resolveTemplate ( 'footer' );
	if (apm_PERFORMANCE_DEBUG) {
		include $theme->resolveTemplate ( 'performance' );
	}
	include $theme->resolveTemplate ( 'message_loading' );
	
	// close the body and html here, instead of on the theme footer.
	echo '</body>
          </html>';
}
ob_end_flush ();