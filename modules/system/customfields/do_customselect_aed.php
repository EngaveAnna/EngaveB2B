<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

if (! canEdit ( 'system' )) {
	return;
}

$field_id = ( int ) apmgetParam ( $_POST, 'field_id', 0 );
$list_value = strip_tags ( apmgetParam ( $_POST, 'field_value', '' ) );
$list_option_id = ( int ) apmgetParam ( $_POST, 'list_option_id', 0 );
$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );

$option_list = new apm_Core_CustomOptionList ( $field_id );
$option_list->options = array (
		$list_value 
);
$option_list->list_option_id = $list_option_id;

// TODO: we should do soething with this result
$result = ($delete) ? $option_list->delete () : $option_list->store ();

die ();