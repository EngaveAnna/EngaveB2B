<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "msassignment2003.class.php";
class MSAssignment2007 extends MSAssignment2003 {
	public function dump() {
		return $this->array_insert ( parent::dump (), array (
				
				// Whether the task is a summary task.
				"Summary" => 0,
				
				// The earned value schedule variance, through the project status date.
				"SV" => "" 
		), 'StartVariance' );
	}
}
