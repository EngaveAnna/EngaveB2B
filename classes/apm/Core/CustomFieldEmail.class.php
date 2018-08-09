<?php

/**
 * Produces an INPUT Element of the TEXT type in edit mode. In view mode, it becomes a clickable email address.
 *
 * @package apmProject\core
 */
class apm_Core_CustomFieldEmail extends apm_Core_CustomFieldText {
	public $field_htmltype = 'email';
	public function getHTML($mode) {
		$html = '<label>' . $this->field_description . ':</label>';
		switch ($mode) {
			case 'edit' :
				$html .= '<input type="text" class="form-control" name="' . $this->fieldName () . '" value="' . $this->charValue () . '" ' . $this->fieldExtraTags () . ' />';
				break;
			case 'view' :
				$html .= apm_email ( $this->charValue () );
				break;
		}
		return $html;
	}
}