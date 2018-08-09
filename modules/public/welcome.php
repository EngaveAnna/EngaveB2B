<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$titleBlock = new apm_Theme_TitleBlock ( '', '', $m );
$titleBlock->show ();

include $AppUI->getTheme ()->resolveTemplate ( 'public/welcome' );