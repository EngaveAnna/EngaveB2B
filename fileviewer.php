<?php
// file viewer
require_once 'base.php';
require_once apm_BASE_DIR . '/includes/config.php';
require_once apm_BASE_DIR . '/includes/main_functions.php';
require_once apm_BASE_DIR . '/includes/db_adodb.php';

$loginFromPage = 'fileviewer.php';

$session = new apm_System_Session ();
$session->start ();

$AppUI = is_object ( $AppUI ) ? $AppUI : new apm_Core_CAppUI ();
// check if session has previously been initialised
// if no ask for logging and do redirect
if (! isset ( $_SESSION ['AppUI'] ) || isset ( $_GET ['logout'] )) {
	$_SESSION ['AppUI'] = new apm_Core_CAppUI ();
	$AppUI = &$_SESSION ['AppUI'];
	$AppUI->setConfig ( $apmconfig );
	$AppUI->setStyle ();
	
	if ($AppUI->doLogin ()) {
		$AppUI->loadPrefs ( 0 );
	}
	// check if the user is trying to log in
	if (isset ( $_POST ['login'] )) {
		$username = apmgetParam ( $_POST, 'username', '' );
		$password = apmgetParam ( $_POST, 'password', '' );
		$redirect = apmgetParam ( $_POST, 'redirect', '' );
		$ok = $AppUI->login ( $username, $password );
		if (! $ok) {
			// display login failed message
			$uistyle = $AppUI->getPref ( 'UISTYLE' ) ? $AppUI->getPref ( 'UISTYLE' ) : $apmconfig ['host_style'];
			$AppUI->setMsg ( 'Login Failed', UI_MSG_ERROR );
			require apm_BASE_DIR . '/style/' . $uistyle . '/login.php';
			session_unset ();
			exit ();
		}
		header ( 'Location: fileviewer.php?' . $redirect );
		exit ();
	}
	
	$uistyle = $AppUI->getPref ( 'UISTYLE' ) ? $AppUI->getPref ( 'UISTYLE' ) : $apmconfig ['host_style'];
	// check if we are logged in
	if ($AppUI->doLogin ()) {
		$AppUI->setUserLocale ();
		include apm_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php';
		include apm_BASE_DIR . '/locales/core.php';
		setlocale ( LC_TIME, $AppUI->user_locale );
		
		$redirect = @$_SERVER ['QUERY_STRING'];
		if (strpos ( $redirect, 'logout' ) !== false) {
			$redirect = '';
		}
		if (isset ( $locale_char_set )) {
			header ( 'Content-type: text/html;charset=' . $locale_char_set );
		}
		require apm_BASE_DIR . '/style/' . $uistyle . '/login.php';
		session_unset ();
		session_destroy ();
		exit ();
	}
}
$AppUI = &$_SESSION ['AppUI'];
include apm_BASE_DIR . '/locales/core.php';

$file_id = ( int ) apmgetParam ( $_GET, 'file_id', 0 );

if (! $file_id) {
	$AppUI->setMsg ( 'fileIdError', UI_MSG_ERROR );
	$AppUI->redirect ();
}

$file = new CFile ();
$file->load ( $file_id );

if (! $file->canView ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$exists = $file->getFileSystem ()->exists ( $file->file_project, $file->file_real_filename );

if (! $exists) {
	$AppUI->setMsg ( 'fileIdError', UI_MSG_ERROR );
	$AppUI->redirect ();
}

ob_end_clean ();
header ( 'MIME-Version: 1.0' );
header ( 'Pragma: ' );
header ( 'Cache-Control: public' );
header ( 'Content-length: ' . $file->file_size );
header ( 'Content-type: ' . $file->file_type );
header ( 'Content-transfer-encoding: 8bit' );
header ( 'Content-disposition: attachment; filename="' . $file->file_name . '"' );

$file->getFileSystem ()->read ( $file->file_project, $file->file_real_filename );

flush ();