<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not call this file directly.' );
}

$sort = apmgetParam ( $_REQUEST, 'sort', 'asc' );
$forum_id = apmgetParam ( $_REQUEST, 'forum_id', 0 );
$message_id = apmgetParam ( $_REQUEST, 'message_id', 0 );

$perms = &$AppUI->acl ();
if (! $perms->checkModuleItem ( 'forums', 'view', $forum_id )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$forum = new CForum ();
$forum->load ( $forum_id );

$project = new CProject ();
$project->load ( $forum->forum_project );

$messages = $forum->getMessages ( null, $forum_id, $message_id, $sort );

// get the prefered date format
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
$df .= ' ' . $AppUI->getPref ( 'TIMEFORMAT' );

$pdfdata = array ();
$pdfhead = array (
		'Date',
		'User',
		'Message' 
);

foreach ( $messages as $row ) {
	// Find the parent message - the topic.
	if ($row ['message_id'] == $message_id) {
		$topic = $row ['message_title'];
	}
	
	$date = new apm_Utilities_Date ( $AppUI->formatTZAwareTime ( $row ['message_date'], '%Y-%m-%d %T' ) );
	$pdfdata [] = array (
			$date->format ( $df ),
			$row ['contact_display_name'],
			'<b>' . $row ['message_title'] . '</b>' . "\n" . $row ['message_body'] 
	);
}

$font_dir = apm_BASE_DIR . '/lib/ezpdf/fonts';
$temp_dir = apm_BASE_DIR . '/files/temp';

$output = new apm_Output_PDFRenderer ();
$pdf = $output->getPDF ();

$pdf->selectFont ( $font_dir . '/Helvetica.afm' );
$pdf->ezText ( 'Project: ' . $project->project_name );
$pdf->ezText ( 'Forum: ' . $forum->forum_name );
$pdf->ezText ( 'Topic: ' . $topic );
$pdf->ezText ( '' );
$options = array (
		'showLines' => 1,
		'showHeadings' => 1,
		'fontSize' => 8,
		'rowGap' => 2,
		'colGap' => 5,
		'xPos' => 35,
		'xOrientation' => 'right',
		'width' => '400',
		'cols' => array (
				0 => array (
						'justification' => 'left',
						'width' => 75 
				),
				1 => array (
						'justification' => 'left',
						'width' => 100 
				),
				2 => array (
						'justification' => 'left',
						'width' => 350 
				) 
		) 
);

$pdf->ezTable ( $pdfdata, $pdfhead, null, $options );
$pdf->ezStream ( array (
		'Content-Disposition' => 'forum-thread-' . $forum_id . '.pdf' 
) );
