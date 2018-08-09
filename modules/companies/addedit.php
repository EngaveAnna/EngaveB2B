<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$object_id = ( int ) apmgetParam ( $_GET, 'company_id', 0 );
$object = new CCompany ();
$object->setId ( $object_id );

$canAddEdit = $object->canAddEdit ();
$canAuthor = $object->canCreate ();
$canEdit = $object->canEdit ();
if (! $canAddEdit) {
	$AppUI->redirect ( ACCESS_DENIED );
}

// load the record data
$obj = $AppUI->restoreObject ();
if ($obj) {
	$object = $obj;
	$object_id = $object->getId ();
} else {
	$object->load ( $object_id );
}
if (! $object && $object_id > 0) {
	$AppUI->setMsg ( 'Company' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

// setup the title block
$ttl = $object_id > 0 ? 'Edit Company' : 'Add Company';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );

$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );
$titleBlock->addViewLink ( 'company', $object_id );
$titleBlock->show ();

// load the company types
$types = apmgetSysVal ( 'CompanyType' );
$countries = array (
		'' => $AppUI->_ ( '(Select a Country)' ) 
) + apmgetSysVal ( 'GlobalCountriesPreferred' ) + array (
		'-' => '----' 
) + apmgetSysVal ( 'GlobalCountries' );

?>
<script language="javascript" type="text/javascript">
    function submitIt() {
        var form = document.editFrm;
        if (form.company_name.value.length < 3) {
            alert( "<?php echo $AppUI->_('companyValidName', UI_OUTPUT_JS); ?>" );
            form.company_name.focus();
        } else {
            form.submit();
        }
    }
</script>
<?php

include $AppUI->getTheme ()->resolveTemplate ( 'companies/addedit' );