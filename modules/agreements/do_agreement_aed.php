<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );
$cb=new CAgreement();
if(!empty($_POST['agreement_source']))
$_POST['sign_src']=$cb->sourceInterpreter($_POST['agreement_source'], $_POST);
$controller = new apm_Controllers_Base ( $cb, $delete, 'Agreements', 'm=agreements', 'm=agreements&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );