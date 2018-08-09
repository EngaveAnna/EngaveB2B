<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "msproject2003.class.php";
class MSProject2007 extends MSProject2003 {
	public function dump() {
		return array (
				
				// The version of Microsoft Office Project from which the project was saved.
				// Values are: 12=Project 2007.
				'SaveVersion' => 12,
				
				// The unique ID of the project.
				'UID' => $this->project ['project_short_name'],
				
				// The name of the project.
				'Name' => html_entity_decode ( $this->project ['project_file'], ENT_COMPAT, "UTF-8" ),
				
				// The title of the project.
				'Title' => html_entity_decode ( $this->project ['project_name'], ENT_COMPAT, "UTF-8" ),
				
				// The category the project belongs to.
				'Category' => html_entity_decode ( $this->project ['project_category'], ENT_COMPAT, "UTF-8" ),
				
				// The company that owns the project.
				'Company' => html_entity_decode ( $this->project ['company_name'], ENT_COMPAT, "UTF-8" ),
				
				// The manager of the project.
				'Manager' => html_entity_decode ( $this->project ['owner'], ENT_COMPAT, "UTF-8" ),
				
				// The author of the project.
				'Author' => html_entity_decode ( $this->project ['creator'], ENT_COMPAT, "UTF-8" ),
				
				// The date that the project was created.
				'CreationDate' => $this->dumpDateTime ( array (
						$this->project ['project_created'],
						$this->project ['project_start_date'] 
				) ),
				
				// The number of times the project has been saved.
				// 'Revision' => 1,
				
				// The date that the project was last saved.
				'LastSaved' => $this->dumpDateTime ( array (
						$this->project ['project_updated'],
						$this->project ['project_created'],
						$this->project ['project_start_date'] 
				) ),
				
				// Indicates whether the project is scheduled from
				// its start date or finish date.
				'ScheduleFromStart' => 1,
				
				// The date and time that a project is scheduled to begin;
				// required if ScheduleFromStart is True.
				'StartDate' => $this->dumpDateTime ( $this->project ['project_start_date'] ),
				
				// The date and time that a project is scheduled to end;
				// required if ScheduleFromStart is False.
				'FinishDate' => $this->dumpDateTime ( $this->project ['project_end_date'] ),
				
				// The month the fiscal year begins.
				'FYStartDate' => apmgetParam ( $this->exporter->options, 'FYStartDate', '1' ),
				
				// The number of days past its end date that a task can go before
				// Microsoft Office Project 2007 marks that task as a critical task.
				'CriticalSlackLimit' => 0,
				
				// The number of digits that appear after the decimal
				// when currency values are shown in Project.
				'CurrencyDigits' => $this->project ['currency_digits'],
				
				// The currency symbol used to represent the type of currency
				// used in the project.
				'CurrencySymbol' => html_entity_decode ( apmgetConfig ( 'currency_symbol' ), ENT_COMPAT, "UTF-8" ),
				
				// The three letter currency character code as defined in ISO 4217.
				// Valid values are: USD.
				'CurrencyCode' => html_entity_decode ( $this->project ['currency_code'], ENT_COMPAT, "UTF-8" ),
				
				// Indicates the placement of the currency symbol
				// in relation to the currency value:
				'CurrencySymbolPosition' => $this->project ['currency_symbol_position'],
				
				// The unique ID for the calendar used in the project.
				'CalendarUID' => 1,
				
				// The default start time for all new tasks.
				'DefaultStartTime' => apmgetConfig ( 'cal_day_start' ) . ':00:00',
				
				// The default finish time for all new tasks.
				'DefaultFinishTime' => apmgetConfig ( 'cal_day_end' ) . ':00:00',
				
				// The default number of minutes per day.
				'MinutesPerDay' => ($mins_per_day = (apmgetConfig ( 'cal_day_end' ) - apmgetConfig ( 'cal_day_start' )) * 60),
				
				// The default number of minutes per week.
				'MinutesPerWeek' => ($mins_per_day * count ( explode ( ',', apmgetConfig ( 'cal_working_days' ) ) )),
				
				// The default number of working days per month.
				'DaysPerMonth' => (4 * count ( explode ( ',', apmgetConfig ( 'cal_working_days' ) ) )),
				
				// The default type for all new tasks in the project.
				// Values are: 0=Fixed Units, 1=Fixed Duration, 2=Fixed Work.
				'DefaultTaskType' => apmgetParam ( $this->exporter->options, 'DefaultTaskType', '0' ),
				
				// The default measuring point when fixed costs are accrued.
				// Values are: 1=Start, 2=Prorated, 3=End
				'DefaultFixedCostAccrual' => apmgetParam ( $this->exporter->options, 'DefaultFixedCostAccrual', '3' ),
				
				// The default standard rate for new resources.
				'DefaultStandardRate' => apmgetParam ( $this->exporter->options, 'DefaultStandardRate', '0' ),
				
				// The default overtime rate for new resources.
				'DefaultOvertimeRate' => apmgetParam ( $this->exporter->options, 'DefaultOvertimeRate', '0' ),
				
				// The default format for all durations in the project
				// Values are:
				// 3=m, 4=em, 5=h, 6=eh, 7=d, 8=ed, 9=w, 10=ew, 11=mo, 12=emo, 19=%, 20=e%,
				// 21=null, 35=m?, 36=em?, 37=h?, 38=eh?, 39=d?, 40=ed?, 41=w?, 42=ew?,
				// 43=mo?, 44=emo?, 51=%?, 52=e%? and 53=null.
				'DurationFormat' => apmgetParam ( $this->exporter->options, 'DurationFormat', '5' ),
				
				// The default format for all work durations in the project.
				// Values are: 1=m, 2=h, 3=d, 4=w, 5=mo
				'WorkFormat' => apmgetParam ( $this->exporter->options, 'WorkFormat', '2' ),
				
				// Indicates whether Project automatically calculates actual costs.
				'EditableActualCosts' => 0,
				
				// Indicates whether Project schedules tasks according to their
				// constraint dates instead of any task dependencies.
				'HonorConstraints' => 0,
				
				// The default method for calculating earned value.
				// Values are: 0=Percent Complete, 1=Physical Percent Complete
				'EarnedValueMethod' => 0,
				
				// Indicates whether inserted projects are treated as summary tasks
				// rather than as separate projects for schedule calculation.
				'InsertedProjectsLikeSummary' => 1,
				
				// Indicates whether Project calculates and displays a critical path for
				// each independent network of tasks within a project.
				'MultipleCriticalPaths' => 0,
				
				// Indicates whether new tasks are effort-driven.
				'NewTasksEffortDriven' => apmgetParam ( $this->exporter->options, 'NewTasksEffortDriven', '0' ),
				
				// Indicates whether new tasks have estimated durations.
				'NewTasksEstimated' => apmgetParam ( $this->exporter->options, 'NewTasksEstimated', '0' ),
				
				// Indicates whether in-progress tasks may be split.
				'SplitsInProgressTasks' => 1,
				
				// Indicates whether actual costs are spread to the status date.
				'SpreadActualCost' => 0,
				
				// Indicates whether percent complete is spread to the status date.
				'SpreadPercentComplete' => 0,
				
				// Indicates whether updates to tasks update resources.
				'TaskUpdatesResource' => 1,
				
				// Indicates whether fiscal year numbering is used.
				'FiscalYearStart' => 0,
				
				// The start day of the week.
				'WeekStartDay' => LOCALE_FIRST_DAY,
				
				// Indicates whether the end of completed portions of tasks scheduled
				// to begin after the status date, but begun early,
				// should be moved back to the status date.
				'MoveCompletedEndsBack' => 0,
				
				// Indicates whether the beginning of remaining portions of tasks scheduled
				// to begin after the status date, but begun early,
				// should be moved back to the status date.
				'MoveRemainingStartsBack' => 0,
				
				// Indicates whether the beginning of remaining portions of tasks scheduled
				// to have begun late should be moved up to the status date.
				'MoveRemainingStartsForward' => 0,
				
				// Indicates whether the end of completed portions of tasks scheduled
				// to have been completed before the status date, but begun late,
				// should be moved up to the status date.
				'MoveCompletedEndsForward' => 0,
				
				// The specific baseline used to calculate Variance values.
				// Values are: 0=Baseline, 1=Baseline 1, 2=Baseline 2, 3=Baseline 3,
				// 4=Baseline 4, 5=Baseline 5, 6=Baseline 6, 7=Baseline 7, 8=Baseline 8,
				// 9=Baseline 9, 10=Baseline 10
				'BaselineForEarnedValue' => 0,
				
				// Indicates whether to automatically add new resources to the resource pool.
				'AutoAddNewResourcesAndTasks' => apmgetParam ( $this->exporter->options, 'AutoAddNewResourcesAndTasks', '0' ),
				
				// Date used for calculation and reporting.
				'StatusDate' => '',
				
				// The system date that the XML was generated.
				'CurrentDate' => date ( 'Y-m-d H:i:s' ),
				
				// Indicates whether the project was created by a
				// Microsoft Office Project Server 2007 user or a Microsoft Windows NT user.
				'MicrosoftProjectServerURL' => 1,
				
				// Indicates whether to autolink inserted or moved tasks.
				'Autolink' => apmgetParam ( $this->exporter->options, 'Autolink', '0' ),
				
				// The default start date for a new task.
				// Values are: 0=Project Start Date, 1=Current Date
				'NewTaskStartDate' => apmgetParam ( $this->exporter->options, 'NewTaskStartDate', '0' ),
				
				// The default earned value method for tasks.
				// Values are: 0=Percent Complete, 1=Physical Percent Complete
				'DefaultTaskEVMethod' => apmgetParam ( $this->exporter->options, 'DefaultTaskEVMethod', '0' ),
				
				// Indicates whether the project was edited externally.
				'ProjectExternallyEdited' => 1,
				
				// Date used for calculation and reporting.
				// 'ExtendedCreationDate' => '',
				
				// Indicates whether all actual work has been synchronized with the project.
				'ActualsInSync' => 0,
				
				// Indicates whether to remove all file properties on save.
				'RemoveFileProperties' => 0,
				
				// Indicates whether the project is an administrative project.
				'AdminProject' => 0,
				
				// The collection of outline code definitions associated with the project;
				// these codes may be associated with any number of projects.
				'OutlineCodes' => '',
				
				// The table of entries that define an outline code mask.
				'WBSMasks' => '',
				
				// The collection of extended attribute (custom field) definitions
				// associated with a project.
				'ExtendedAttributes' => $this->dumpExtendedAttributes (),
				
				// The collection of calendars associated with the project.
				'Calendars' => $this->dumpCalendars (),
				
				// The collection of tasks that make up the project.
				'Tasks' => $this->exportTasks (),
				
				// The collection of resources that make up the project.
				'Resources' => $this->dumpResources (),
				
				// The collection of assignments that make up the project.
				'Assignments' => $this->dumpAssignments () 
		)
		;
	}
	protected function dumpCalendars() {
		$calendars = array ();
		$working_days = explode ( ",", apmgetConfig ( "cal_working_days" ) );
		$weekdays = array ();
		for($i = 1; $i <= 7; $i ++) {
			$dayworking = 0;
			$workingtimes = array ();
			if (in_array ( $i - 1, $working_days )) {
				$dayworking = 1;
				$workingtimes [] = array (
						
						// Defines the time worked on the working day.
						// One of these must be present, and there may be no more than five..
						'WorkingTime' => array (
								
								// The start of the working time.
								'FromTime' => apmgetConfig ( 'cal_day_start' ) . ':00:00',
								
								// The end of the working time.
								'ToTime' => apmgetConfig ( 'cal_day_end' ) . ':00:00' 
						) 
				);
			}
			$weekdays [] = array (
					
					// A weekday defines either regular days of the week
					// or exception days in the calendar.
					'WeekDay' => array (
							
							// The type of working day (exception, or Monday - Sunday)
							'DayType' => $i,
							
							// Indicates whether the specified date or date type is a working day.
							'DayWorking' => $dayworking,
							
							// The collection of working times that define the time worked.
							'WorkingTimes' => $workingtimes 
					)
					 
			);
		}
		foreach ( $this->exporter->calendar as $holiday ) {
			$weekdays [] = array (
					
					// A weekday defines either regular days of the week
					// or exception days in the calendar.
					'WeekDay' => array (
							
							// The type of day. Values are: 0=Exception, 1=Sunday, 2=Monday, 3=Tuesday, 4=Wednesday, 5=Thursday, 6=Friday, 7=Saturday.
							'DayType' => 0,
							
							// Indicates whether the specified date or date type is a working day.
							'DayWorking' => 0,
							
							// Defines a set of exception days.
							'TimePeriod' => array (
									
									// The start of the exception time.
									'FromDate' => $this->dumpDateTime ( $holiday ['startDate']->getDate (), false ),
									
									// The end of the exception time.
									'ToDate' => $this->dumpDateTime ( $holiday ['endDate']->getDate (), false ) 
							)
							 
					) 
			);
		}
		$calendars [] = array (
				'Calendar' => array (
						'UID' => 1,
						'Name' => 'Standard',
						'IsBaseCalendar' => 1,
						'BaseCalendarUID' => - 1,
						'WeekDays' => $weekdays 
				) 
		);
		foreach ( $this->exporter->resources as $resource ) {
			if ($resource ['resource_type'] == 1) {
				$uid = $resource ['user_id'];
				$holidays = $resource ['resource_holidays'];
				$weekdays = array ();
				foreach ( $holidays as $holiday ) {
					if ($holiday ['type'] != HOLIDAY_TYPE_CALENDAR_HOLIDAY) {
						$weekdays [] = array (
								'WeekDay' => array (
										'DayType' => 0,
										// Indicates whether the specified date or date type is a working day.
										'DayWorking' => 0,
										'TimePeriod' => array (
												'FromDate' => $this->dumpDateTime ( $holiday ['startDate']->getDate (), false ),
												'ToDate' => $this->dumpDateTime ( $holiday ['endDate']->getDate (), false ) 
										) 
								) 
						);
					}
				}
				if (! empty ( $weekdays )) {
					$calendars [] = array (
							'Calendar' => array (
									'UID' => $resource ['user_id'],
									'Name' => html_entity_decode ( $resource ['resource_name'], ENT_COMPAT, "UTF-8" ),
									'IsBaseCalendar' => 0,
									'BaseCalendarUID' => 1,
									'WeekDays' => $weekdays 
							) 
					);
				}
			}
		}
		return $calendars;
	}
}
