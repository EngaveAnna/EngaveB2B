<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'invoice_id', 0 );
$task_id = ( int ) apmgetParam ( $_GET, 'task_id', 0 );
$project_id = ( int ) apmgetParam ( $_GET, 'project_id', 0 );
$status_array = array(0=>$AppUI->_('new'), 1=>$AppUI->_('received'), 2=>$AppUI->_('pinned to an existing resource'), 3=>$AppUI->_('created a task for the invoice'), 4=>$AppUI->_('created a project for this invoice'));
$projectPriority = apmgetSysVal ('ProjectPriority');
$invoiceCategory = apmgetSysVal('InvoiceCategory');
$invoicePaymnetType = apmgetSysVal('InvoicePaymentType');

$object = new CInvoice ();
$object->setId ( $object_id );

global $AppUI, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canView = $obj->canView ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canDelete = $object->canDelete ();
if (! $canView) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$obj = $AppUI->restoreObject ();
if ($obj) {
	$object = $obj;
	$object_id = $object->getId ();
} else {
	$object->load ( $object_id );
}

if (! $object && $object_id > 0) {
	$AppUI->setMsg ( 'Invoice' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

if($object->invoice_task)
{
	$invoice_task = new CTask ();
	$invoice_task->load ( $object->invoice_task );
}
	
if (0 == $object_id && ($project_id || $task_id)) {
	// We are creating a invoice, so if we have them lets figure out the project
	// and task id
	$object->invoice_project = $project_id;
	$object->invoice_task = $task_id;
	
	if ($task_id) {
		$invoice_task = new CTask ();
		$invoice_task->load ( $task_id );
	}
}


$prj = new CProject ();
$projects = $prj->getAllowedProjects ( $AppUI->user_id, false );

foreach ( $projects as $project_id => $project_info ) 
{
	$projects [$project_id] = $project_info ['project_name'];
}

$projects = arrayMerge ( array ('0' => $AppUI->_ ( 'All', UI_OUTPUT_JS )), $projects );

$que = apmgetParam ( $_GET, 'que', 0 );
$user_id = apmgetParam ( $_GET, 'user_id', $AppUI->user_id );
$procVar = apmgetParam ( $_GET, 'procVar', 0 );
$modId = apmgetParam ( $_REQUEST, 'modId', 0 );
$mult = apmgetParam ( $_GET, 'mult', 0 );
$current = apmgetParam ( $_GET, 'current', 0 );

// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'invoices' );
$view = new apm_Controllers_View ( $AppUI, $object, 'Invoice' );

//APM ajaxModal variables
$procVar=array('invoice_project','invoice_task','invoice_parties_owner','invoice_parties_client','sign_u','invoice_template');
$modId=array('selector_'.$object_id,'selector_2_'.$object_id,'selector_3_'.$object_id,'selector_4_'.$object_id,'selector_5_'.$object_id,'selector_6_'.$object_id);
$elemName=array('project_name','task_name', 'company_name', 'company_name', 'contact_name', 'template_name');
$objectType=array('projects','tasks', 'companies', 'companies', 'contacts', 'invoices_templates');
$ajaxList=null;

//APM ajaxList args preparing: array(procVar, elemName, elemId, modId)
foreach($procVar as $key=>$proc)
{
	${$proc}=loadModal($objectType[$key], $object->$proc);
	if(!empty($object->$proc))
	{
		if (is_numeric($object->$proc))
		{
			$val = $object->$proc;
			$ajaxList[$key][]=array('procVar'=>$proc,'elemName'=>${$proc}[$val],'elemId'=>$val,	'modId'=>$modId[$key]);
		}
		elseif($x=explode(',', $object->$proc))
		{
			foreach($x as $xkey=>$val)
			{
				if (is_numeric($val))
				{
	 				$ajaxList[$key][]=array('procVar'=>$proc,'elemName'=>${$proc}[$val],'elemId'=>$val,'modId'=>$modId[$key]);
				}
			}
		}
	}
}


if($object->invoice_id && ($object->sign_ued!=null))
{
	// setup the title block
	$ttl = $object_id ? 'Edit Invoice' : 'Add Invoice';
	$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
	$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );
	$titleBlock->show();
	include ( 'style/_common/view.php' );
}
else
{
	include ( 'addedit.php' );
}
?>