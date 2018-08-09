<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'valuation_id', 0 );
$object = new CValuation ();
$object->setId ( $object_id );

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
	$AppUI->setMsg ( 'Valuation' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

global $AppUI, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();
$df = $AppUI->getPref ( 'SHDATEFORMAT' ).' ' . $AppUI->getPref ( 'TIMEFORMAT' );

// setup the title block
$ttl = $object_id ? 'Edit Valuation' : 'Add Valuation';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete valuation', $canDelete, $msg );
}
$titleBlock->show ();

$types = apmgetSysVal ( 'ValuationType' );


// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'valuations' );
$view = new apm_Controllers_View ( $AppUI, $object, 'Valuation' );

$valuationStatus = apmgetSysVal ('ValuationStatus');
$valuationType = apmgetSysVal ('ValuationType');

//APM ajaxModal variables
$procVar=array('valuation_project');
$modId=array('selector_'.$object_id);
$elemName=array('project_name');
$objectType=array('projects');
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

?>
<script language="javascript" type="text/javascript">

function submitIt() {
	var f = document.editFrm;
	f.submit();
}
</script>

<?php
echo $view->renderDelete();
include ( 'style/_common/addedit.php' );
?>
