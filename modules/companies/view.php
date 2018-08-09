<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$company_id = ( int ) apmgetParam ( $_GET, 'company_id', 0 );
$tab = $AppUI->processIntState ( 'CompVwTab', $_GET, 'tab', 0 );
$company = new CCompany ();

if (! $company->load ( $company_id )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$canEdit = $company->canEdit ();
$canDelete = $company->canDelete ();
$deletable = $canDelete; // TODO: this should be removed once the $deletable variable is removed

$contact = new CContact ();
$canCreateContacts = $contact->canCreate ();

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'View Company', 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canCreateContacts) {
	$titleBlock->addCrumb ( '?m=contacts&a=addedit&company_id=' . $company_id, 'new contact', '', true );
}
if ($canEdit) {
	if ($AppUI->isActiveModule ( 'departments' )) {
		$titleBlock->addCrumb ( '?m=departments&a=addedit&company_id=' . $company_id, 'new department', '', true );
	}
	$titleBlock->addCrumb ( '?m=projects&a=addedit&company_id=' . $company_id, 'new project', '', true );
	
	$titleBlock->addCrumb ( '?m=companies&a=addedit&company_id=' . $company_id, 'edit this company' );
	
	if ($canDelete && $deletable) {
		$titleBlock->addCrumbDelete ( 'delete company', $deletable, $msg );
	}
}
$titleBlock->show ();

$view = new apm_Controllers_View ( $AppUI, $company, 'Company' );
echo $view->renderDelete ();

$types = apmgetSysVal ( 'CompanyType' );

include $AppUI->getTheme ()->resolveTemplate ( 'companies/view' );

// tabbed information boxes
$moddir = apm_BASE_DIR . '/modules/companies/';
$tabBox = new CTabBox ( '?m=companies&a=view&company_id=' . $company_id, '', $tab );
$tabBox->add ( $moddir . 'vw_projects', 'Active Projects' );
$tabBox->add ( $moddir . 'vw_projects', 'Archived Projects' );
$tabBox->show ();