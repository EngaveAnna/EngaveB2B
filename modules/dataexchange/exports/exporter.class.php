<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

define ( "INDENT", "\t" );
define ( "LF", "\n" );
abstract class Exporter {
	public $version = null;
	public $file = null;
	public $options = null;
	public $project = null;
	public $taskstree = array ();
	public $resources = array ();
	public $assignments = array ();
	public $calendar = array ();
	private $max_user_id = 0;
	public function __construct($version, $file, $options = array()) {
		$this->version = $version;
		$this->file = $file;
		$this->options = $options;
	}
	protected function loadProject($project_id) {
		global $AppUI;
		$q = new apm_Database_Query ();
		$q->addQuery ( 'projects.*' );
		$q->addQuery ( 'CONCAT(coo.contact_first_name, \' \', coo.contact_last_name) as owner' );
		$q->addQuery ( 'CONCAT(coc.contact_first_name, \' \', coc.contact_last_name) as creator' );
		$q->addQuery ( 'company_name' );
		$q->addTable ( 'projects' );
		$q->leftJoin ( 'companies', 'c', 'project_company = c.company_id' );
		$q->leftJoin ( 'users', 'uo', 'project_owner = uo.user_id' );
		$q->leftJoin ( 'contacts', 'coo', 'uo.user_contact = coo.contact_id' );
		$q->leftJoin ( 'users', 'uc', 'project_creator = uc.user_id' );
		$q->leftJoin ( 'contacts', 'coc', 'uc.user_contact = coc.contact_id' );
		$q->addWhere ( 'project_id = ' . $project_id );
		list ( $project ) = $q->loadList ();
		$currency = formatCurrency ( 1234.56789, $AppUI->setUserLocale ( $AppUI->user_prefs ['CURRENCYFORM'], false ) );
		if (preg_match ( "/^([^\d]*)1([^\d]*)234([^\d]+)5(\d+)([^\d]*)$/", $currency, $T )) {
			$currencydigits = strlen ( $T [4] ) + 1;
			if ($T [1]) {
				$currencycode = trim ( $T [1] );
				$currencysymbolposition = substr ( $T [1], - 1 ) == ' ' ? 2 : 0;
			} else {
				$currencycode = trim ( $T [5] );
				$currencysymbolposition = substr ( $T [5], 0, 1 ) == ' ' ? 3 : 1;
			}
		}
		$project ['currency_digits'] = $currencydigits;
		$project ['currency_code'] = $currencycode;
		$project ['currency_symbol_position'] = $currencysymbolposition;
		$categories = apmgetSysVal ( "ProjectType" );
		$project ['project_category'] = $AppUI->_ ( $categories [$project ['project_type']] );
		$project ['project_work'] = 0;
		$project ['project_file'] = $this->file;
		$q->clear ();
		$q->addTable ( 'tasks' );
		$q->addQuery ( 'ROUND(SUM(task_duration),2)' );
		$q->addWhere ( 'task_project = ' . ( int ) $project ['project_id'] . ' AND task_duration_type = 24 AND task_dynamic <> 1' );
		$days = $q->loadResult ();
		$q->clear ();
		$q->addTable ( 'tasks' );
		$q->addQuery ( 'ROUND(SUM(task_duration),2)' );
		$q->addWhere ( 'task_project = ' . ( int ) $project ['project_id'] . ' AND task_duration_type = 1 AND task_dynamic <> 1' );
		$hours = $q->loadResult ();
		$total_project_hours = $days * apmgetConfig ( 'daily_working_hours' ) + $hours;
		$project ['project_duration'] = rtrim ( $total_project_hours, '.' );
		$this->project = $project;
		$this->loadTasksTree ();
		$this->loadCalendar ();
		$this->loadResources ();
		$this->loadAssignments ();
	}
	protected function loadCalendar() {
		global $AppUI;
		if ($AppUI->isActiveModule ( 'holiday' )) {
			require_once apm_BASE_DIR . "/modules/holiday/holiday_functions.class.php";
			$startyear = substr ( $this->project ['project_start_date'], 0, 4 );
			$endyear = substr ( $this->project ['project_end_date'], 0, 4 );
			for($year = $startyear; $year <= $endyear; $year ++) {
				$start = new apm_Utilities_Date ( $year . "-01-01" );
				$end = new apm_Utilities_Date ( $year . "-12-31" );
				$holidays = HolidayFunctions::getDefaultCalendarHolidaysForDatespan ( $start, $end );
				foreach ( $holidays as $holiday ) {
					$endDate = clone $holiday ['endDate'];
					$endDate->setHour ( 23 );
					$endDate->setMinute ( 59 );
					array_push ( $this->calendar, array (
							'type' => $holiday ['type'],
							'startDate' => $holiday ['startDate'],
							'endDate' => $endDate 
					) );
				}
			}
		}
	}
	protected function loadResources() {
		global $AppUI;
		$q = new apm_Database_Query ();
		$q->addTable ( 'users', 'u' );
		$q->addQuery ( 'u.*, c.*, d.dept_name, cm.method_value as contact_url' );
		$q->innerJoin ( "contacts", "c", "c.contact_id = u.user_contact" );
		$q->innerJoin ( "user_tasks", "ut", "ut.user_id = u.user_id" );
		$q->innerJoin ( "tasks", "t", "t.task_id = ut.task_id" );
		$q->leftJoin ( "departments", "d", "d.dept_id = c.contact_department" );
		$q->leftJoin ( "contacts_methods", "cm", "cm.contact_id = c.contact_id and cm.method_name = 'url'" );
		$q->addWhere ( "t.task_project=" . $this->project ['project_id'] );
		$users = $q->loadList ();
		if ($AppUI->isActiveModule ( 'holiday' )) {
			require_once apm_BASE_DIR . "/modules/holiday/holiday_functions.class.php";
			$startyear = substr ( $this->project ['project_start_date'], 0, 4 );
			$endyear = substr ( $this->project ['project_end_date'], 0, 4 );
		}
		$this->max_user_id = 0;
		$this->resources = array ();
		if (count ( $users ) > 0) {
			$perms = &$AppUI->acl ();
			foreach ( $users as $u => $user ) {
				$users [$u] ['resource_id'] = $user ['user_id'];
				$users [$u] ['resource_type'] = 1;
				$users [$u] ['resource_name'] = $user ['contact_first_name'] . ' ' . $user ['contact_last_name'];
				$users [$u] ['resource_code'] = $user ['user_username'];
				$users [$u] ['resource_group'] = $user ['dept_name'];
				$users [$u] ['resource_log_hours'] = $this->computeResourceLog ( $user );
				$users [$u] ['resource_max_units'] = 1;
				$users [$u] ['resource_peak_units'] = 1;
				$users [$u] ['resource_calendar_id'] = $user ['user_id'];
				$users [$u] ['resource_notes'] = $user ['contact_notes'];
				$users [$u] ['resource_inactive'] = $perms->isUserPermitted ( $user ['user_id'] ) ? 0 : 1;
				$users [$u] ['resource_holidays'] = array ();
				if ($AppUI->isActiveModule ( 'holiday' )) {
					for($year = $startyear; $year <= $endyear; $year ++) {
						$start = new apm_Utilities_Date ( $year . "-01-01" );
						$end = new apm_Utilities_Date ( $year . "-12-31" );
						$holidays = HolidayFunctions::getWhitelistForDatespan ( $start, $end, $user ['user_id'] );
						foreach ( $holidays as $holiday ) {
							$endDate = clone $holiday ['endDate'];
							$endDate->setHour ( 23 );
							$endDate->setMinute ( 59 );
							array_push ( $users [$u] ['resource_holidays'], array (
									'type' => $holiday ['type'],
									'startDate' => $holiday ['startDate'],
									'endDate' => $endDate 
							) );
						}
					}
				}
				$this->resources [$users [$u] ['resource_id']] = $users [$u];
				$this->max_user_id = max ( $user ['user_id'], $this->max_user_id );
			}
		}
		$q->clear ();
		$q->addTable ( 'resources', 'r' );
		$q->addQuery ( 'r.*, rt.*, ty.*' );
		$q->innerJoin ( "resource_tasks", "rt", "rt.resource_id = r.resource_id" );
		$q->innerJoin ( "tasks", "t", "t.task_id = rt.task_id" );
		$q->leftJoin ( "resource_types", "ty", "ty.resource_type_id = r.resource_type" );
		$q->addWhere ( "t.task_project=" . $this->project ['project_id'] );
		$resources = $q->loadList ();
		foreach ( $resources as $r => $resource ) {
			$resources [$r] ['resource_id'] = $this->max_user_id + $resource ['resource_id'];
			$resources [$r] ['resource_type'] = 0;
			$resources [$r] ['resource_code'] = $resource ['resource_key'];
			$resources [$r] ['resource_group'] = $resource ['resource_type_name'];
			$resources [$r] ['resource_max_units'] = $resource ['resource_max_allocation'] / 100;
			$resources [$r] ['resource_peak_units'] = $resource ['resource_max_allocation'] / 100;
			$resources [$r] ['resource_calendar_id'] = 1;
			$resources [$r] ['resource_notes'] = $resource ['resource_note'];
			$resources [$r] ['resource_inactive'] = 0;
			$resources [$r] ['contact_id'] = 0;
			$this->resources [$resources [$r] ['resource_id']] = $resources [$r];
		}
	}
	protected function loadAssignments() {
		$this->assignments = array ();
		$q = new apm_Database_Query ();
		$q->addTable ( 'user_tasks', 'ut' );
		$q->addQuery ( 'ut.*, t.*' );
		$q->innerJoin ( "tasks", "t", "t.task_id = ut.task_id" );
		$q->addWhere ( "t.task_project=" . $this->project ['project_id'] );
		$dbassignments = $q->loadList ();
		foreach ( $dbassignments as $assignment ) {
			$q->addTable ( 'task_log', 'tl' );
			$q->addQuery ( 'tl.*' );
			$q->addWhere ( "tl.task_log_task=" . $assignment ['task_id'] );
			$q->addWhere ( "tl.task_log_creator=" . $assignment ['user_id'] );
			$assignment ['resource_id'] = $assignment ['user_id'];
			$tasklogs = $q->loadList ();
			$assignment ['task_log'] = array ();
			$task_log_hours = 0;
			foreach ( $tasklogs as $tasklog ) {
				$assignment ['task_log'] [] = array (
						'task_log_date' => $tasklog ['task_log_date'],
						'task_log_hours' => $tasklog ['task_log_hours'],
						'task_log_costcode' => $tasklog ['task_log_costcode'],
						'task_log_related_url' => $tasklog ['task_log_related_url'] 
				);
				$task_log_hours += $tasklog ['task_log_hours'];
			}
			$assignment ['task_log_hours'] = $task_log_hours;
			$this->assignments [] = $assignment;
		}
		$q->clear ();
		$q->addTable ( "resource_tasks", "rt" );
		$q->addQuery ( 'rt.*, t.*' );
		$q->innerJoin ( "tasks", "t", "t.task_id = rt.task_id" );
		$q->addWhere ( "t.task_project=" . $this->project ['project_id'] );
		$dbassignments = $q->loadList ();
		foreach ( $dbassignments as $assignment ) {
			$assignment ['resource_id'] = $this->max_user_id + $assignment ['resource_id'];
			$assignment ['perc_assignment'] = $assignment ['percent_allocated'];
			$this->assignments [] = $assignment;
		}
	}
	protected function loadTasksTree() {
		$q = new apm_Database_Query ();
		$q->addQuery ( 't.*' );
		$q->addQuery ( 'CONCAT(contact_first_name, \' \', contact_last_name) as task_owner_contact' );
		$q->addQuery ( 'tc.critical_task' );
		$q->addTable ( 'tasks', 't' );
		$q->leftJoin ( 'users', 'u', 'task_owner = user_id' );
		$q->leftJoin ( 'contacts', 'co', 'user_contact = contact_id' );
		$q->leftJoin ( 'tasks_critical', 'tc', 'tc.task_project = t.task_project and tc.critical_task = t.task_id' );
		$q->addWhere ( 't.task_project = ' . $this->project ['project_id'] );
		$q->addOrder ( 't.task_parent, t.task_start_date, t.task_id' );
		$dbtasks = $q->loadList ();
		$refs = array ();
		$this->taskstree = array ();
		$max_end_date = new apm_Utilities_Date ( $this->project ['project_start_date'] );
		foreach ( $dbtasks as $i => $task ) {
			$task_end_date = new apm_Utilities_Date ( $task ['task_end_date'] );
			if ($max_end_date->duplicate ()->before ( $task_end_date->duplicate () )) {
				$max_end_date = $task_end_date;
			}
			$task ['task_predecessors'] = $this->getPredecessors ( $task );
			$task ['task_work'] = $this->computeTaskWork ( $task );
			$task ['task_log_hours'] = $this->computeTaskLog ( $task );
			$task ['task_regular_work'] = $task ['task_work'];
			$task ['task_overtime_work'] = 0;
			$task ['task_actual_duration'] = $this->getTaskActualDuration ( $task );
			$task ['task_remaining_duration'] = $this->getTaskRemainingDuration ( $task );
			$thisref = &$refs [$task ['task_id']];
			$thisref ['task_parent'] = $task ['task_parent'];
			$thisref ['task'] = $task;
			if ($task ['task_id'] == $task ['task_parent']) {
				$this->taskstree [$task ['task_id']] = &$thisref;
			} else {
				$refs [$task ['task_parent']] ['children'] [$task ['task_id']] = &$thisref;
			}
		}
		$this->project ['project_actual_end_date'] = $max_end_date->getDate ();
		if ($this->project ['project_end_date']) {
			$project_end_date = new apm_Utilities_Date ( $this->project ['project_end_date'] );
			if ($project_end_date->before ( $task_end_date )) {
				$this->project ['project_end_date'] = $this->project ['project_actual_end_date'];
			}
		} else {
			$this->project ['project_end_date'] = $this->project ['project_actual_end_date'];
		}
		$this->computeWBS ( $this->taskstree );
	}
	private function computeWBS(&$taskstree, $level = 1, $number = '') {
		$wbslevel = 0;
		foreach ( $taskstree as $t => $tasknode ) {
			$taskstree [$t] ['task'] ['outline_level'] = $level;
			$wbslevel ++;
			$taskstree [$t] ['task'] ['outline_number'] = $number ? $number . "." . $wbslevel : $wbslevel;
			$taskstree [$t] ['task'] ['wbs'] = $taskstree [$t] ['task'] ['outline_number'];
			$taskstree [$t] ['task'] ['wbs_level'] = $wbslevel;
			$taskstree [$t] ['task'] ['task_summary'] = isset ( $tasknode ['children'] ) ? 1 : 0;
			if (isset ( $tasknode ['children'] )) {
				$this->computeWBS ( $taskstree [$t] ['children'], $level + 1, $taskstree [$t] ['task'] ['outline_number'] );
			}
		}
	}
	private function getTaskActualDuration(&$task) {
		return ($task ['task_duration'] * $task ['task_percent_complete']) / 100;
	}
	private function getTaskRemainingDuration(&$task) {
		return ($task ['task_duration'] * (100 - $task ['task_percent_complete'])) / 100;
	}
	private function computeTaskWork(&$task) {
		$taskwork = 0;
		$hoursperday = apmgetConfig ( 'cal_day_end' ) - apmgetConfig ( 'cal_day_start' );
		$q = new apm_Database_Query ();
		$q->addTable ( 'user_tasks', 'ut' );
		$q->addQuery ( 'ut.*' );
		$q->addWhere ( "ut.task_id=" . $task ['task_id'] );
		$dbassignments = $q->loadList ();
		foreach ( $dbassignments as $assignment ) {
			switch ($task ['task_duration_type']) {
				case 1 :
					$taskwork += (($task ['task_duration'] * $assignment ['perc_assignment']) / 100);
					break;
				default :
					$taskwork += (($task ['task_duration'] * $hoursperday * $assignment ['perc_assignment']) / 100);
			}
		}
		// TODO : consider holidays for user
		$this->project ['project_work'] += $taskwork;
		return $taskwork;
	}
	private function computeTaskLog(&$task) {
		$q = new apm_Database_Query ();
		$q->addTable ( 'task_log', 'tl' );
		$q->addQuery ( 'SUM(tl.task_log_hours)' );
		$q->addWhere ( "tl.task_log_task=" . $task ['task_id'] );
		$tasklog = $q->loadResult ();
		$this->project ['project_log_hours'] += $tasklog;
		return $tasklog;
	}
	private function computeResourceLog(&$user) {
		$q = new apm_Database_Query ();
		$q->addTable ( 'task_log', 'tl' );
		$q->addQuery ( 'SUM(tl.task_log_hours)' );
		$q->addWhere ( "tl.task_log_creator=" . $user ['user_id'] );
		return $q->loadResult ();
	}
	private function getPredecessors(&$task) {
		$q = new apm_Database_Query ();
		$q->addTable ( 'task_dependencies' );
		$q->addQuery ( '*' );
		$q->addWhere ( "dependencies_task_id=" . $task ['task_id'] );
		$dbpredecessors = $q->loadList ();
		$predecessors = array ();
		foreach ( $dbpredecessors as $predecessor ) {
			$predecessors [] = $predecessor ['dependencies_req_task_id'];
		}
		return $predecessors;
	}
	public function exportProject($project_id = -1) {
		global $AppUI;
		if ($project_id == - 1) {
			return '';
		}
		$this->loadProject ( $project_id );
		$class = substr ( get_class ( $this ), 0, - 8 );
		require_once strtolower ( $class ) . ".class.php";
		$projectinstance = new $class ( $this, $this->project );
		return $projectinstance->dump ();
	}
	protected function dumpXMLElement($xmlelement, $xmlcontent, $attributes = array(), $level = 0) {
		$output = $indent = "";
		for($i = 0; $i < $level; $i ++)
			$indent .= INDENT;
		if ($xmlelement {0} != "#") {
			if (! is_array ( $xmlcontent ) && strlen ( $xmlcontent ) == 0 && empty ( $attributes )) {
				return "";
			}
			$output .= "$indent<$xmlelement";
			foreach ( $attributes as $attr => $value ) {
				$output .= ' ' . $attr . '="' . $value . '"';
			}
			if ((is_array ( $xmlcontent ) && empty ( $xmlcontent )) || (! is_array ( $xmlcontent ) && strlen ( $xmlcontent ) == 0)) {
				return $output . "/>" . LF;
			}
			$output .= ">";
		}
		if (is_array ( $xmlcontent )) {
			if ($xmlelement {0} != "#")
				$output .= LF;
			else
				$level --;
			foreach ( $xmlcontent as $element => $content ) {
				if (preg_match ( "/^\d+$/", $element )) {
					foreach ( $content as $subelement => $subcontent ) {
						$output .= $this->dumpXMLElement ( $subelement, $subcontent, array (), $level + 1 );
					}
				} else {
					$output .= $this->dumpXMLElement ( $element, $content, array (), $level + 1 );
				}
			}
			if ($xmlelement {0} != "#")
				$output .= "$indent";
		} elseif ($xmlelement {0} != "#") {
			$output .= $xmlcontent;
		}
		if ($xmlelement {0} != "#") {
			if (preg_match ( "/^([^\s]+)\s.*$/", $xmlelement, $T )) {
				$xmlelement = $T [1];
			}
			$output .= "</$xmlelement>" . LF;
		}
		return $output;
	}
	public function export($item) {
		$xmlns = 'http://schemas.microsoft.com/project';
		// $xmlns .= $this->version == "2003" ? "" : "/" . $this->version;
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . $this->dumpXMLElement ( "Project", $this->exportProject ( $item ), array (
				'xmlns' => $xmlns 
		) );
	}
}
