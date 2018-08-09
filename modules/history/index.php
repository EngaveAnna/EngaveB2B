<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$filter_param = apmgetParam ( $_REQUEST, 'filter', '' );

$options = array ();
$options [- 1] = $AppUI->_ ( 'Show all' );
$options = $options + $AppUI->getActiveModules ();
$options ['login'] = $AppUI->_ ( 'Login/Logouts' );

/*
 * This validates that anything provided via the filter_param is definitely an
 * active module and not some other crazy garbage.
 */
if (! isset ( $options [$filter_param] )) {
	$filter_param = 'projects';
}

$titleBlock = new apm_Theme_TitleBlock ( 'History', 'icon.png', $m );
$titleBlock->addFilterCell ( 'Changes to', 'filter', $options, $filter_param );
$titleBlock->show ();

$tabBox = new CTabBox ( '?m=history', apm_BASE_DIR . '/modules/history/' );
$tabBox->add ( 'index_table', $AppUI->_ ( 'History' ) );
$tabBox->show ();