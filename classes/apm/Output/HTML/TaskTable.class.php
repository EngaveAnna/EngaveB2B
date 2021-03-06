<?php
/**
 * This class helps us build simple task table for the various modules. Using this ensures we have similar layouts and
 *   styles across the board. You can always hardcode your own.
 *
 * @package     apmProject\output\html
 */
class apm_Output_HTML_TaskTable extends apm_Output_ListTable {
	protected $task = null;
	public function __construct($AppUI, $task = null) {
		$this->task = (is_null ( $task )) ? new CTask () : $task;
		
		parent::__construct ( $AppUI );
	}
	public function buildHeader($fields = array(), $sortable = false, $m = '') {
		$header = parent::buildHeader ( $fields, $sortable, $m );
		
		if ('projectdesigner' == $m) {
			$checkAll = '<th width="1"><input type="checkbox" onclick="select_all_rows(this, \'selected_task[]\')" name="multi_check"/></th></tr>';
			$header = str_replace ( '</tr>', $checkAll, $header );
		}
		return $header;
	}
	public function buildRow($rowData, $customLookups = array()) {
		$this->stageRowData ( $rowData );
		$class = apmFindTaskComplete ( $rowData ['task_start_date'], $rowData ['task_end_date'], $rowData ['task_percent_complete'] );
		
		$row = '<tr>';
		foreach ( $this->_fieldKeys as $key => $column ) {
			if ('task_percent_complete' == $column) {
				
				$rowData [$column] = array (
						0 => $rowData [$column],
						1 => $class 
				);
			}
			
			if ('task_name' == $column) {
				$prefix = $suffix = '';
				if ($rowData ['depth'] > 1) {
					$prefix .= str_repeat ( '&nbsp;', ($rowData ['depth'] - 1) * 4 ) . '<img src="' . apmfindImage ( 'corner-dots.gif' ) . '" />';
				}
				if ($rowData ['children'] > 0) {
					$prefix .= '<img src="' . apmfindImage ( 'icons/collapse.gif' ) . '" />&nbsp;';
				}
				if ('' != $rowData ['task_description']) {
					$prefix .= apmtoolTip ( $this->_AppUI->_ ( 'Task Description' ), $rowData ['task_description'] );
					$suffix .= apmendTip ();
				}
				if ($rowData ['task_milestone']) {
					$suffix .= '&nbsp;' . '<label class="label label-default" data-placement="right" data-toggle="tooltip" data-container="body" data-original-title="' . $this->_AppUI->_ ( 'Milestone' ) . '"><span class="glyphicon glyphicon-star"></span></label>';
				}
				if (1 == $rowData ['task_dynamic'] || $rowData ['task_milestone']) {
					$rowData [$column] = '<b>' . $rowData [$column] . '</b>';
				}
				
				$rowData [$column] = $prefix . $rowData [$column] . $suffix;
			}
			if ('task_assignees' == $column) {
				$parsed = array ();
				$assignees = $this->task->assignees ( $rowData ['task_id'] );
				foreach ( $assignees as $assignee ) {
					$parsed [] = '<a href="?m=users&a=view&user_id=' . $assignee ['user_id'] . '">' . $assignee ['contact_name'] . '</a>';
				}
				$rowData [$column] = implode ( ', ', $parsed );
			}
			
			$row .= $this->createCell ( $column, $rowData [$column], $customLookups );
		}
		if ('projectdesigner' == $this->module) {
			$row .= '<td class="data"><input type="checkbox" name="selected_task[]" value="' . $rowData ['task_id'] . '"/></td>';
		}
		$row .= '</tr>';
		return $row;
	}
}