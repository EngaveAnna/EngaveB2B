<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'signature_id', 0);
$user_id = ( int ) apmgetParam ( $_GET, 'oid', 0);
$signature_mod_name=apmgetParam ( $_GET, 'mod', 0 );
$signature_row =apmgetParam( $_GET, 'id');

$object = new CSignature ();
$object->setId ( $object_id );
$signature_mod_prefix=$object->getPreviewPrefix($signature_mod_name);
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
	$AppUI->setMsg ( 'Signature' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

// setup the title block
$ttl = $object_id ? 'Edit Signature' : 'Add Signature';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete signature', $canDelete, $msg );
}
$titleBlock->show ();



// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'signatures' );

$view = new apm_Controllers_View ( $AppUI, $object, 'Signature' );
echo $view->renderDelete ();
?>
<script language="javascript" type="text/javascript">
function submitIt() {
	var f = document.editFrm;
	f.submit();
}
function popTask() {
    var f = document.editFrm;
    if (f.signature_mod.selectedIndex == 0) {
        alert( "<?php echo $AppUI->_('Please select a project first!', UI_OUTPUT_JS); ?>" );
    } else {
        window.open('./index.php?m=public&a=selector&dialog=1&callback=setTask&table=tasks&task_project='
            + f.signature_mod.options[f.signature_mod.selectedIndex].value, 'task','left=50,top=50,height=250,width=400,resizable')
    }
}

</script>
<?php

if($object_id <= 0)
{
	if($signature_mod_name&&$signature_row)
	{
		if(!$items=$object->getSignatureBySpecData($signature_mod_name,$signature_row,$AppUI->user_id))
		{
			include ('style/_common/new.php');
		}
		else
		{
			$AppUI->setMsg ( 'Signature' );
			$AppUI->setMsg ( '::signature exist', UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}		
	}
	else
	{
		$AppUI->setMsg ( 'Signature' );
		$AppUI->setMsg ( '::uncomplete data to sign', UI_MSG_ERROR, true );
		$AppUI->redirect ( 'm=' . $m );
	}
	
}
else
include ('style/_common/view.php');