<?php
/**
 * Class apm_Output_HTML_FormHelper
 *
 * @package     apmProject\output\html
 */
class apm_Output_HTML_ViewHelper extends apm_Output_HTML_Base {
	public function addField($fieldName, $fieldValue) {
		if ('' == $fieldValue) {
			return '-';
		}
		
		$pieces = explode ( '_', $fieldName );
		$suffix = end ( $pieces );
		
		switch ($suffix) {
			case 'datetime' :
			case 'date' :				
				$myDate = intval ( $fieldValue ) ? new apm_Utilities_Date ( $this->AppUI->formatTZAwareTime ( $fieldValue, '%Y-%m-%d %T' ) ) : null;
				$output = $myDate ? $myDate->format ( $this->dtf ) : '-';
				break;
			case 'email' :
				$output = apm_email ( $fieldValue );
				break;
			case 'url' :
				$value = str_replace ( array (
						'"',
						'"',
						'<',
						'>' 
				), '', $fieldValue );
				$output = apm_url ( $value );
				break;
			case 'owner' :
				if (! $fieldValue) {
					return '-';
				}
				$obj = new CContact ();
				$obj->findContactByUserid ( $fieldValue );
				$link = '?m=users&a=view&user_id=' . $fieldValue;
				$output = '<a href="' . $link . '">' . $obj->contact_display_name . '</a>';
				break;
			case 'percent' :
				$output = round ( $fieldValue ) . '%';
				break;
			case 'description' :
				$output = apm_textarea ( $fieldValue );
				break;
			case 'company' :
			case 'department' :
			case 'project' :
				$class = 'C' . ucfirst ( $suffix );
				$obj = new $class ();
				$obj->load ( $fieldValue );
				$link = '?m=' . apm_pluralize ( $suffix ) . '&a=view&' . $suffix . '_id=' . $fieldValue;
				$output = '<a href="' . $link . '">' . $obj->{"$suffix" . '_name'} . '</a>';
				break;
			default :
				$output = htmlspecialchars ( $fieldValue, ENT_QUOTES );
		}
		return $output;
	}
	public function showField($fieldName, $fieldValue) {
		echo $this->addField ( $fieldName, $fieldValue );
	}
	public function showAddress($name, $object) {
		$countries = apmgetSysVal ( 'GlobalCountries' );
		$output .= $object->{$name . '_address1'} . (($object->{$name . '_address2'}) ? ', ' . $object->{$name . '_address2'} : '') . (($object->{$name . '_city'}) ? '' . $object->{$name . '_city'} : '') . (($object->{$name . '_state'}) ? ' ' . $object->{$name . '_state'} : '') . (($object->{$name . '_zip'}) ? ', ' . $object->{$name . '_zip'} : '') . (($object->{$name . '_country'}) ? ', ' . $countries [$object->{$name . '_country'}] : '');
		echo $output;
	}
}