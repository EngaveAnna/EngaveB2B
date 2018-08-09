<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
require_once 'invoices_tpl.class.php';
$tab = $AppUI->processIntState ( 'InvoiceIdxTab', $_GET, 'tab', 0 );
$submod= apmgetParam ( $_GET, 'submod', '' );

$invoice = new CInvoice ();
if (! $invoice->canAccess ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $invoice->canCreate ();
// get the list of visible companies
$extra = array (
		'from' => 'invoices',
		'where' => 'projects.project_id = invoice_project'
);

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
if(!$submod=='template')
{
	$titleBlock = new apm_Theme_TitleBlock ( 'Invoices', 'icon.png', $m );
	$titleBlock->addSearchCell ( $search_string );
	
	if ($canCreate) {
		$titleBlock->addCrumb ( '?m=invoices&a=addedit', 'new invoice', '', true );
		$titleBlock->addCrumb ( '?m=invoices&submod=templates', $AppUI->_('Invoices templates'), '', true );
	}
	
	$titleBlock->show ();
	$invoiceTypes = apmgetSysVal ( 'InvoiceCategory' );
	
	$tabBox = new CTabBox ( '?m=invoices', apm_BASE_DIR . '/modules/invoices/', $tab );
	if ($tabBox->isTabbed ()) {
		array_unshift ( $invoiceTypes, $AppUI->_ ( 'All Invoices', UI_OUTPUT_RAW ) );
	}
	foreach ( $invoiceTypes as $invoice_type ) {
		$tabBox->add ( 'index_table', $invoice_type );
	}
	$tabBox->show ();
}
else
{
	$titleBlock = new apm_Theme_TitleBlock ( 'Invoices templates', 'icon.png', $m );
	$titleBlock->addSearchCell ( $search_string );
	
	if ($canCreate) {
		$titleBlock->addCrumb ( '?m=invoices&a=addedit_tpl&submod=template', 'new invoice template', '', true );
		$titleBlock->addCrumb('?m=invoices', $AppUI->_('Invoices list'));
	}
	$titleBlock->show ();

	$invoiceTypes = apmgetSysVal ( 'InvoiceTemplateCategory' );
	
	$tabBox = new CTabBox ( '?m=invoices&submod=templates', apm_BASE_DIR . '/modules/invoices/', $tab );
	if ($tabBox->isTabbed ()) {
		array_unshift ( $invoiceTypes, $AppUI->_ ( 'All templates', UI_OUTPUT_RAW ) );
	}
	foreach ( $invoiceTypes as $invoice_type ) {
		$tabBox->add ( 'index_table_tpl', $invoice_type );
	}

	$tabBox->show ();
}
