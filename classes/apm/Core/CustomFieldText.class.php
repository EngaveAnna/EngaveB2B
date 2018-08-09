<?php

/**
 * Produces an INPUT Element of the TEXT type in edit mode
 *
 * @package apmProject\core
 */
class apm_Core_CustomFieldText extends apm_Core_CustomField {
	public $field_htmltype = 'textinput';
	public function getHTML($mode) {
		$html = '<label>' . $this->field_description . ':</label>';
		switch ($mode) {
			case 'edit' :
				$html .= '<input type="text" class="form-control" name="' . $this->fieldName () . '" value="' . $this->charValue () . '" ' . $this->fieldExtraTags () . ' />';
				break;
			case 'view' :
				$html .= '&nbsp;' . $this->charValue ();
				break;
		}
		return $html;
	}
}