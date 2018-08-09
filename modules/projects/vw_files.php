<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $AppUI, $project_id, $deny, $canRead, $canEdit, $apmconfig;

$showProject = false;
require (apm_BASE_DIR . '/modules/files/index_table.php');