<?php
$loginFromPage = 'index.php';
require_once 'base.php';

//clearstatcache ();
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

// check if we are logged in

//APM todo: sign applet must store APM cookie
if($_GET['m']=='signatures')
{
	if(($_GET['a']=='do_get_signdata'||$_GET['a']=='do_post_signdata') AND md5(apmgetConfig('sign_pid').$_GET['mod'].$_GET['id']))
	$doLogin=false;
	else
	$doLogin=$AppUI->doLogin();
}
else
$doLogin=$AppUI->doLogin();


if ($doLogin) {
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
	
	$obj = new CSignature ();
	$signature_mod_name=apmgetParam ( $_GET, 'mod', 0 );
	$signature_row =apmgetParam( $_GET, 'id');
	$canAddEdit = $obj->canAddEdit ();
	$canAuthor = $obj->canCreate ();
	$canEdit = $obj->canEdit ();
	$canDelete = $obj->canDelete ();
	if (! $canAddEdit) {
		$AppUI->redirect ( ACCESS_DENIED );
	}

	$get=$obj->getSignData($signature_mod_name, $signature_row);

$string = "<document>
<body>test</body>
</document>";
echo $string;
?>