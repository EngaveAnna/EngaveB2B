<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'PaymentIdxTab', $_GET, 'tab', 0 );
$df = $AppUI->getPref ( 'SHDATEFORMAT' ).' ' . $AppUI->getPref ( 'TIMEFORMAT' );
$payment = new CPayment ();

if (! $payment->canAccess ()) {
	$AppUI->redirect ( ACCESS_DENIED );
}
$canCreate = $payment->canCreate ();


$search_string = apmgetParam ( $_POST, 'search_string', '' );
$AppUI->setState ( $m . '_search_string', $search_string );
$search_string = apmformSafe ( $search_string, true );

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Payments', 'icon.png', $m );
$titleBlock->addSearchCell ( $search_string );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=payments&a=addedit', 'new payment', '', true );
}
$titleBlock->show();

$paymentStatus = apmgetSysVal ( 'PaymentStatus' );

$tabBox = new CTabBox ( '?m=payments', apm_BASE_DIR . '/modules/payments/', $tab );
if ($tabBox->isTabbed ()) {
	array_unshift ( $paymentStatus, $AppUI->_ ( 'All Payments', UI_OUTPUT_RAW ) );
}
foreach ( $paymentStatus as $payment_type ) {
	$tabBox->add ( 'index_table', $payment_type );
}

$tabBox->show ();