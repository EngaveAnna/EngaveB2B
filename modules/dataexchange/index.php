<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once 'dataexchange.class.php';
$canAuthor = canAdd ( 'projects' );
if (! $canAuthor) {
	$AppUI->redirect ( "m=public&a=access_denied" );
}

$canEdit = canEdit ( $m );
$AppUI->setState ( "msimportIdxTab", $tab );
$titleBlock = new apm_Theme_TitleBlock ( 'dataexchange', 'projectimporter.png', $m, "$m.$a" );
$titleBlock->show ( false );

$tab = $AppUI->processIntState ( 'LinkIdxTab', $_GET, 'tab', 0 );
$tabBox = new CTabBox ( "?m=$m", apm_BASE_DIR . "/modules/$m/", $tab );

if ($canEdit) {
	$tabBox->add ( 'vw_idx_export', $AppUI->_ ( 'Export' ), '0' );
	$tabBox->add ( 'vw_idx_import', $AppUI->_ ( 'Import' ), '1' );
}
$tabBox->show ();