<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "msassignment2007.class.php";
class MSAssignment2010 extends MSAssignment2007 {
	public function dump() {
		return $this->array_insert ( parent::dump (), array (
				
				// The time unit for the usage rate of the material resource assignment,
				// for example resource m1[5lbs/hr].
				// The time units are NONE=0, SECONDS=1, MINUTES=2, HOURS=3, DAYS=4, WEEKS=5,
				// MONTHS=6.
				"RateScale" => 0 
		)
		, 'PeakUnits' );
	}
}
