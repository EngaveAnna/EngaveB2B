<?php

/**
 * Produces a TEXTAREA Element in edit mode
 *
 * @package     apmProject\core
 */
class apm_Core_CustomFieldTextArea extends apm_Core_CustomField {
	public $field_htmltype = 'textarea';
	public function getHTML($mode) {
		$html = '<label>' . $this->field_description . ':</label>';
		switch ($mode) {
			case 'edit' :
				$html .= '<textarea name="' . $this->fieldName () . '" ' . $this->fieldExtraTags () . ' class="customfield">' . $this->charValue () . '</textarea>';
				break;
			case 'view' :
				$html .= nl2br ( $this->charValue () );
				break;
		}
		return $html;
	}
}