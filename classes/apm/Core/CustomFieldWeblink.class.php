<?php

/**
 * Produces an INPUT Element of the TEXT type in edit mode and a <a href> </a>
 * weblink in display mode
 *
 * @package     apmProject\core
 */
class apm_Core_CustomFieldWeblink extends apm_Core_CustomField {
	public $field_htmltype = 'href';
	public function getHTML($mode) {
		$html = '<label>' . $this->field_description . ':</label>';
		switch ($mode) {
			case 'edit' :
				$html .= '<input type="text" class="form-control" name="' . $this->fieldName () . '" value="' . $this->charValue () . '" ' . $this->fieldExtraTags () . ' />';
				break;
			case 'view' :
				$html .= '<a href="' . $this->charValue () . '">' . $this->charValue () . '</a>';
				break;
		}
		return $html;
	}
}