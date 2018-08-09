<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
global $AppUI, $cal_sdf;

if(!function_exists('classAutoLoader')){
	function classAutoLoader($class){
		include 'invoices_tpl.class.php';
	}
}
spl_autoload_register('classAutoLoader'); 

// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'template_id', 0 );
$invoiceTemplateCategory = apmgetSysVal('InvoiceTemplateType');
$AppUI->getTheme ()->loadCalendarJS ();
// format dates
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$object = new CInvoiceTemplate();
$object->setId ( $object_id );

$canEdit = $object->canEdit();
if (! $canEdit) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$obj = $AppUI->restoreObject ();
if ($obj) {
	$object=$obj;
	$object_id=$object->getId();
} else
{
	//APM load
	$object->getTemplatesById($object_id);
}

if (! $object && $object_id > 0) {
	$AppUI->setMsg ( 'Invoice' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

// setup the title block
$ttl = $object_id ? 'Edit template' : 'Add template';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

$titleBlock->show();

$que = apmgetParam ( $_GET, 'que', 0 );
$user_id = apmgetParam ( $_GET, 'user_id', $AppUI->user_id );
$procVar = apmgetParam ( $_GET, 'procVar', 0 );
$modId = apmgetParam ( $_REQUEST, 'modId', 0 );
$mult = apmgetParam ( $_GET, 'mult', 0 );
$current = apmgetParam ( $_GET, 'current', 0 );

// Load the users
/* $perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'invoices' ); */

$view = new apm_Controllers_View ( $AppUI, $object, 'Invoice' );
$view->setDoSQL('do_invoice_aed_tpl');
$view->setKey('template_id');

//APM TextEditor config
$textEdBaseArea='txtEditor_area';
$textEdSource='template_source';
$lang_textEditor_dict=array('from_computer','from_url','Words','Characters','Font','Fonts','Formatting','Paragraph Format','Font size','Font Size','Insert marker');
$lang_textEditor='var lang =  {';
foreach ($lang_textEditor_dict as $val)
$lang_textEditor.='\''.$val.'\': \''.$AppUI->_($val).'\',';
$lang_textEditor.='};';

$marker_textEditor_dict=array('invoice_name', 'invoice_place', 'invoice_issue_date', 'invoice_pay_date', 'invoice_sale_date', 'invoice_parties_owner', 'invoice_parties_client', 'invoice_total_pay', 'invoice_payed', 'invoice_payment_type', 'invoice_bank_account', 'invoice_authorized_issue', 'invoice_authorized_receive', 'invoice_description', 'invoice_topay_say', 'invoice_topay', 'invoice_items', 'invoice_resume');
$marker_textEditor='var marker = [';
foreach ($marker_textEditor_dict as $val)
{
	switch($val)
	{
		case 'invoice_items':
			$marker_textEditor.='{name:"'.$AppUI->_($val).'", text:"<label id=\"'.$val.'\" class=\"label label-xs label-success drop-inline table-responsive\" style=\"height: 200px;\"><span class=\"fa fa-star fa-marker\"></span>'.$AppUI->_($val).'</label>"},';
		break;
		case 'invoice_resume':
			$marker_textEditor.='{name:"'.$AppUI->_($val).'", text:"<label id=\"'.$val.'\" class=\"label label-xs label-success drop-inline table-responsive\" style=\"height: 50px;\"><span class=\"fa fa-star fa-marker\"></span>'.$AppUI->_($val).'</label>"},';
		break;		
		default:
		$marker_textEditor.='{name:"'.$AppUI->_($val).'", text:"<label id=\"'.$val.'\" class=\"label label-xs label-default\"><span class=\"fa fa-star fa-marker\"></span>'.$AppUI->_($val).'</label>"},';
	}
}
$marker_textEditor.='];';

include ( 'style/_common/addedit_tpl.php' );