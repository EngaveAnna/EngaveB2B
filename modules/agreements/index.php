<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'AgreementIdxTab', $_GET, 'tab', 0 );
$submod= apmgetParam ( $_GET, 'submod', '' );

$agreement = new CAgreement ();
if (! $agreement->canAccess ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $agreement->canCreate ();
// get the list of visible companies
$extra = array (
		'from' => 'agreements',
		'where' => 'projects.project_id = agreement_project'
);

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
if(!$submod=='template')
{
	$titleBlock = new apm_Theme_TitleBlock ( 'Agreements', 'icon.png', $m );
	$titleBlock->addSearchCell ( $search_string );
	
	if ($canCreate) {
		$titleBlock->addCrumb ( '?m=agreements&a=addedit', 'new agreement', '', true );
		$titleBlock->addCrumb ( '?m=agreements&submod=templates', $AppUI->_('Agreements templates'), '', true );
	}
	
	$titleBlock->show ();
	$agreementTypes = apmgetSysVal ( 'AgreementCategory' );
	
	$tabBox = new CTabBox ( '?m=agreements', apm_BASE_DIR . '/modules/agreements/', $tab );
	if ($tabBox->isTabbed ()) {
		array_unshift ( $agreementTypes, $AppUI->_ ( 'All Agreements', UI_OUTPUT_RAW ) );
	}
	foreach ( $agreementTypes as $agreement_type ) {
		$tabBox->add ( 'index_table', $agreement_type );
	}
	$tabBox->show ();
}
else
{
	$titleBlock = new apm_Theme_TitleBlock ( 'Agreements templates', 'icon.png', $m );
	$titleBlock->addSearchCell ( $search_string );
	
	if ($canCreate) {
		$titleBlock->addCrumb ( '?m=agreements&a=addedit_tpl&submod=template', 'new agreement template', '', true );
		$titleBlock->addCrumb('?m=agreements', $AppUI->_('Agreements list'));
	}
	$titleBlock->show ();

	$agreementTypes = apmgetSysVal ( 'AgreementTemplateCategory' );
	
	$tabBox = new CTabBox ( '?m=agreements&submod=template', apm_BASE_DIR . '/modules/agreements/', $tab );
	if ($tabBox->isTabbed ()) {
		array_unshift ( $agreementTypes, $AppUI->_ ( 'All templates', UI_OUTPUT_RAW ) );
	}
	foreach ( $agreementTypes as $agreement_type ) {
		$tabBox->add ( 'index_table_tpl', $agreement_type );
	}

	$tabBox->show ();
}
