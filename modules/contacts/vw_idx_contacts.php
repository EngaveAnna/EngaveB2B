<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

global $AppUI, $tab, $m;

$tab ++;
$days = ($tab == 0) ? 30 : 0;
$searchString = apmgetParam ( $_POST, 'search_string', '' );
if ('' == $searchString) {
	$searchString = ((0 < $tab) && ($tab < 26)) ? chr ( 64 + $tab ) : '';
	$searchString = (0 == $tab) ? '' : $searchString;
} else {
	$AppUI->setState ( 'ContactsIdxTab', 26 );
}

$AppUI->setState ( 'ContIdxWhere', $searchString );

$where = $AppUI->getState ( 'ContIdxWhere' ) ? $AppUI->getState ( 'ContIdxWhere' ) : '%';

$contact = new CContact ();
$rows = $contact->search ( $where, $days );

$countries = apmgetSysVal ( 'GlobalCountries' );

unset ( $carr );
$carrWidth = 4;
$carrHeight = 4;

$rn = count ( $rows );
$t = ceil ( $rn / $carrWidth );

if ($rn < ($carrWidth * $carrHeight)) {
	$i = 0;
	for($y = 0; $y < $carrWidth; $y ++) {
		$x = 0;
		while ( ($x < $carrHeight) && isset ( $rows [$i] ) && ($row = $rows [$i]) ) {
			$carr [$y] [] = $row;
			$x ++;
			$i ++;
		}
	}
} else {
	$i = 0;
	for($y = 0; $y <= $carrWidth; $y ++) {
		$x = 0;
		while ( ($x < $t) && isset ( $rows [$i] ) && ($row = $rows [$i]) ) {
			$carr [$y] [] = $row;
			$x ++;
			$i ++;
		}
	}
}

$tdw = floor ( 100 / $carrWidth );

$df = $AppUI->getPref ( 'SHDATEFORMAT' );
$df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

// optional fields shown in the list (could be modified to allow brief and verbose, etc)
$showfields = array (
		'contact_address1' => 'contact_address1',
		'contact_address2' => 'contact_address2',
		'contact_city' => 'contact_city',
		'contact_state' => 'contact_state',
		'contact_zip' => 'contact_zip',
		'contact_country' => 'contact_country',
		'contact_company' => 'contact_company',
		'company_name' => 'company_name',
		'dept_name' => 'dept_name',
		'contact_phone' => 'contact_phone',
		'contact_email' => 'contact_email',
		'contact_job' => 'contact_job' 
);
$contactMethods = array (
		'phone_alt',
		'phone_mobile',
		'phone_fax' 
);
$methodLabels = apmgetSysVal ( 'ContactMethods' );

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'contacts', 'index_list' );

for($z = 0; $z < $carrWidth; $z ++) {
	if (! isset ( $carr [$z] )) {
		continue;
	}
	
	for($x = 0, $x_cmp = count ( $carr [$z] ); $x < $x_cmp; $x ++) {
		$items [$x] ['contact_options'] = '';
		$contactid = $carr [$z] [$x] ['contact_id'];
		$items [$x] ['contact_name'] = '<a href="./index.php?m=contacts&a=view&contact_id=' . $contactid . '"><strong>' . ($carr [$z] [$x] ['contact_title'] ? $carr [$z] [$x] ['contact_title'] . ' ' : '') . $carr [$z] [$x] ['contact_first_name'] . ' ' . $carr [$z] [$x] ['contact_last_name'] . '</strong></a>';
		if ($carr [$z] [$x] ['user_id']) {
		$items [$x] ['contact_options'] = '<a class="btn btn-xs btn-default" href="./index.php?m=users&a=view&user_id=' . $carr [$z] [$x] ['user_id'] . '" role="button">
		<span class="glyphicon glyphicon-user" data-placement="right" data-toggle="tooltip" data-container="body" data-original-title="' . $AppUI->_ ( 'This contact is also a user, click to view its details' ) . '"></span></a>';
		}
		
		// $items[$x]['contact_options'].='<a class="btn btn-xs btn-info" href="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id='.$contactid.'" role="button"><span class="glyphicon glyphicon-user" data-placement="right" data-toggle="tooltip" data-container="body" data-original-title="'.$AppUI->_('export vCard of this contact').'"></span></a>';
		$items [$x] ['contact_options'] .= '<a class="btn btn-xs btn-info" href="?m=contacts&a=addedit&contact_id=' . $contactid . '" role="button">
		<span class="glyphicon glyphicon-edit" data-placement="right" data-toggle="tooltip" data-container="body" data-original-title="' . $AppUI->_ ( 'edit this contact' ) . '"></span></a>';
		
		$projectList = CContact::getProjects ( $contactid );
		
		$contact_updatekey = $carr [$z] [$x] ['contact_updatekey'];
		$contact_lastupdate = $carr [$z] [$x] ['contact_lastupdate'];
		$contact_updateasked = $carr [$z] [$x] ['contact_updateasked'];
		$last_ask = new apm_Utilities_Date ( $contact_updateasked );
		$lastAskFormatted = $last_ask->format ( $df );
		if (count ( $projectList ) > 0) {
		$items [$x] ['contact_options'] .= '<a class="btn btn-xs btn-default" onclick="	window.open(\'./index.php?m=public&a=selector&dialog=1&callback=goProject&table=projects&user_id=' . $carr [$z] [$x] ['contact_id'] . '\', \'selector\', \'left=50,top=50,height=250,width=400,resizable\');return false;" role="button">
		<span class="glyphicon glyphicon-open" data-placement="right" data-toggle="tooltip" data-container="body" data-original-title="' . $AppUI->_ ( 'Click to view projects associated with this contact' ) . '"></span></a>';
		}
		
		reset ( $showfields );
		$s = '';
		foreach ( $showfields as $key => $val ) {
			if (isset ( $carr [$z] [$x] [$key] ) && mb_strlen ( $carr [$z] [$x] [$key] ) > 0) {
				
				if ($val == 'contact_email') {
					$items [$x] ['contact_email'] = $carr [$z] [$x] [$key];
				} elseif ($val == 'contact_company' && is_numeric ( $carr [$z] [$x] [$key] )) {
					// Don't do a thing
				} elseif ($val == 'company_name') {
					$items [$x] ['company_name'] = $carr [$z] [$x] [$key];
				} elseif ($val == 'contact_job') {
					$items [$x] ['contact_job'] = $carr [$z] [$x] [$key];
				} elseif ($val == 'dept_name') {
					$items [$x] ['dept_name'] = $carr [$z] [$x] [$key];
				} elseif ($val == 'contact_country' && $carr [$z] [$x] [$key]) {
					$items [$x] ['contact_country'] = ($countries [$carr [$z] [$x] [$key]] ? $countries [$carr [$z] [$x] [$key]] : $carr [$z] [$x] [$key]);
				} elseif ($val == 'contact_phone') {
					$items [$x] ['contact_phone'] = $carr [$z] [$x] [$key];
				}
			}
		}
		echo $s;
	}
}

$listTable = new apm_Output_ListTable ( $AppUI );
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields );
echo $listTable->buildRows ( $items, $customLookups );
echo $listTable->endTable ();

?>