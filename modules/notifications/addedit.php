<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'notification_id', 0 );
$task_id = ( int ) apmgetParam ( $_GET, 'task_id', 0 );
$project_id = ( int ) apmgetParam ( $_GET, 'project_id', 0 );
$status_array=array(0=>$AppUI->_('new'), 1=>$AppUI->_('received'), 2=>$AppUI->_('pinned to an existing resource'), 3=>$AppUI->_('created a task for the notification'), 4=>$AppUI->_('created a project for this notification'));
$projectPriority = apmgetSysVal ( 'ProjectPriority' );

$object = new CNotification ();
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
	$AppUI->setMsg ( 'Notification' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

if($object->notification_task)
{
	$notification_task = new CTask ();
	$notification_task->load ( $object->notification_task );
}
	
if (0 == $object_id && ($project_id || $task_id)) {
	// We are creating a notification, so if we have them lets figure out the project
	// and task id
	$object->notification_project = $project_id;
	$object->notification_task = $task_id;
	
	if ($task_id) {
		$notification_task = new CTask ();
		$notification_task->load ( $task_id );
	}
}


// setup the title block
$ttl = $object_id ? 'Edit Notification' : 'Add Notification';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );

if ($canDelete && $object_id) {
	if (! isset ( $msg )) {
		$msg = '';
	}
	$titleBlock->addCrumbDelete ( 'delete notification', $canDelete, $msg );
}
$titleBlock->show ();

$prj = new CProject ();
$projects = $prj->getAllowedProjects ( $AppUI->user_id, false );

foreach ( $projects as $project_id => $project_info ) 
{
	$projects [$project_id] = $project_info ['project_name'];
}

$projects = arrayMerge ( array ('0' => $AppUI->_ ( 'All', UI_OUTPUT_JS )), $projects );
$types = apmgetSysVal ( 'NotificationType' );

// Load the users
$perms = &$AppUI->acl ();
$users = $perms->getPermittedUsers ( 'notifications' );
$view = new apm_Controllers_View ( $AppUI, $object, 'Notification' );

// ajaxModal variables
$procVar='notification_task';
$modId='select_'.$object_id;

// ajaxList args preparing: array(procVar, elemName, elemId, modId)
$ajaxList=null;

if(!empty($object->notification_task))
{
	if (is_numeric($object->notification_task))
	$ajaxList[]=array( 'procVar'=>$procVar, 'elemName'=>$notification_task->task_name, 'elemId'=>$object->notification_task, 'modId'=>$modId);
}

?>

<script language="javascript" type="text/javascript">
function getModalAjaxData(modId, ajaxUrl) {
    if (modId == "") {
        document.getElementById(modId+'_body').innerHTML = "No ID Error";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else if(window.ActiveXObject) {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }else
        {
        	  alert("Your browser does not support XMLHTTP!");
       	}
        xmlhttp.overrideMimeType('text/xml');
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById(modId+'_body').innerHTML=xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET",ajaxUrl,true);
        xmlhttp.send();
    }
}


function modalAjaxProcess(obj, procVar, elemName, elemId, modId)
{
	if(obj.checked===true)
	updateAjaxVal(obj, procVar, elemName, elemId, modId);
	else
	removeAjaxVal(procVar, elemName, elemId, modId);
}

function updateAjaxVal(obj, procVar, elemName, elemId, modId)
{
	if(obj.type=='radio')
	{
		document.getElementById(modId+'_area').innerHTML='<p id="'+modId+'_'+elemId+'"><button class="btn btn-default" type="button" onclick="removeAjaxVal(\''+procVar+'\', \'\', \''+elemId+'\', \''+modId+'\');"><span class="glyphicon glyphicon-remove"></span></button>&nbsp;'+elemName+'</p>';
		arr=[elemId];
	}
	else
	{
		if(document.getElementById(modId+'_'+elemId)==null)
			document.getElementById(modId+'_area').innerHTML+='<p id="'+modId+'_'+elemId+'"><button class="btn btn-default" type="button" onclick="removeAjaxVal(\''+procVar+'\', \'\', \''+elemId+'\', \''+modId+'\');"><span class="glyphicon glyphicon-remove"></span></button>&nbsp;'+elemName+'</p>';

			var arr;
			var str=document.getElementById(procVar).value;
			if(str!='')
			{	
				arr=str.split(',');
				arr.push(elemId);
			}
			else
			{
				arr=[elemId];
			}
		    document.getElementById(procVar).value=arr.toString();

	}
}


function removeAjaxVal(procVar, elemName, elemId, modId)
{
	if(document.getElementById(modId+'_'+elemId))
	{
		var chl = document.getElementById(modId+'_'+elemId)
		chl.parentNode.removeChild(chl);
	}
	
	var str=null;
	str=document.getElementById(procVar).value;

	var arr=str.split(',');

    for(var i = arr.length; i--;) {
        if(arr[i] === elemId) {
            arr.splice(i, 1);
        }
    }

    document.getElementById(procVar).value=arr.toString();
}

function getModalAjaxProcVar(procVar, type)
{
	if(type)
	return document.getElementById(procVar).options[document.getElementById(procVar).selectedIndex].value;
	else
	return document.getElementById(procVar).value;
}

function submitIt() {
	var f = document.editFrm;
	f.submit();
}
function popTask() {
    var f = document.editFrm;
    if (f.notification_project.selectedIndex == 0) {
        alert( "<?php echo $AppUI->_('Please select a project first!', UI_OUTPUT_JS); ?>" );
    } else {
        window.open('./index.php?m=public&a=selector&dialog=1&callback=setTask&table=tasks&task_project='
            + f.notification_project.options[f.notification_project.selectedIndex].value, 'task','left=50,top=50,height=250,width=400,resizable')
    }
}

// Callback function for the generic selector
function setTask( key, val ) {
    var f = document.editFrm;
    if (val != '') {
        f.notification_task.value = key;
        f.task_name.value = val;
    } else {
        f.notification_task.value = '0';
        f.task_name.value = '';
    }
}
</script>
<?php
echo $view->renderDelete();
include ( 'style/_common/addedit.php' );