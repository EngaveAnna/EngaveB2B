<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "msresource2003.class.php";
class MSResource2007 extends MSResource2003 {
	public function dump() {
		return $this->array_insert ( parent::dump (), array (
				
				// Whether the resource is a cost resource.
				'IsCostResource' => "",
				
				// The name of the assignment owner.
				'AssnOwner' => "",
				
				// The GUID of the assignment owner.
				'AssnOwnerGuid' => "",
				
				// Whether the resource is a budget resource.
				'IsBudget' => "" 
		)
		, 'ExtendedAttributes' );
	}
	protected function dumpObjectExtendedAttributeValues($object_id) {
		$extendedattributevalues = array ();
		$values = $this->getObjectExtendedAttributeValues ( 'contacts', $object_id );
		foreach ( $values as $value ) {
			if ($value ['value_charvalue']) {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'FieldID' => $value ['value_field_id'],
								'Value' => $value ['value_charvalue'] 
						) 
				);
			} elseif ($value ['value_intvalue'] > 0 && $value ['field_htmltype'] == 'select') {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'FieldID' => $value ['value_field_id'],
								'ValueID' => $value ['value_intvalue'] 
						) 
				);
			} else {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'FieldID' => $value ['value_field_id'],
								'Value' => $value ['value_intvalue'] 
						) 
				);
			}
		}
		return $extendedattributevalues;
	}
}
