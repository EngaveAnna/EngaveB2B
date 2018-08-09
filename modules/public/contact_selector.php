<?php
// if (!defined('apm_BASE_DIR')){
// die('You should not access this file directly.');
// }
// @todo remove database query
$show_all = apmgetParam ( $_REQUEST, 'show_all', 0 );
$company_id = apmgetParam ( $_REQUEST, 'company_id', 0 );
$contact_id = apmgetParam ( $_POST, 'contact_id', 0 );
$call_back = apmgetParam ( $_GET, 'call_back', null );
$contacts_submited = apmgetParam ( $_POST, 'contacts_submited', 0 );
$selected_contacts_id = apmgetParam ( $_GET, 'selected_contacts_id', '' );
if (apmgetParam ( $_POST, 'selected_contacts_id' )) {
	$selected_contacts_id = apmgetParam ( $_POST, 'selected_contacts_id' );
}
?>

<script language="javascript" type="text/javascript">
// ECMA Script section Carsten Menke <menke@smp-synergie.de>
function setContactIDs(method, querystring) {
	var URL = 'index.php?m=public&a=contact_selector';
	var field = document.getElementsByName('contact_id[]');
	var selected_contacts_id = document.frmContactSelect.selected_contacts_id;
	var tmp = new Array();

	if (method == 'GET' && querystring){
		URL += '&' + querystring;
	}

	var count = 0;
	for (i = 0, i_cmp = field.length; i < i_cmp; i++) {
		if (field[i].checked) {
			tmp[count++] = field[i].value;
		}
	}
	selected_contacts_id.value = tmp.join(',');

	if (method == 'GET') {
		URL +=  '&selected_contacts_id=' + selected_contacts_id.value;
		return URL;
	} else {
		return selected_contacts_id;
	}
}
</script>
<?php

if ($contacts_submited == 1) {
	$call_back_string = ! is_null ( $call_back ) ? "window.opener.$call_back('$selected_contacts_id');" : '';
	?>
<script language="javascript" type="text/javascript">
	<?php echo $call_back_string; ?>
	self.close();
</script>
<?php
}

$company_name = '';

// Remove any empty elements
$contacts_id = array_filter ( explode ( ',', $selected_contacts_id ) );
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
?>

<form action="index.php?m=public&a=contact_selector&dialog=1&<?php
	
if (! is_null ( $call_back ))
		echo 'call_back=' . $call_back . '&';
	?>company_id=<?php echo $company_id ?>"
	method="post" name="frmContactSelect" accept-charset="utf-8">

	

		
			
			
	<?php
	$actual_department = '';
	$actual_company = '';
	$companies_names = array (
			0 => $AppUI->_ ( 'Select a company' ) 
	) + $aCpies;
	echo arraySelect ( $companies_names, 'company_id', 'onchange="document.frmContactSelect.contacts_submited.value=0; ' . 'setContactIDs(); document.frmContactSelect.submit();"', $company_id );
	?>

			
			

<div style="padding:10px 0;">
	
					<a class="btn btn-default" href="javascript: void(0);"
						onclick="window.location.href=setContactIDs('GET','dialog=1&<?php
						
if (! is_null ( $call_back ))
							echo 'call_back=' . $call_back . '&';
						?>show_all=1');"><?php echo $AppUI->_('View all allowed contacts'); ?></a>
</div>						
					<?php	
					$s1 ='<div class="table-responsive">
						<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
						<tbody>';

					foreach ( $contacts as $contact_id => $contact_data ) {

	

						$contact_department = $contact_data ['dept_name'] ? $contact_data ['dept_name'] : $contact_data ['contact_department'];
						if ($contact_department && $contact_department != $actual_department) {
							$s .= $s1.$contact_department.$s2.$u;
						
							$actual_department = $contact_department;
						}
						
						
						$checked = in_array ( $contact_id, $contacts_id ) ? 'checked="checked"' : '';
						$u .= '<tr><td class="apm-label"><input type="checkbox" class="form-control" name="contact_id[]" id="contact_' . $contact_id . '" value="' . $contact_id . '" ' . $checked . ' /></td>';
						$u .= '<td>'.$contact_data ['contact_first_name'] . ' ' . $contact_data ['contact_last_name'] . '</td>';
						$u .= '</tr>';
					}
					
					echo $s1.$u.$s2;
					?>
					
						</tbody>
	</table>
	</div>
	
<div>
<input name="contacts_submited" type="hidden" value="1" /> <input name="selected_contacts_id" type="hidden" value="<?php echo $selected_contacts_id; ?>" />
<input type="submit" value="<?php echo $AppUI->_('Continue'); ?>" onclick="setContactIDs();" class="btn btn-info" />
</div>
				
				
				

</form>