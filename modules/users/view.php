<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
$user_id = ( int ) apmgetParam ( $_GET, 'user_id', 0 );

$tab = $AppUI->processIntState ( 'UserVwTab', $_GET, 'tab', 0 );
$addPwT = $AppUI->processIntState ( 'addProjWithTasks', $_POST, 'add_pwt', 0 );

$user = new CUser ();

if (! $user->load ( $user_id )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$canEdit = $user->canEdit ();
$user->loadFull ( $user_id );

global $addPwT, $company_id, $dept_ids, $department, $min_view, $m, $a;

if ($user_id != $AppUI->user_id && (! $perms->checkModuleItem ( 'users', 'view', $user_id ) || ! $perms->checkModuleItem ( 'users', 'view', $user_id ))) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$company_id = $AppUI->getState ( 'UsrProjIdxCompany' ) !== null ? $AppUI->getState ( 'UsrProjIdxCompany' ) : $AppUI->user_company;

$company_prefix = 'company_';

if (isset ( $_POST ['department'] )) {
	$AppUI->setState ( 'UsrProjIdxDepartment', $_POST ['department'] );
	
	// if department is set, ignore the company_id field
	unset ( $company_id );
}
$department = $AppUI->getState ( 'UsrProjIdxDepartment' ) !== null ? $AppUI->getState ( 'UsrProjIdxDepartment' ) : $company_prefix . $AppUI->user_company;

// if $department contains the $company_prefix string that it's requesting a company and not a department. So, clear the
// $department variable, and populate the $company_id variable.
if (! (strpos ( $department, $company_prefix ) === false)) {
	$company_id = substr ( $department, strlen ( $company_prefix ) );
	$AppUI->setState ( 'UsrProjIdxCompany', $company_id );
	unset ( $department );
}

$contact = new CContact ();
$contact->contact_id = $user->user_contact;
$methods = $contact->getContactMethods ();
$methodLabels = apmgetSysVal ( 'ContactMethods' );

$countries = apmgetSysVal ( 'GlobalCountries' );
// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'View User', 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );
if ($canEdit || $user_id == $AppUI->user_id) {
	// APMoff $titleBlock->addCell('<div class="crumb"><ul><li><a href="javascript: void(0);" onclick="popChgPwd();return false"><span>' . $AppUI->_('change password') . '</span></a></li></ul></div>');
	$titleBlock->addCrumb ( '?m=users&a=addedit&user_id=' . $user_id, 'edit this user' );
	$titleBlock->addCrumb ( '?m=contacts&a=addedit&contact_id=' . $user->contact_id, 'edit this contact' );
	$titleBlock->addCrumb ( '?m=system&a=addeditpref&user_id=' . $user_id, 'edit preferences' );
}
$titleBlock->show ();

$utypes = apmgetSysVal ( 'UserType' );
include $AppUI->getTheme ()->resolveTemplate ( 'users/view' );
// tabbed information boxes
$min_view = true;

$tabBox = new CTabBox ( '?m=users&a=view&user_id=' . $user_id, '', $tab );
$tabBox->add ( apm_BASE_DIR . '/modules/users/vw_usr_log', 'User Log' );
$tabBox->add ( apm_BASE_DIR . '/modules/users/vw_usr_perms', 'Permissions' );
$tabBox->add ( apm_BASE_DIR . '/modules/users/vw_usr_roles', 'Roles' );
$tabBox->show ();

//include apm_BASE_DIR . '/modules/users/vw_usr_perms.php';