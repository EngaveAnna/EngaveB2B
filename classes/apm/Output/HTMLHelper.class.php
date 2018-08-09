<?php

/**
 * @package     apmProject\output
 */
class apm_Output_HTMLHelper extends apm_Output_HTML_Base {
	protected $tableRowData = array ();
	
	/**
	 * @deprecated
	 */
	public static function renderContactList(apm_Core_CAppUI $AppUI, array $contactList) {
		$output = '<table cellspacing="1" cellpadding="2" border="0" width="100%" class="tbl">';
		$output .= '<tr><th>' . $AppUI->_ ( 'Name' ) . '</th><th>' . $AppUI->_ ( 'Email' ) . '</th>';
		$output .= '<th>' . $AppUI->_ ( 'Phone' ) . '</th><th>' . $AppUI->_ ( 'Department' ) . '</th></tr>';
		foreach ( $contactList as $contact_id => $contact_data ) {
			$contact = new CContact ();
			$contact->contact_id = $contact_id;
			
			$output .= '<tr>';
			$output .= '<td class="hilite"><a href="index.php?m=contacts&amp;a=addedit&amp;contact_id=' . $contact_id . '">' . $contact_data ['contact_order_by'] . '</a></td>';
			$output .= '<td class="hilite">' . apm_email ( $contact_data ['contact_email'] ) . '</td>';
			$output .= '<td class="hilite">' . $contact_data ['contact_phone'] . '</td>';
			$output .= '<td class="hilite">' . $contact_data ['dept_name'] . '</td>';
			$output .= '</tr>';
		}
		$output .= '</table>';
		
		return $output;
	}
	public function renderContactTable($moduleName, array $contactList) {
		$module = new apm_System_Module ();
		$fields = $module->loadSettings ( 'contacts', $moduleName . '_view' );
		
		if (0 == count ( $fields )) {
			$fieldList = array (
					'contact_name',
					'contact_email',
					'contact_phone',
					'dept_name' 
			);
			$fieldNames = array (
					'Name',
					'Email',
					'Phone',
					'Department' 
			);
			
			$module->storeSettings ( 'contacts', $moduleName . '_view', $fieldList, $fieldNames );
			$fields = array_combine ( $fieldList, $fieldNames );
		}
		
		$listTable = new apm_Output_ListTable ( $this->AppUI );
		
		$output = $listTable->startTable ( 'bindedToolbar' );
		$output .= $listTable->buildHeader ( $fields );
		$output .= $listTable->buildRows ( $contactList );
		$output .= $listTable->endTable ();
		
		return $output;
	}
	public function stageRowData($myArray) {
		$this->tableRowData = $myArray;
	}
	
	/**
	 * createColumn is handy because it can take any input $fieldName and use
	 * its suffix to determine how the field should be displayed.
	 *
	 * This allows us to treat project_description, task_description,
	 * company_description, or even some_other_crazy_wacky_description in
	 * exactly the same way without additional lines of code or configuration.
	 * If you want to do your own, feel free... but this is probably easier.
	 *
	 * Examples: _budget, _date, _name, _owner
	 *
	 * This may not work for things like company_type or project_type which are
	 * actually just references to look up tables, ... but should work on
	 * fields like project_company, dept_company because we still have a
	 * common suffix.
	 *
	 * @note I'm kind of annoyed about the complexity and sheer number of
	 * paths of this method but overall I think it's laid out reasonably
	 * well. I think the more important part is that I've been able to
	 * encapsulate it all here instead of spreading it all over the modules
	 * and views.
	 */
	public function createCell($fieldName, $value, $custom = array()) {
		$additional = '';
		
		if ('' == $value) {
			return '<td>-</td>';
		}
		
		$pieces = explode ( '_', $fieldName );
		$prefix = $pieces [0];
		$suffix = '_' . end ( $pieces );
		
		if ($fieldName == 'project_actual_end_date') {
			$suffix = '_actual';
		}
		
		switch ($suffix) {
			// BEGIN: object-based linkings
			/*
			 * TODO: The following cases are likely to change once we have an approach to
			 * handle module-level objects and their proper mapping/linkings.
			 */
			case '_company' :
			case '_contact' :
			case '_task' :
				$module = substr ( $suffix, 1 );
				$class = 'C' . ucfirst ( $module );
				
				$obj = new $class ();
				$obj->load ( $value );
				$link = '?m=' . apm_pluralize ( $module ) . '&a=view&' . $module . '_id=' . $value;
				$cell = '<a href="' . $link . '">' . $obj->{"$module" . '_name'} . '</a>';
				$suffix .= ' _name';
				break;
			case '_event' :
				$module = substr ( $suffix, 1 );
				$class = 'C' . ucfirst ( $module );
				
				$obj = new $class ();
				$obj->load ( $value );
				$link = '?m=' . apm_pluralize ( $module ) . '&a=view&' . $module . '_id=' . $value;
				$cell = '<a href="' . $link . '">' . $obj->{"$module" . '_name'} . '</a>';
				$suffix .= ' _name';
				break;
			case '_department' :
				$module = substr ( $suffix, 1 );
				$class = 'C' . ucfirst ( $module );
				
				$obj = new $class ();
				$obj->load ( $value );
				/**
				 * This is a branch separate from _company, _contact, etc above because although the module is called
				 * departments, the fields are dept_id and dept_name.
				 * :(
				 * ~ caseydk, Dec 11 2013
				 */
				$link = '?m=' . apm_pluralize ( $module ) . '&a=view&dept_id=' . $value;
				$cell = '<a href="' . $link . '">' . $obj->dept_name . '</a>';
				$suffix .= ' _name';
				break;
			case '_folder' :
				$obj = new CFile_Folder ();
				$obj->load ( $value );
				$foldername = ($value) ? $obj->file_folder_name : 'Root';
				$image = '<span class="glyphicon glyphicon-folder-open"></span>';
				$link = '?m=files&tab=4&folder=' . ( int ) $value;
				$cell = '<a href="' . $link . '"><button class="btn btn-default btn-xs">' . $image . '</button>' . $foldername . '</a>';
				$suffix .= ' _name';
				break;
			case '_user' :
			case '_username' :
				$obj = new CContact ();
				$obj->findContactByUserid ( $this->tableRowData ['user_id'] );
				$link = '?m=users&a=view&user_id=' . $this->tableRowData ['user_id'];
				$cell = '<a href="' . $link . '">' . $obj->user_username . '</a>';
				break;
			// END: object-based linkings
			
			/*
			 * TODO: These two prefix adjustments are an ugly hack because our departments
			 * table doesn't follow the same convention as every other table we have.
			 * This needs to be fixed in v4.0 - caseydk 13 Feb 2012
			 *
			 * TODO: And unfortunately, the forums module is screwy using 'viewer' instead
			 * of our standard 'view' for the page. ~ caseydk 16 Feb 2012
			 */
			case '_name' :
				if(is_array($value))
				{
					$cell = '<a href="' . $value[0] . '">' . $value[1] . '</a>';
				}
				else
				{
					$prefix = ($prefix == 'project_short') ? 'project' : $prefix;
					$prefix = ($prefix == 'dept') ? 'department' : $prefix;
					$page = ($prefix == 'forum' || $prefix == 'message') ? 'viewer' : 'view';
					$link = '?m=' . apm_pluralize ( $prefix ) . '&a=' . $page . '&';
					$link = ($prefix == 'message') ? '?m=forums&a=' . $page . '&' : $link;
					$prefix = ($prefix == 'department') ? 'dept' : $prefix;
					$link .= $prefix . '_id=' . $this->tableRowData [$prefix . '_id'];
					$link .= ($prefix == 'task_log') ? '&tab=1&task_id=' . $this->tableRowData ['task_id'] : '';
					$icon = ($fieldName == 'file_name') ? '<button class="btn btn-default btn-xs"><span class="glyphicon glyphicon-' . getIcon ( $this->tableRowData ['file_type'] ) . '" /></span></button>&nbsp;' : '';
					$cell = '<a href="' . $link . '">' . $icon . $value . '</a>';
					// TODO: task_logs are another oddball..
					$cell = ($prefix == 'task_log') ? str_replace ( 'task_logs', 'tasks', $cell ) : $cell;
				}
				break;
			case '_author' :
			case '_creator' :
			case '_owner' :
			case '_updator' :
				if (( int ) $value) {
					$obj = new CContact ();
					$obj->findContactByUserid ( $value );
					$suffix .= ' nowrap';
					$link = '?m=users&a=view&user_id=' . $value;
					$cell = '<a href="' . $link . '">' . $obj->contact_display_name . '</a>';
				} else {
					$cell = $value;
				}
				break;
			// The above are all contact/user display names, the below are numbers.
			case '_count' :
			case '_hours' :
				$cell = $value;
				break;
			case '_duration' :
				$durnTypes = apmgetSysVal ( 'TaskDurationType' );
				$cell = $value . ' ' . $this->AppUI->_ ( $durnTypes [$this->tableRowData ['task_duration_type']] );
				break;
			case '_size' :
				$cell = file_size ( $value );
				break;
			case '_budget' :
				switch (apmgetConfig ( 'host_locale' )) {
					case 'pl_PL' :
						$cell = formatCurrency ( $value, $this->AppUI->getPref ( 'CURRENCYFORM' ) );
						$cell .= ' ' . apmgetConfig ( 'currency_symbol' );
						break;
					default :
						$cell = apmgetConfig ( 'currency_symbol' );
						$cell .= formatCurrency ( $value, $this->AppUI->getPref ( 'CURRENCYFORM' ) );
				}
				break;
			case '_url' :
				$value = str_replace ( array (
						'"',
						'"',
						'<',
						'>' 
				), '', $value );
				$cell = apm_url ( $value );
				break;
			case '_email' :
				$cell = apm_email ( $value );
				break;
			case '_birthday' :
			case '_date' :
				$myDate = intval ( $value ) ? new apm_Utilities_Date ( $value ) : null;
				$cell = $myDate ? $myDate->format ( $this->df ) : '-';
				$suffix= 'input-group date '.$suffix;
				break;
			case '_actual' :
				$end_date = intval ( $this->tableRowData ['project_end_date'] ) ? new apm_Utilities_Date ( $this->tableRowData ['project_end_date'] ) : null;
				$actual_end_date = intval ( $this->tableRowData ['project_actual_end_date'] ) ? new apm_Utilities_Date ( $this->tableRowData ['project_actual_end_date'] ) : null;
				$style = (($actual_end_date < $end_date) && ! empty ( $end_date )) ? 'style="font-weight:bold"' : '';
				if ($actual_end_date) {
					$cell = '<a href="?m=tasks&a=view&task_id=' . $this->tableRowData ['project_last_task'] . '" ' . $style . '>' . $actual_end_date->format ( $this->df ) . '</a>';
				} else {
					$cell = '-';
				}
				break;
			case '_created' :
			case '_datetime' :
			case '_update' :
			case '_updated' :
				$myDate = intval ( $value ) ? new apm_Utilities_Date ( $this->AppUI->formatTZAwareTime ( $value, '%Y-%m-%d %T' ) ) : null;
				$cell = $myDate ? $myDate->format ( $this->dtf ) : '-';
				break;
			case '_description' :
				$cell = apm_textarea ( $value );
				break;
			case '_priority' :
				$mod = ($value > 0) ? 'up' : 'down';
				switch (abs ( $value )) {
					case 1 :
						$clr = 'warning';
						break;
					case 2 :
						$clr = 'danger';
						break;
					default :
						$clr = 'default';
				}
				
				$image = '<label class="label label-' . $clr . '"><span class="glyphicon glyphicon-arrow-' . $mod . '" alt=""></span></label>';
				$cell = ($value != 0) ? $image : '';
				break;
			case '_complete' :
				if (is_array ( $value )) {
					
					$cell = '<label class="btn btn-default btn-xs">' . round ( $value [0] ) . '%' . '</label>';
					switch ($value [1]) {
						case 'late' :
							$cell .= '<label class="label label-info"><span class="glyphicon glyphicon-bell"></span> ' . $this->AppUI->_ ( 'Overdue' ) . '</label>';
							break;
						case 'done' :
							$cell .= '<label class="label label-info"><span class="glyphicon glyphicon-bell"></span> ' . $this->AppUI->_ ( 'Done' ) . '</label>';
							break;
						case 'active' :
							$cell .= '<label class="label label-default">' . $this->AppUI->_ ( 'Started and on time' ) . '</label>';
							break;
						case 'notstarted' :
							$cell .= '<label class="label label-default"><span class="glyphicon glyphicon-bell"></span> ' . $this->AppUI->_ ( 'Should have started' ) . '</label>';
							break;
						case 'future' :
							$cell .= '<label class="label label-default"> ' . $this->AppUI->_ ( 'Future task' ) . '</label>';
							break;
						default :
							$cell .= '<label class="label label-default"> ' . $this->AppUI->_ ( 'Key' ) . '</label>';
							break;
					}
				} else
					$cell = round ( $value ) . '%';
				break;
			case '_id' :
				if (is_array ( $value )) {
					$cell = $value;
				}
				break;
			
			case '_assignment' :
			case '_allocated' :
			case '_allocation' :
				$cell = round ( $value ) . '%';
				break;
			case '_password' :
				$cell = '(' . $this->AppUI->_ ( 'hidden' ) . ')';
				break;
			case '_version' :
				$value [0] = ( int ) (100 * $value [0]);
				$cell = number_format ( $value [0] / 100, 2 ) . $value [1];
				break;
			case '_identifier' :
				$additional = 'style=" color:' . bestColor ( $value ) . '" ';
				if ($this->tableRowData ['project_percent_complete'] == null) {
					$this->tableRowData ['project_percent_complete'] = 0;
				}
				$cell = '<div style="float:left; padding-right:3px;">' . $this->tableRowData ['project_percent_complete'] . '%</div><div class="progress">
                		               
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                		aria-valuenow="' . $this->tableRowData ['project_percent_complete'] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $this->tableRowData ['project_percent_complete'] . '%">

                		</div>
                		</div>';
				break;
			case '_project' :
				$module = substr ( $suffix, 1 );
				$class = 'C' . ucfirst ( $module );
				
				$obj = new $class ();
				$obj->load ( $value );
				$color = $obj->project_color_identifier;
				$link = '?m=' . apm_pluralize ( $module ) . '&a=view&' . $module . '_id=' . $value;
				$cell = '<span><a href="' . $link . '">' . $obj->{"$module" . '_name'} . '</a></span>';
				$suffix .= ' _name';
				break;
			case '_assignees' :
				$cell = $value;
				break;
			case '_problem' :
				if ($value) {
					$cell = '<a href="?m=tasks&a=index&f=all&project_id=' . $this->tableRowData ['project_id'] . '">';
					$cell .= apmshowImage ( 'icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem' );
					$cell .= '</a>';
				} else {
					$cell = '-';
				}
				break;
			case '_options' :
			case '_category' :
			case '_pay':
				$value = (isset ( $custom [$fieldName] )) ? $custom [$fieldName] [$value] : $value;
				$cell = $value;
				break;
			case '_default' :
				$value = (isset ( $custom [$fieldName] )) ? $custom [$fieldName] [$value] : $value;
				$cell = htmlspecialchars ( $value );
				break;
			default :
				$value = (isset ( $custom [$fieldName] )) ? $custom [$fieldName] [$value] : $value;
				$cell = htmlspecialchars ( $value );
		}
		
		$begin = '<td ' . $additional . 'class="' . $suffix . '">';
		$end = '</td>';
		
		return $begin . $cell . $end;
	}
	
	/**
	 *
	 * @deprecated
	 *
	 */
	public function createColumn($fieldName, $value) {
		trigger_error ( "The method createColumn has been deprecated in v3.0 and will be removed by v4.0. Please use createCell instead.", E_USER_NOTICE );
		
		return $this->createCell ( $fieldName, $value [$fieldName] );
	}
	
	/**
	 *
	 * @deprecated
	 *
	 */
	public static function renderColumn(apm_Core_CAppUI $AppUI, $fieldName, $row) {
		global $apmconfig;
		trigger_error ( "The static method renderColumn has been deprecated and will be removed by v4.0.", E_USER_NOTICE );
		
		$last_underscore = strrpos ( $fieldName, '_' );
		$suffix = ($last_underscore !== false) ? substr ( $fieldName, $last_underscore ) : $fieldName;
		
		switch ($suffix) {
			case '_creator' :
			case '_owner' :
				$s .= apmgetUsernameFromID ( $row [$fieldName] );
				break;
			case '_budget' :
				switch (apmgetConfig ( 'host_locale' )) {
					case 'pl_PL' :
						$cell = formatCurrency ( $value, $this->AppUI->getPref ( 'CURRENCYFORM' ) );
						$cell .= ' ' . apmgetConfig ( 'currency_symbol' );
						break;
					default :
						$cell = apmgetConfig ( 'currency_symbol' );
						$cell .= formatCurrency ( $value, $this->AppUI->getPref ( 'CURRENCYFORM' ) );
				}
				break;
			case '_url' :
				$s = apm_url ( $row [$fieldName] );
				break;
			case '_date' :
				$df = $AppUI->getPref ( 'SHDATEFORMAT' );
				$myDate = intval ( $row [$fieldName] ) ? new apm_Utilities_Date ( $row [$fieldName] ) : null;
				$s = ($myDate ? $myDate->format ( $df ) : '-');
				break;
			default :
				$s = htmlspecialchars ( $row [$fieldName], ENT_QUOTES );
		}
		
		return '<td nowrap="nowrap">' . $s . '</td>';
	}
}