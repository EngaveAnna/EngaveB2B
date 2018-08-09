<?php

/**
 * Produces just an horizontal line
 *
 * @package     apmProject\core
 */
class apm_Core_CustomFieldSeparator extends apm_Core_CustomField {
	public $field_htmltype = 'separator';
	public function getHTML($mode) {
		// We don't really care about its mode
		return '<hr ' . $this->fieldExtraTags () . ' />';
	}
}