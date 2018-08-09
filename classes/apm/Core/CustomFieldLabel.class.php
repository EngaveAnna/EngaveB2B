<?php

/**
 * Produces just a non editable label
 *
 * @package     apmProject\core
 */
class apm_Core_CustomFieldLabel extends apm_Core_CustomField {
	public $field_htmltype = 'label';
	public function getHTML($mode) {
		// We don't really care about its mode
		return '<span ' . $this->fieldExtraTags () . '>' . $this->field_description . '</span>';
	}
}