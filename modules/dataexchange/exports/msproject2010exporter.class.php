<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "exporter.class.php";
class MsProject2010Exporter extends Exporter {
	public function __construct($file, $options = array()) {
		parent::__construct ( '2010', $file, $options );
	}
}
