<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$contact_id = ( int ) apmgetParam ( $_GET, 'contact_id', 0 );

$tab = $AppUI->processIntState ( 'ContactVwTab', $_GET, 'tab', 0 );

$contact = new CContact ();

if (! $contact->load ( $contact_id )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$canEdit = $contact->canEdit ();
$canDelete = $contact->canDelete ();

$is_user = $contact->isUser ( $contact_id );

// Get the contact details for company and department
$company_detail = $contact->getCompanyDetails ();
$dept_detail = $contact->getDepartmentDetails ();

// Get the Contact info (phone, emails, etc) for the contact
$methods = $contact->getContactMethods ();
$methodLabels = apmgetSysVal ( 'ContactMethods' );

// setup the title block
$ttl = 'View Contact';
$titleBlock = new apm_Theme_TitleBlock ( $ttl, 'icon.png', $m );
$titleBlock->addCrumb ( '?m=contacts', 'contacts list' );
if ($canEdit) {
	$titleBlock->addCrumb ( '?m=contacts&a=addedit&contact_id=' . $contact_id, 'edit this contact' );
}
if ($contact->user_id) {
	$titleBlock->addCrumb ( '?m=users&a=view&user_id=' . $contact->user_id, 'view this user' );
}
if ($canDelete) {
	$titleBlock->addCrumbDelete ( 'delete contact', $canDelete, $msg );
}
$titleBlock->show ();

$view = new apm_Controllers_View ( $AppUI, $contact, 'Contact' );
echo $view->renderDelete ();

include $AppUI->getTheme ()->resolveTemplate ( 'contacts/view' );