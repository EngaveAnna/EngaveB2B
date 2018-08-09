<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'agreement_id', 0 );
$task_id = ( int ) apmgetParam ( $_GET, 'task_id', 0 );
$project_id = ( int ) apmgetParam ( $_GET, 'project_id', 0 );
$status_array = array(0=>$AppUI->_('new'), 1=>$AppUI->_('received'), 2=>$AppUI->_('pinned to an existing resource'), 3=>$AppUI->_('created a task for the agreement'), 4=>$AppUI->_('created a project for this agreement'));
$projectPriority = apmgetSysVal ('ProjectPriority');
$agreementCategory = apmgetSysVal('AgreementCategory');
$agreementPaymnetType = apmgetSysVal('AgreementPaymentType');

$object = new CAgreement ();
$object->setId ( $object_id );

global $AppUI, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
$canDelete = $object->canDelete ();
if (! $canAddEdit) {
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
	$AppUI->setMsg ( 'Agreement' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}


if($object->agreement_task)
{
	$agreement_task = new CTask ();
	$agreement_task->load ( $object->agreement_task );
}
	
if (0 == $object_id && ($project_id || $task_id)) {
	// We are creating a agreement, so if we have them lets figure out the project
	// and task id
	$object->agreement_project = $project_id;
	$object->agreement_task = $task_id;
	
	if ($task_id) {
		$agreement_task = new CTask ();
		$agreement_task->load ( $task_id );
	}
}

// setup the title block
$ttl = $object_id ? 'Edit Agreement' : 'Add Agreement';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

$titleBlock->show();
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
$users = $perms->getPermittedUsers ( 'agreements' );
$view = new apm_Controllers_View ( $AppUI, $object, 'Agreement' );

//APM ajaxModal variables
$procVar=array('agreement_project','agreement_task','agreement_parties_owner','agreement_parties_client','sign_u','agreement_template');
$modId=array('selector_'.$object_id,'selector_2_'.$object_id,'selector_3_'.$object_id,'selector_4_'.$object_id,'selector_5_'.$object_id,'selector_6_'.$object_id);
$elemName=array('project_name','task_name', 'company_name', 'company_name', 'contact_name', 'template_name');
$objectType=array('projects','tasks', 'companies', 'companies', 'contacts', 'agreements_templates');
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
//APM TextEditor config

$textEdBaseArea='txtEditor_area';
$textEdSource='agreement_source';

$lang_textEditor_dict=array('from_computer','from_url','Words','Characters','Font','Fonts','Formatting','Paragraph Format','Font size','Font Size','Insert marker');
$lang_textEditor='var lang =  {';
foreach ($lang_textEditor_dict as $val)
$lang_textEditor.='\''.$val.'\': \''.$AppUI->_($val).'\',';
$lang_textEditor.='};';

$marker_textEditor_dict=array('agreement_name', 'agreement_category', 'agreement_place', 'date', 'agreement_parties_owner', 'agreement_parties_client', 'agreement_project', 'agreement_task', 'start_date', 'end_date', 'agreement_payment_type', 'agreement_payment_amount', 'sign_u');
$marker_textEditor='var marker = [';
foreach ($marker_textEditor_dict as $val)
$marker_textEditor.='{name:"'.$AppUI->_($val).'", text:"<label id=\"'.$val.'\" class=\"label label-xs label-default\"><span class=\"fa fa-star fa-marker\"></span>'.$AppUI->_($val).'</label>"},';$marker_textEditor.='];';


if($object->agreement_id&&($object->sign_ued!=null))
include ( 'style/_common/view.php' );
else
include ( 'style/_common/addedit.php' );