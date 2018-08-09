<?php
$t=apmgetParam($_GET, 't', 0);
switch($t)
{


	case 'contact_selector':

		// if (!defined('apm_BASE_DIR')){
		// die('You should not access this file directly.');
		// }
		// @todo remove database query
		$show_all = apmgetParam ( $_REQUEST, 'show_all', 0 );
		$modId = apmgetParam ( $_REQUEST, 'modId', 0 );
		$procVar=apmgetParam ( $_REQUEST, 'procVar', 0 );
		$company_id = apmgetParam ( $_REQUEST, 'company_id', 0 );
		$contact_id = apmgetParam ( $_POST, 'contact_id', 0 );
		$selected_contacts_id = apmgetParam ( $_GET, 'selected_contacts_id', '' );
		if (apmgetParam ( $_POST, 'selected_contacts_id' )) {
			$selected_contacts_id = apmgetParam ( $_POST, 'selected_contacts_id' );
		}

		$company_name = '';

		// Remove any empty elements
		$key_id = array_filter ( explode ( ',', $selected_contacts_id ) );
		$selected_contacts_id = implode ( ',', $contacts_id );

		$q = new apm_Database_Query ();

		if (strlen ( $selected_contacts_id ) > 0 && ! $show_all && ! $company_id) {
			$q->addTable ( 'contacts' );
			$q->addQuery ( 'DISTINCT contact_company' );
			$q->addWhere ( 'contact_id IN (' . $selected_contacts_id . ')' );
			$where = implode ( ',', $q->loadColumn () );
			$q->clear ();
			if (substr ( $where, 0, 1 ) == ',') {
				$where = '0' . $where;
			}
			$where = (($where) ? ('contact_company IN(' . $where . ')') : '');
		} elseif (! $company_id && ! $show_all) {
			// Contacts from all allowed companies
			$where = '(contact_company IS NULL OR contact_company = 0)';
			$company_name = $AppUI->_ ( 'Not assigned to company' );
		} elseif ($show_all) {
			$company_name = $AppUI->_ ( 'Allowed Companies' );
		} else {
			// Contacts for this company only
			$q->addWhere ( 'contact_company = ' . ( int ) $company_id );
		}

		// This should now work on company ID, but we need to be able to handle both
		$q->addTable ( 'contacts', 'a' );
		$q->leftJoin ( 'companies', 'b', 'company_id = contact_company' );
		$q->leftJoin ( 'departments', 'c', 'dept_id = contact_department' );
		$q->addQuery ( 'contact_id, contact_first_name, contact_last_name, contact_company, contact_department' );
		$q->addQuery ( 'company_name' );
		$q->addQuery ( 'dept_name' );
		if (isset ( $where ) && $where) { // Don't assume where is set. Change needed to fix Mantis Bug 0002056
			$q->addWhere ( $where );
		}
		if (isset ( $where_dept ) && $where_dept) { // Don't assume where is set. Change needed to fix Mantis Bug 0002056
			$q->addWhere ( $where_dept );
		}
		$oCpy = new CCompany ();
		$aCpies = $oCpy->getAllowedRecords ( $AppUI->user_id, 'company_id, company_name', 'company_name' );
		$where = $oCpy->getAllowedSQL ( $AppUI->user_id, 'contact_company' );
		$q->addWhere ( $where );

		$oDpt = new CDepartment ();
		$where = $oDpt->getAllowedSQL ( $AppUI->user_id, 'contact_department' );
		$q->addWhere ( $where );

		$q->addWhere ( '(contact_owner = ' . ( int ) $AppUI->user_id . ' OR contact_private = 0)' );
		$q->addOrder ( 'company_name, contact_company, dept_name, contact_department, contact_last_name' ); // May need to review this.

		$contacts = $q->loadHashList ( 'contact_id' );

		$actual_department = '';
		$actual_company = '';
		$companies_names = array (0 => $AppUI->_ ( 'Select a company' )) + $aCpies;
		$ajaxUrl='./index.php?m=public&a=modalajax&suppressHeaders=true&t=contact_selector&modId='.$modId.'&procVar='.$procVar.'&company_id=';
		echo arraySelect ( $companies_names, 'company_id', 'onchange="getModalAjaxData(\''.$modId.'\', \''.$ajaxUrl.'\'+this.value+\'&selected_contacts_id=\'+getModalAjaxProcVar(\''.$procVar.'\',false));"', $company_id );
		$ajaxUrl='./index.php?m=public&a=modalajax&suppressHeaders=true&t=contact_selector&modId='.$modId.'&procVar='.$procVar.'&company_id='.$company_id;
		?>
<div style="padding:10px 0;">
<a class="btn btn-default" href="javascript: void(0);" onclick="getModalAjaxData('<?php echo $modId;?>', '<?php echo $ajaxUrl;?>&show_all=1&selected_contacts_id='+getModalAjaxProcVar('<?php echo $procVar; ?>',false));">
<?php echo $AppUI->_('View all allowed contacts'); ?>
</a>
</div>

<?php
if($contacts)
{
$s1 ='<div class="table-responsive"><table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static"><tbody>';

foreach ( $contacts as $contact_id => $contact_data ) {
	$checked = in_array ( $contact_id, $contacts_id ) ? 'checked="checked"' : '';
	$u .= '<tr><td class="apm-label"><input type="checkbox" class="form-control" name="contact_id[]" onclick="modalAjaxProcess(this, \''.$procVar.'\', \''.$contact_data ['contact_first_name'] . ' ' . $contact_data ['contact_last_name'].'\',\''.$contact_id.'\', \''.$modId.'\');" id="contact_' . $contact_id . '" value="' . $contact_id . '" ' . $checked . ' /></td>';
	$u .= '<td>'.$contact_data ['contact_first_name'] . ' ' . $contact_data ['contact_last_name'] . '</td>';
	$u .= '</tr>';
}

$s2='</tbody></table></div>';
echo $s1.$u.$s2;
}
else
echo $AppUI->_ ( 'No results for search criteria' );

break;
case 'selector':

	$que = apmgetParam ( $_GET, 'que', 0 );
	$user_id = apmgetParam ( $_GET, 'user_id', $AppUI->user_id );
	$procVar = apmgetParam ( $_GET, 'procVar', 0 );
	$modId = apmgetParam ( $_REQUEST, 'modId', 0 );
	$mult = apmgetParam ( $_GET, 'mult', 0 );
	$current = apmgetParam ( $_GET, 'current', 0 );

	$q = new apm_Database_Query ();
	$q->addTable ( $que );
	$optionsJSON='';
	$query_result = false;
	
	switch ($que) {
		case 'contacts' :
			$obj = new CContact ();
			$title = 'Contact';
			$q->addQuery ( 'contact_id' );
			$q->addQuery ( 'CONCAT_WS(\'  \',contact_first_name, contact_last_name) AS contact_name' );
			$q->addOrder ( 'contact_last_name ASC' );
			$resultList = $q->loadHashList ();
		break;
		case 'companies' :
			$obj = new CCompany ();
			$title = 'Company';
			$q->addQuery ( 'company_id, company_name' );
			$q->addOrder ( 'company_name' );
			$q->addWhere ( selPermWhere ( $obj, 'company_id', 'company_name' ) );
			$resultList = $q->loadHashList ();
			break;
		case 'departments' :
			// known issue: does not filter out denied companies
			$title = 'Department';
			$company_id = apmgetParam ( $_GET, 'company_id', 0 );
			$obj = new CDepartment ();
			$q->addWhere ( selPermWhere ( $obj, 'dept_id', 'dept_name' ) );
			$q->addWhere ( 'dept_company = company_id ' );
			$q->addTable ( 'companies', 'b' );
	
			$company = new CCompany ();
			$allowed = $company->getAllowedRecords ( $AppUI->user_id, 'company_id, company_name' );
			if (count ( $allowed )) {
				$q->addWhere ( 'company_id IN (' . implode ( ',', array_keys ( $allowed ) ) . ') ' );
			}
	
			$hide_company = apmgetParam ( $_GET, 'hide_company', 0 );
			$q->addQuery ( 'dept_id' );
			if ($hide_company == 1) {
				$q->addQuery ( 'dept_name' );
			} else {
				$q->addQuery ( 'CONCAT_WS(\': \',company_name,dept_name) AS dept_name' );
			}
			if ($company_id) {
				$q->addWhere ( 'dept_company = ' . ( int ) $company_id );
				$q->addOrder ( 'dept_name' );
			} else {
				$q->addOrder ( 'company_name, dept_name' );
			}
			$resultList = $q->loadHashList ();
			break;
		case 'files' :
			$title = 'File';
			$q->addQuery ( 'file_id,file_name' );
			$q->addOrder ( 'file_name' );
			$resultList = $q->loadHashList ();
			break;
		case 'forums' :
			$title = 'Forum';
			$q->addQuery ( 'forum_id,forum_name' );
			$q->addOrder ( 'forum_name' );
			$resultList = $q->loadHashList ();
			break;
		case 'projects' :
			$project_company = apmgetParam ( $_GET, 'project_company', $AppUI->user_company );

			if ($user_id > 0) {
				$projectList = CContact::getProjects ( $user_id );
			} else {
				$projectList = CCompany::getProjects ( $AppUI, $project_company );
			}
			foreach ( $projectList as $project ) {
				$resultList [$project ['project_id']] = $project ['project_name'];
			}
			break;
		case 'valuationproject':
			$project_company = apmgetParam ( $_GET, 'project_company', $AppUI->user_company );
			
			if ($user_id > 0) {
				$projectList = CContact::getProposedProjects ( $user_id );
			} else {
				$projectList = CCompany::getProposedProjects ( $AppUI, $project_company );
			}
			foreach ( $projectList as $project ) {
				$resultList [$project ['project_id']] = $project ['project_name'];
			}
			break;
		case 'tasks' :
			$title = 'Task';
			$task_project = ( int ) apmgetParam ( $_GET, 'task_project', 0 );
	
			$myTask = new CTask ();
			$task_list = $myTask->getAllowedTaskList ( null, $task_project );
	
			$level = 0;
			$query_result = array ();
			$last_parent = 0;
			foreach ( $task_list as $task ) {
				if ($task ['task_parent'] != $task ['task_id']) {
					if ($last_parent != $task ['task_parent']) {
						$last_parent = $task ['task_parent'];
						$level ++;
					}
				} else {
					$last_parent = 0;
					$level = 0;
				}
				$query_result [$task ['task_id']] = ($level ? str_repeat ( '&nbsp;&nbsp;', $level ) : '') . $task ['task_name'];
			}
			break;
		case 'agreements_templates' :
			$title = 'Agreement template';
			$q->addQuery ( '*' );
			$q->addWhere('template_owner='.$AppUI->user_id.' OR template_category=1');			
			$q->addOrder ( 'template_date ASC' );
			$resultList = $q->loadHashList (null,true);
			$optionsList =  $q->loadList();
			break;
		case 'invoices_templates' :
			$title = 'Invoice template';
			$q->addQuery ( '*' );
			$q->addWhere('template_owner='.$AppUI->user_id.' OR template_category=1');
			$q->addOrder ( 'template_date ASC' );
			$resultList = $q->loadHashList (null,true);
			$optionsList =  $q->loadList();
			break;			
		case 'users' :
			$title = 'User';
			$q->addTable ( 'contacts', 'c' );
			$q->addTable ( 'users', 'u' );
			$q->addQuery ( 'u.user_id');
			$q->addQuery ( 'CONCAT_WS(\'  \', c.contact_first_name, c.contact_last_name) AS user_name' );
			$q->addWhere ( 'c.contact_id = u.user_contact' );
			$q->addOrder ( 'u.user_id' );
			$resultList = $q->loadHashList ();
			break;
		case 'SGD' :
			$title = 'Document';
			$q->addQuery ( 'SGD_id, SGD_name' );
			$q->addOrder ( 'SGD_name' );
			$resultList = $q->loadHashList ();
			break;
		default :
			$ok = false;
			break;
	}
		$list =  $query_result ? $query_result : $resultList;
		
		if (count ( $list ) >= 1) {
			if($mult!='false')
			{	
				echo '<div class="alert alert-success"><span class="glyphicon glyphicon-circle-ok"></span><a class="close" data-dismiss="alert" href="#">×</a>'.$AppUI->_ ('Select one or more elements').'</div>';
			}
			else
			{ 
				echo '<div class="alert alert-warning"><span class="glyphicon glyphicon-circle-ok"></span><a class="close" data-dismiss="alert" href="#">×</a>'.$AppUI->_ ('Select only one element').'</div>';
			}
			$s1 ='<div class="table-responsive"><table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static"><tbody>';
			$u=null;

			$current_ar=explode(',', $current);
			
			//APM Options i loadList so row id isnt object id
			$optionsId=0;
			
			foreach ( $list as $key => $val ) 
			{	
				$name=htmlspecialchars ( $val, ENT_QUOTES );
				
				$signed='';
				
				if(is_array($current_ar))
				$signed =  in_array ( $key, $current_ar ) ? 'checked="checked"' : '';
				else
				$signed = 'checked';

				if($mult!='false') 
				{ 
					$inputType="checkbox";
					$inputName=$key;
					$inputValue=$key;
				}
				else
				{
					$inputType="radio";
					$inputName=$procVar;
					$inputValue=$key;
				}

				$onclick='modalAjaxProcess(this, \''.$procVar.'\', \''.$name.'\',\''.$key.'\', \''.$modId.'\');';
				
				if(isset($optionsList))
				{
					switch($que)
					{
						case 'agreements_templates':
							$optionsJSON=addslashes(json_encode($optionsList[$optionsId]));
							$options='<label class="label label-default label-xs label-micro">ID: '.$optionsList[$optionsId]['template_id'].'</label>';
							if($optionsList[$optionsId]['template_category']=='1')
								$options.='<label class="label label-xs label-success">'.$AppUI->_('Public').'</label>';
							$options.=' ['.$optionsList[$optionsId]['template_date'].'] ';
							$onclick='modalAjaxTemplate(this, \''.$procVar.'\', \''.$name.'\',\''.$key.'\', \''.$modId.'\', \''.htmlspecialchars($optionsJSON).'\', true);';
							break;
						case 'invoices_templates':							
							$optionsJSON=addslashes(json_encode($optionsList[$optionsId]));
							$options='<label class="label label-default label-xs label-micro">ID: '.$optionsList[$optionsId]['template_id'].'</label>';
							if($optionsList[$optionsId]['template_category']=='1')
								$options.='<label class="label label-xs label-success">'.$AppUI->_('Public').'</label>';
							$options.=' ['.$optionsList[$optionsId]['template_date'].'] ';
							$onclick='modalAjaxTemplate(this, \''.$procVar.'\', \''.$name.'\',\''.$key.'\', \''.$modId.'\', \''.htmlspecialchars($optionsJSON).'\', false);';
							break;
						default:
							$options=null;
							break;
					}
					
				}
				$optionsId++;
				
				$u.= '<tr><td><input type="'.$inputType.'" name="'.$inputName.'" value="'.$inputValue.'" onclick="'.$onclick.'"  '.$signed.' ></td><td>'.$name.$options.'</td></tr>';
			}
			$s2='</tbody></table></div>';
			echo $s1.$u.$s2;
		} else {
			echo $AppUI->_ ( 'No results for search criteria' );
		}
break;
}
?>				

