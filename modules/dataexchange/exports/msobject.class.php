<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
abstract class MSObject {
	protected $exporter = null;
	public function __construct($exporter) {
		$this->exporter = $exporter;
	}
	protected function factory($object, &$values) {
		$version = $this->exporter->version;
		require_once "ms" . strtolower ( $object ) . $version . ".class.php";
		$class = "MS" . $object . $version;
		return new $class ( $this->exporter, $values );
	}
	protected function dumpExtendedAttributes() {
		$extendedattributes = array ();
		$q = new apm_Database_Query ();
		$q->addTable ( 'custom_fields_struct' );
		$q->addQuery ( '*' );
		$q->addWhere ( "field_published=1" );
		$attributes = $q->loadList ();
		if (count ( $attributes )) {
			foreach ( $attributes as $attribute ) {
				$extendedattributevalues = $this->dumpExtendedAttributeValues ( $attribute ['field_id'], html_entity_decode ( $attribute ['field_description'], ENT_COMPAT, "UTF-8" ) );
				if (empty ( $extendedattributevalues )) {
					$extendedattributes [] = array (
							'ExtendedAttribute' => array (
									'FieldID' => $attribute ['field_id'],
									'FieldName' => html_entity_decode ( $attribute ['field_name'], ENT_COMPAT, "UTF-8" ) 
							) 
					);
				} else {
					$extendedattributes [] = array (
							'ExtendedAttribute' => array (
									'FieldID' => $attribute ['field_id'],
									'FieldName' => html_entity_decode ( $attribute ['field_name'], ENT_COMPAT, "UTF-8" ),
									'ValueList' => $extendedattributevalues 
							) 
					);
				}
			}
		}
		return $extendedattributes;
	}
	protected function dumpExtendedAttributeValues($field_id = -1, $description) {
		$extendedattributevalues = array ();
		$q = new apm_Database_Query ();
		$q->addQuery ( '*' );
		$q->addTable ( 'custom_fields_lists' );
		$q->addWhere ( "field_id=$field_id" );
		$values = $q->loadList ();
		foreach ( $values as $value ) {
			$extendedattributevalues [] = array (
					'Value' => array (
							'ID' => $value ['list_option_id'],
							'Value' => html_entity_decode ( $value ['list_value'], ENT_COMPAT, "UTF-8" ),
							'Description' => $description 
					) 
			);
		}
		return $extendedattributevalues;
	}
	protected function getObjectExtendedAttributeValues($object, $object_id) {
		$extendedattributevalues = array ();
		$q = new apm_Database_Query ();
		$q->addQuery ( "fv.*, fs.field_htmltype" );
		$q->addTable ( "custom_fields_values", "fv" );
		$q->innerJoin ( "custom_fields_struct", "fs", "fs.field_id = fv.value_field_id and fs.field_module='$object'" );
		$q->addWhere ( "fv.value_object_id=$object_id" );
		$values = $q->loadList ();
		return $values;
	}
	protected function dumpDateTime($dates, $tz = true) {
		global $AppUI;
		if (is_array ( $dates )) {
			foreach ( $dates as $date ) {
				if (! is_null ( $date ) && strlen ( $date ) > 0 && $date != "0000-00-00 00:00:00") {
					return $tz ? $AppUI->formatTZAwareTime ( $date, '%Y-%m-%dT%T' ) : str_replace ( "T", " ", $date );
				}
			}
		} else {
			if (! is_null ( $dates ) && strlen ( $dates ) > 0 && $dates != "0000-00-00 00:00:00") {
				return $tz ? $AppUI->formatTZAwareTime ( $dates, '%Y-%m-%dT%T' ) : str_replace ( "T", " ", $dates );
			}
		}
		return "";
	}
	protected function logToHours($value) {
		$value = $value * 60;
		$hours = floor ( $value / 60 ) . "";
		$minutes = round ( $value % 60 ) . "";
		return $hours . 'H' . $minutes . 'M0S';
	}
	public function dump() {
		trigger_error ( "dump is not implemented.", E_USER_NOTICE );
	}
	protected function array_insert($array, $insert, $after, $before = array()) {
		reset ( $array );
		$offset = 0;
		while ( list ( $key, $val ) = each ( $array ) ) {
			$offset ++;
			if ($key == $after) {
				break;
			} elseif (in_array ( $key, $before )) {
				$offset --;
				break;
			}
		}
		$first_array = array_splice ( $array, 0, $offset );
		return array_merge ( $first_array, $insert, $array );
	}
}
