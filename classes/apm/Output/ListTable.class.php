<?php
/**
 * This class helps us build simple list table for the various modules. Using
 *   this ensures we have similar layouts and styles across the board. You can
 *   always hardcode your own.
 *
 * @package     apmProject\output
 */
class apm_Output_ListTable extends apm_Output_HTMLHelper {
	protected $_AppUI = null;
	protected $module = '';
	protected $_fieldKeys = array ();
	protected $_fieldNames = array ();
	protected $_fieldPriority = array ();
	public $cellCount = 0;
	protected $_before = array ();
	protected $_after = array ();
	public function __construct($AppUI) {
		$this->_AppUI = $AppUI;
		
		parent::__construct ( $AppUI );
	}
	public function startTable($id = '') {
		return '<div class="table-responsive" data-pattern="priority-columns" data-add-focus-btn="false">
    	<table cellspacing="0" id="rwd-table" class="table table-bordered table-striped ' . $id . '">
    	<input type="hidden" id="displayAllBtn-label" value="' . $this->_AppUI->_ ( 'Display All' ) . '">
    	<input type="hidden" id="dropdownBtn-label" value="' . $this->_AppUI->_ ( 'Display' ) . '">';
	}
	public function buildHeader($fields = array(), $sortable = false, $m = '') {
		$this->module = $m;
		$cells = '';
		
		foreach ( $fields as $field ) {
			
			$this->_fieldKeys [] = $field ['module_config_value'];
			$this->_fieldNames [] = $field ['module_config_text'];
			$this->_fieldPriority [] = $field ['module_config_priority'];
			$field ['module_config_priority'] = ($field ['module_config_priority'] == null) ? 1 : $field ['module_config_priority'];

			
			$link = '<span ><p style="float:left; font-weight:normal;">' . $this->_AppUI->_ ( $field ['module_config_text'] ) . "</p>";
			$link .= ($sortable) ? '<p style="right:0px; float:right;"><a role="button" href="?m=' . $this->module . '&orderby=' . $field ['module_config_value'] . '" style="float:right;" class="label label-default btn-xs"><span class="glyphicon glyphicon-sort"></span></a></p>' : '';
			$link .= "</span>";
			$cells .= '<th data-priority="' . $field ['module_config_priority'] . '">' . $link . '</th>';
		}
		
		$this->cellCount = count ( $this->_before ) + count ( $fields ) + count ( $this->_after );
		return '<thead><tr>' . str_repeat ( '<th></th>', count ( $this->_before ) ) . $cells . str_repeat ( '<th></th>', count ( $this->_after ) ) . '</tr></thead>';
	}
	public function buildRows($allRows, $customLookups = array()) {
		$body = '';
		
		if (count ( $allRows ) > 0) {
			foreach ( $allRows as $row ) {
				$body .= $this->buildRow ( $row, $customLookups );
			}
		} else {
			$body .= $this->buildEmptyRow ();
		}
		
		return $body;
	}
	public function buildRow($rowData, $customLookups = array()) {
		$this->stageRowData ( $rowData );
		$row = '<tr>';
		$row .= $this->_buildCells ( $this->_before );
		foreach ( $this->_fieldKeys as $column ) {
			$row .= $this->createCell ( $column, $rowData [$column], $customLookups );
		}
		$row .= $this->_buildCells ( $this->_after );
		$row .= '</tr>';
		return $row;
	}
	public function addBefore($type, $value = '') {
		$this->_before [$type] = $value;
	}
	public function addAfter($type, $value = '') {
		$this->_after [$type] = $value;
	}
	protected function _buildCells($array = array()) {
		$cells = '';
		
		/**
		 * Note: We can't refactor the actual td/class stuff out to the return statement because we may have multiple
		 * inserted cells processed together..
		 * and we need them to remain separate cells.
		 */
		foreach ( $array as $type => $value ) {
			switch ($type) {
				case 'edit' :
					// @note This module determination *only* works if you've followed our naming conventions.
					$pieces = explode ( '_', $value );
					$module = apm_pluralize ( $pieces [0] );
					$contents = '<td class="_' . $type . '">';
					$contents .= '<a href="./index.php?m=' . $module . '&a=addedit&' . $value . '=' . $this->tableRowData [$value] . '">' . apmshowImage ( 'icons/stock_edit-16.png', '16', '16' ) . '</a>';
					$contents .= '</td>';
					break;
				case 'select' :
					$contents = '<td class="_' . $type . '">';
					$contents .= '<input type="checkbox" value="' . $this->tableRowData [$value] . '" name="' . $value . '[]" />';
					$contents .= '</td>';
					break;
				case 'log' :
					$pieces = explode ( '_', $value );
					$module = apm_pluralize ( $pieces [0] );
					$contents = '<td class="_' . $type . '">';
					$contents .= '<a href="./index.php?m=' . $module . '&a=view&tab=1&' . $value . '=' . $this->tableRowData [$value] . '">' . apmshowImage ( 'icons/edit_add.png', '16', '16' ) . '</a>';
					$contents .= '</td>';
					break;
				case 'pin' :
					$image = ($this->tableRowData ['task_pinned']) ? 'pin.gif' : 'unpin.gif';
					$pieces = explode ( '_', $value );
					$module = apm_pluralize ( $pieces [0] );
					$contents = '<td class="_' . $type . '">';
					$contents .= '<a href="./index.php?m=' . $module . '&pin=1&' . $value . '=' . $this->tableRowData [$value] . '">' . apmshowImage ( 'icons/' . $image, '16', '16' ) . '</a>';
					$contents .= '</td>';
					break;
				case 'url' :
					$contents = '<td class="_' . $type . '">';
					$contents .= '<a href="' . $this->tableRowData [$value] . '" target="_blank">' . apmshowImage ( 'forward.png', '16', '16' ) . '</a>';
					$contents .= '</td>';
					break;
				case 'watch' :
					$contents = '<td class="_' . $type . '">';
					$contents .= '<input type="checkbox" name="forum_' . $this->tableRowData [$value] . '"' . ($this->tableRowData ['watch_user'] ? 'checked="checked"' : '') . ' />';
					$contents .= '</td>';
					break;
				case 'options' :
					$contents = '<td>' . $value . '</td>';
					break;
				default :
					$contents = '<td>dd</td>';
			}
			$cells .= $contents;
		}
		
		return $cells;
	}
	public function buildEmptyRow() {
		$row = '<tr><td colspan="' . $this->cellCount . '">' . $this->_AppUI->_ ( 'No data available' ) . '</td></tr>';
		return $row;
	}
	public function endTable() {
		return '</table></div>';
	}
}