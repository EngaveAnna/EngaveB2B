<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$canRead = canView ( $m );
$canEdit = canEdit ( $m );

if (! $canRead) {
	$AppUI->redirect ( "m=public&a=access_denied" );
}
$separator = ',';
$project_id = ( int ) apmgetParam ( $_POST, 'project_id', '0' );
$msproject = "MsProject" . apmgetParam ( $_POST, 'msproject', '2003' );
$file = apmgetParam ( $_POST, 'sql_file', $msproject . "-" . $project_id );
if (! $file) {
	$file = $msproject . "-" . $project_id;
}
$file .= '.xml';
$zipped = apmgetParam ( $_POST, 'zipped', false );

$clazz = $msproject . "Exporter";
require_once ("exports/" . strtolower ( $msproject ) . "exporter.class.php");
$exporter = new $clazz ( $file, $_POST );
$output = $exporter->export ( $project_id );
$mime_type = 'application/vnd.ms-project';
if ($zipped) {
	include ('lib/zip.lib.php');
	$zip = new zipfile ();
	$zip->addFile ( $output, $file );
	$output = $zip->file ();
	$file .= '.zip';
	$mime_type = 'application/x-zip';
}
$testing = false;
if (! $testing) {
	header ( 'Content-Disposition: inline; filename="' . $file . '"' );
	header ( 'Content-Type: ' . $mime_type );
} else {
	echo '<code>';
	print_r ( $_POST );
	$output .= '</code>';
}
echo $output;
