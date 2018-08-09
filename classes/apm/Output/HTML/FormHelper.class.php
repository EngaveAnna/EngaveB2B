<?php
/**
 * Class apm_Output_HTML_FormHelper
 *
 * @package     apmProject\output\html
 */
class apm_Output_HTML_FormHelper extends apm_Output_HTML_Base {
	public function addField($fieldName, $fieldValue, $options = array(), $values = array()) {
		$pieces = explode ( '_', $fieldName );
		$suffix = end ( $pieces );
		
		$params = '';
		$options['class']="form-control";
		foreach ( $options as $key => $value ) {
			$params .= $key . '="' . $value . '" ';
		}
		
		switch ($suffix) {
			case 'company' :
				$class = 'C' . ucfirst ( $suffix );
				
				$obj = new $class ();
				$obj->load ( $fieldValue );
				$link = '?m=' . apm_pluralize ( $suffix ) . '&a=view&' . $suffix . '_id=' . $fieldValue;
				$output = '<a href="' . $link . '">' . $obj->{"$suffix" . '_name'} . '</a>';
				break;
			case 'desc' : // @todo This is a special case because department->dept_notes should be renamed department->dept_description
			case 'note' : // @todo This is a special case because resource->resource_note should be renamed resource->resource_description
			case 'notes' : // @todo This is a special case because contact->contact_notes should be renamed contact->contact_description
			case 'signature' : // @todo This is a special case because user->user_signature should be renamed to something else..?
			case 'source' :
			case 'description' :
				$output = '<textarea name="'.$fieldName.'" class="form-control">'.apmformSafe( $fieldValue ).'</textarea>';
				break;
			case 'birthday' : // @todo This is a special case because contact->contact_birthday should be renamed contact->contact_birth_date
				$myDate = intval ( $fieldValue ) ? new apm_Utilities_Date ( $fieldValue ) : null;
				$date = $myDate ? $myDate->format ( '%Y-%m-%d' ) : '-';
				$output = '<input type="text" class="form-control" ';
				$output .= 'name="' . $fieldName . '" value="' . apmformSafe ( $date ) . '" ' . $params . ' />';
				break;
			case 'date' :
				$date = ($fieldValue) ? new apm_Utilities_Date ( $fieldValue ) : null;
				unset ( $pieces [0] );
				$datename = implode ( '_', $pieces );
				$output = '';
				$output .= '<input type="text" name="'.$datename.'" id="'.$datename.'" onchange="setDate_new(\'editFrm\', \'' . $datename . '\');" value="' . ($date ? $date->format ( $this->df ) : '') . '" class="form-control" />';
				$output .= '<span class="input-group-addon"><a href="javascript: void(0);" onclick="return showCalendar(\'' . $datename . '\', \'' . $this->df . '\', \'editFrm\', null, true, true)">';
				$output .= '<i class="glyphicon glyphicon-calendar btn-default"></i>';
				$output .= '</a><input type="hidden" name="' . $fieldName . '" id="' . $fieldName . '" value="' . ($date ? $date->format ( FMT_TIMESTAMP_DATE ) : '') . '" /></span>';
				break;
			case 'private' :
			case 'updateask' : // @todo This is unique to the contacts module
				$output = '<input type="checkbox" value="1" class="form-control" ';
				$output .= 'name="' . $fieldName . '" ' . $params . ' />';
				break;
			case 'parent' : // @note This drops through on purpose
				$suffix = 'department';
			case 'allocation' :
			case 'category' :
			case 'country' :
			case 'owner' :
			case 'client' :				
			case 'priority' :
			case 'project' :
			case 'status' :
			case 'type' :
				$output = arraySelect ( $values, $fieldName, 'size="1"', $fieldValue );
			break;
			case 'url' :
				$output = '<input type="text" class="form-control" ';
				$output .= 'name="' . $fieldName . '" value="' . apmformSafe ( $fieldValue ) . '" ' . $params . ' />';
			break;
			case 'ajaxList':
				$output=null;
				if(is_array($fieldValue))
				{
					foreach ($fieldValue as $field)
					{
						switch ($options[0])
						{
							case 'id':
								$output_add[0]='<label class="label label-default label-xs label-micro">'.$options[1]['prefix'].$field[$options[1]['field']].'</label>';
							break;
						}
						$output .='<p id="'.$field['modId'].'_'.$field['elemId'].'"><button class="btn btn-default" type="button" onclick="removeAjaxVal(\''.$field['procVar'].'\', \'\', \''.$field['elemId'].'\', \''.$field['modId'].'\');"><span class="glyphicon glyphicon-remove"></span></button>&nbsp;'.$field['elemName'].$output_add[0].'</p>';
					}
				}		
			break;
			/**
			 * This handles the default input text input box.
			 * It currently covers these fields:
			 * all names, email, phone1, phone2, url, address1, address2, city, state, zip, fax, title, job
			 */
			default :
				$output = '<input type="text" ';
				$output .= 'name="' . $fieldName . '" value="'.apmformSafe ( $fieldValue ) . '" ' . $params . ' />';
		}
		
		return $output;
	}
	public function showField($fieldName, $fieldValue, $options = array(), $values = array()) {
		echo $this->addField ( $fieldName, $fieldValue, $options, $values );
	}
	public function addCancelButton() {
		$output = '<input type="button" value="' . $this->AppUI->_ ( 'back' ) . '" class="btn btn-default" onclick="javascript:history.back(-1);" />';
		return $output;
	}
	public function showCancelButton() {
		echo $this->addCancelButton ();
	}
	public function addSaveButton() {
		$output = '<input type="button" value="' . $this->AppUI->_ ( 'save' ) . '" class="btn btn-info" onclick="submitIt()" />';
		
		return $output;
	}
	public function showSaveButton() {
		echo $this->addSaveButton ();
	}
	public function addNonce() {
		$nonce = md5 ( time () . implode ( $this->AppUI->user_prefs ) );
		$output = '<input type="hidden" name="__nonce" value="' . $nonce . '" />';
		$this->AppUI->__nonce = $nonce;
		
		return $output;
	}
	/**
	 * The drawing function
	 */
	public function addAjaxModal($modId, $modTitle) {
		global $a;
		$m = $this->module;
		$s = '';

		$s .= '<div class="modal fade" id="'.$modId.'" tabindex="-1" role="dialog" aria-labelledby="tabToolsModalLabel" aria-hidden="true">
		<div class="modal-dialog"><div class="modal-content"><div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="'. $this->AppUI->_( "Close" ) . '"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">'.$modTitle.'</h4>
		</div><div class="modal-body" id="'.$modId.'_body'.'">';
		
		$s .= '</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->AppUI->_( "Close" ) . '</button>
		</div>
		</div><!-- modal-content -->
		</div><!-- modal-dialog -->
		</div><!-- modal -->';
		
		echo $s;
	}
}