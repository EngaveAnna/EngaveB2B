<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'SignatureIdxTab', $_GET, 'tab', 0 );

$signature = new CSignature ();

if (! $signature->canAccess ()) 
{
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $signature->canCreate ();

$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Signatures', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );
$titleBlock->show ();

$signatureTypes = array();

$tabBox = new CTabBox ( '?m=signatures', apm_BASE_DIR . '/modules/signatures/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $signatureTypes, $AppUI->_ ( 'All Signatures', UI_OUTPUT_RAW ) );
}
foreach ( $signatureTypes as $signature_type ) {
	$tabBox->add ( 'index_table', $signature_type );
}

$tabBox->show ();