<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'ValuationIdxTab', $_GET, 'tab', 0 );
$df = $AppUI->getPref ( 'SHDATEFORMAT' ).' ' . $AppUI->getPref ( 'TIMEFORMAT' );
$valuation = new CValuation ();

if (! $valuation->canAccess ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $valuation->canCreate ();


$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Valuations', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=valuations&a=addedit', 'new valuation', '', true );
}
$titleBlock->show();

$valuationStatus = apmgetSysVal ( 'ValuationStatus' );

$tabBox = new CTabBox ( '?m=valuations', apm_BASE_DIR . '/modules/valuations/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $valuationStatus, $AppUI->_ ( 'All Valuations', UI_OUTPUT_RAW ) );
}
foreach ( $valuationStatus as $valuation_type ) {
	$tabBox->add ( 'index_table', $valuation_type );
}

$tabBox->show ();