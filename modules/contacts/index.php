<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$tab = $AppUI->processIntState ( 'ContactsIdxTab', $_GET, 'tab', 0 );
$searchString = apmgetParam ( $_POST, 'search_string', '' );

$contact = new CContact ();
$canCreate = $contact->canCreate ();
$canAccess = $contact->canAccess ();

if (! $canAccess) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$titleBlock = new apm_Theme_TitleBlock ( 'Contacts', 'icon.png', $m );
// $titleBlock->addCell('<a href="./index.php?m=contacts&amp;tab=0">' . $AppUI->_('Reset search') . '</a>');
$titleBlock->addCell ( '<label>' . $AppUI->_ ( 'Search' ) . '</label>' );
$titleBlock->addCell ( '<form action="index.php?m=contacts&tab=26" method="post" accept-charset="utf-8" name="searchform">' . '<input type="text" class="form-control" name="search_string" value="' . $searchString . '" /></form>' );

if ($canCreate) {
	$titleBlock->addCrumb ( '?m=contacts&a=addedit', 'New contact' );
	$titleBlock->addCrumb ( '?m=contacts&a=csvexport&suppressHeaders=1', 'CSV Download' );
	// $titleBlock->addCrumb('?m=contacts&a=vcardimport&dialog=0', 'Import vCard');
}
$titleBlock->show ();
$tabBox = new CTabBox ( '?m=contacts', apm_BASE_DIR . '/modules/contacts/', $tab );
for($c = 65; $c < 91; $c ++) {
	$tabBox->add ( 'vw_idx_contacts', $AppUI->_ ( chr ( $c ) ) );
}
$tabBox->add ( 'vw_idx_contacts', $AppUI->_ ( 'All Contacts' ) );
$tabBox->show ();

// TODO: Check to see that the Edit function is separated.
?>
<script language="javascript" type="text/javascript">
	// Callback function for the generic selector
	function goProject( key, val ) {
		var f = document.modProjects;
		if (val != '') {
			f.project_id.value = key;
			f.submit();
		}
	}
</script>
<form action="./index.php" method='get' name="modProjects"
	accept-charset="utf-8">
	<input type='hidden' name='m' value='projects' /> <input type='hidden'
		name='a' value='view' /> <input type='hidden' name='project_id' />
</form>