<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template
$object_id = ( int ) apmgetParam ( $_GET, 'project_id', 0 );
$company_id = ( int ) apmgetParam ( $_GET, 'company_id', $AppUI->user_company );
$contact_id = ( int ) apmgetParam ( $_GET, 'contact_id', 0 );

$object = new CProject ();
$object->setId ( $object_id );

$obj = $object;
$canAddEdit = $obj->canAddEdit ();
$canAuthor = $obj->canCreate ();
$canEdit = $obj->canEdit ();
if (! $canAddEdit) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$obj = $AppUI->restoreObject ();
if ($obj) {
	$object = $obj;
	$object_id = $object->getId ();
} else {
	$object->loadFull ( null, $object_id );
}
if (! $object && $object_id > 0) {
	$AppUI->setMsg ( 'Project' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

global $AppUI, $cal_sdf;
$AppUI->getTheme ()->loadCalendarJS ();

$pstatus = apmgetSysVal ( 'ProjectStatus' );
$ptype = apmgetSysVal ( 'ProjectType' );

$structprojs = $object->getAllowedProjects ( $AppUI->user_id, false );
unset ( $structprojs [$object_id] );
foreach ( $structprojs as $key => $tmpInfo ) {
	$structprojs [$key] = $tmpInfo ['project_name'];
}
$structprojects = arrayMerge ( array (
		'0' => '(' . $AppUI->_ ( 'No Parent' ) . ')' 
), $structprojs );

// get a list of permitted companies
$company = new CCompany ();
$companies = $company->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$companies = arrayMerge ( array (
		'0' => '' 
), $companies );

if (count ( $companies ) < 2 && $object_id == 0) {
	$AppUI->setMsg ( 'noCompanies', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}
if ($object_id == 0 && $company_id > 0) {
	$object->project_company = $company_id;
}

// add in the existing company if for some reason it is dis-allowed
if ($object_id && ! array_key_exists ( $object->project_company, $companies )) {
	$companies [$object->project_company] = $company->load ( $object->project_company )->company_name;
}

// get critical tasks (criteria: task_end_date)
$criticalTasks = ($object_id > 0) ? $object->getCriticalTasks () : null;

// get ProjectPriority from sysvals
$projectPriority = apmgetSysVal ( 'ProjectPriority' );

// format dates
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$end_date = intval ( $object->project_end_date ) ? new apm_Utilities_Date ( $object->project_end_date ) : null;
$actual_end_date = intval ( $criticalTasks [0] ['task_end_date'] ) ? new apm_Utilities_Date ( $criticalTasks [0] ['task_end_date'] ) : null;
$style = (($actual_end_date > $end_date) && ! empty ( $end_date )) ? 'style="font-weight:bold"' : '';

// setup the title block
$ttl = $object_id > 0 ? 'Edit Project' : 'Create Project';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );
$titleBlock->addViewLink ( 'project', $object_id );
$titleBlock->show ();

$canDelete = $object->canDelete ();
// Get contacts list
$selected_contacts = array ();

if ($object_id) {
	$myContacts = $object->getContactList ();
	$selected_contacts = array_keys ( $myContacts );
}
if ($object_id == 0 && $contact_id > 0) {
	$selected_contacts [] = '' . $contact_id;
}

// Get the users notification options
$tl = $AppUI->getPref ( 'TASKLOGEMAIL' );
$ta = $tl & 1;
$tt = $tl & 2;
$tp = $tl & 4;


// ajaxModal variables
$project_contacts=implode(',', $selected_contacts);
$procVar='project_contacts';
$modId='contacts_'.( int ) apmgetParam ( $_GET, 'project_id', 0 );

// ajaxList args preparing: array(procVar, elemName, elemId, modId)
$ajaxListContacts=null;

if(!empty(${$procVar}))
{
	if (is_numeric(${$procVar}))
	$ajaxListContacts[]=array( 'procVar'=>$procVar, 'elemName'=>$myContacts[${$procVar}]['contact_first_name'].' '.$myContacts[${$procVar}]['contact_last_name'], 'elemId'=>${$procVar}, 'modId'=>$modId);
	elseif($x=explode(',', ${$procVar}))
	{
		foreach($x as $val)
		{
			if (is_numeric($val))
			$ajaxListContacts[] = array( 'procVar'=>$procVar, 'elemName'=>$myContacts[$val]['contact_first_name'].' '.$myContacts[$val]['contact_last_name'], 'elemId'=>$val, 'modId'=>$modId);
		}
	}
}

?>
<script src="./js/modalAjax.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">

function setColor(color) {
	var f = document.editFrm;
	if (color) {
		f.project_color_identifier.value = color;
	}
	document.getElementById('test').style.background = '#' + f.project_color_identifier.value; 		//fix for mozilla: does this work with ie? opera ok.
}

function setShort() {
	var f = document.editFrm;
	var x = 10;
	if (f.project_name.value.length < 11) {
		x = f.project_name.value.length;
	}
	if (f.project_short_name.value.length == 0) {
		f.project_short_name.value = f.project_name.value.substr(0,x);
	}
}

function submitIt() {
	var f = document.editFrm;
	var msg = '';

	<?php
	/*
	 * * Automatic required fields generated from System Values
	 */
	$requiredFields = apmgetSysVal ( 'ProjectRequiredFields' );
	echo apmrequiredFields ( $requiredFields );
	?>

	if (msg.length < 1) {
		f.submit();
	} else {
		alert(msg);
	}
}



function popDepartment() {
        var f = document.editFrm;
	var url = './index.php?m=public&a=selector&dialog=1&callback=setDepartment&table=departments&company_id='
            + f.project_company.options[f.project_company.selectedIndex].value;
        window.open(url,'dept','left=50,top=50,height=250,width=400,resizable');
}

function setDepartment(department_id_string){
	if(!department_id_string){
		department_id_string = '';
	}
	document.editFrm.project_departments.value = department_id_string;
	selected_departments_id = department_id_string;
}

</script>
<?php

include $AppUI->getTheme ()->resolveTemplate ( 'projects/addedit' );