<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "mstask2003.class.php";
class MSTask2007 extends MSTask2003 {
	public function dump() {
		$taskproperties = array (
				
				// Unique identifier of the task
				'UID' => $this->task ['task_id'],
				
				// The position identifier of the task in the list of tasks.
				'ID' => $this->task ['id'],
				
				// The name of the task.
				'Name' => html_entity_decode ( $this->task ['task_name'], ENT_QUOTES, "UTF-8" ),
				
				// The type of task. Values are: 0=Fixed Units, 1=Fixed Duration, 2=Fixed Work.
				'Type' => 1,
				
				// Indicates whether a task is null.
				'IsNull' => 0,
				
				// The date and time that a task was added to the project.
				'CreateDate' => $this->dumpDateTime ( array (
						$this->task ['task_created'],
						$this->task ['task_start_date'] 
				) ),
				
				// The name of the individual who is responsible for a task.
				'Contact' => html_entity_decode ( $this->task ['task_owner_contact'], ENT_QUOTES, "UTF-8" ),
				
				// A unique code (WBS) used to represent a task's position
				// within the hierarchical structure of the project.
				'WBS' => $this->task ['wbs'],
				
				// The right-most level of the task.
				// For example, if the task level was A.01.03, the right-most level would be 03.
				'WBSLevel' => $this->task ['wbs_level'],
				
				// The number that indicates the level of a task in the project outline hierarchy.
				'OutlineNumber' => $this->task ['outline_number'],
				
				// Indicates the exact position of a task in the outline.
				'OutlineLevel' => $this->task ['outline_level'],
				
				// Indicates the level of importance assigned to a task (from 0 to 1000).
				'Priority' => $this->task ['task_priority'],
				
				// The date and time that a task is scheduled to begin.
				'Start' => $this->dumpDateTime ( $this->task ['task_start_date'] ),
				
				// The date and time that a task is scheduled to be completed.
				'Finish' => $this->dumpDateTime ( $this->task ['task_end_date'] ),
				
				// The planned duration of the task.
				'Duration' => $this->dumpDurationDataTypeForTask ( $this->task ['task_duration'] ),
				
				// The format used to show the duration of the task.
				// Values are:
				// 3=m, 4=em, 5=h, 6=eh, 7=d, 8=ed, 9=w, 10=ew, 11=mo, 12=emo,
				// 19=%, 20=e%, 21=null, 35=m?, 36=em?, 37=h?, 38=eh?, 39=d?, 40=ed?, 41=w?,
				// 42=ew?, 43=mo?, 44=emo?, 51=%?, 52=e%? and 53=null
				'DurationFormat' => $this->dumpTaskDurationFormat (),
				
				// The total amount of work scheduled to be performed on a task
				// by all assigned resources.
				'Work' => $this->dumpDurationDataTypeForTask ( $this->task ['task_work'] ),
				
				// The date that represents the end of the actual portion of a task.
				'Stop' => $this->dumpDateTime ( array (
						$this->task ['task_actual_end_date'],
						$this->task ['task_end_date'] 
				) ),
				
				// The date the remaining portion of a task is scheduled to resume.
				'Resume' => '',
				
				// Indicates whether the task can be resumed.
				'ResumeValid' => 0,
				
				// Indicates whether scheduling for a task is effort-driven.
				'EffortDriven' => 1,
				
				// Indicates whether a task is a recurring task.
				'Recurring' => 0,
				
				// Indicates whether an assigned resource on a task has been assigned
				// to more work on the task than can be done within the normal working capacity.
				'OverAllocated' => 0,
				
				// Indicates whether the task's duration is flagged as an estimate.
				'Estimated' => 1,
				
				// Indicates whether a task is a milestone.
				'Milestone' => $this->task ['task_milestone'],
				
				// Indicates whether a task is a summary task.
				'Summary' => $this->task ['task_summary'],
				
				// Indicates whether a task has room in the schedule to slip,
				// or if it is on the critical path.
				'Critical' => $this->task ['critical_task'] ? 1 : 0,
				
				// Indicates whether the task is an inserted project.
				'IsSubproject' => $this->task ['task_represents_project'] ? 1 : 0,
				
				// Indicates whether the inserted project is a read-only project.
				'IsSubprojectReadOnly' => 0,
				
				// The source location of the inserted project.
				'SubprojectName' => '',
				
				// Indicates whether the task is linked from another project
				// or whether it originated in the current project.
				'ExternalTask' => 0,
				
				// The source of an external task.
				'ExternalTaskProject' => '',
				
				// The earliest date that a task could possibly begin,
				// based on the early start dates of predecessor
				// and successor tasks and other constraints.
				'EarlyStart' => $this->dumpDateTime ( $this->task ['task_start_date'] ),
				
				// The earliest date that a task could possibly finish,
				// based on early finish dates of predecessor and successor tasks,
				// other constraints, and any leveling delay.
				'EarlyFinish' => $this->dumpDateTime ( $this->task ['task_end_date'] ),
				
				// The latest date that a task can start
				// without delaying the finish of the project.
				'LateStart' => $this->dumpDateTime ( $this->task ['task_start_date'] ),
				
				// The latest date that a task can finish
				// without delaying the finish of the project.
				'LateFinish' => $this->dumpDateTime ( $this->task ['task_end_date'] ),
				
				// The difference between a task's baseline start date
				// and its currently scheduled start date (as minutes x 1000).
				'StartVariance' => 0,
				
				// The amount of time that represents the difference between
				// a task's baseline finish date and its current finish date
				// (as minutes x 1000).
				'FinishVariance' => 0,
				
				// The difference between a task's baseline work
				// and the currently scheduled work (as minutes x 1000).
				'WorkVariance' => 0,
				
				// The amount of time that a task can be delayed
				// without delaying any successor tasks.
				'FreeSlack' => 0,
				
				// The amount of time a task can be delayed
				// without delaying a project's finish date.
				'TotalSlack' => 0,
				
				// A task expense that is not associated with a resource cost.
				'FixedCost' => 0,
				
				// Indicates how fixed costs are to be charged,
				// or accrued, to the cost of a task.
				'FixedCostAccrual' => 3,
				
				// The current status of a task, expressed as the percentage of the
				// task's duration that has been completed.
				'PercentComplete' => $this->task ['task_percent_complete'],
				
				// The current status of a task,
				// expressed as the percentage of the task's work that has been completed.
				'PercentWorkComplete' => $this->task ['task_percent_complete'],
				
				// The total scheduled, or projected, cost for a task.
				'Cost' => $this->task ['task_target_budget'],
				
				// The sum of the actual overtime cost for the task.
				'OvertimeCost' => 0,
				
				// The amount of overtime scheduled to be performed by all resources
				// assigned to a task and charged at overtime rates.
				'OvertimeWork' => $this->dumpDurationDataTypeForTask ( $this->task ['task_overtime_work'] ),
				
				// The date and time that a task actually began.
				'ActualStart' => $this->dumpDateTime ( $this->task ['task_start_date'] ),
				
				// The date and time that a task actually finished.
				'ActualFinish' => $this->dumpDateTime ( $this->task ['task_end_date'] ),
				
				// The span of actual working time for a task so far,
				// based on the scheduled duration and current remaining work
				// or percent complete.
				'ActualDuration' => $this->dumpDurationDataTypeForTask ( $this->task ['task_actual_duration'] ),
				
				// The costs incurred for work already performed by all resources on a task,
				// along with any other recorded costs associated with the task.
				'ActualCost' => 0,
				
				// The costs incurred for overtime work already performed on
				// a task by all assigned resources.
				'ActualOvertimeCost' => 0,
				
				// The amount of work that has already been done by the resources assigned to a task.
				'ActualWork' => $this->dumpDurationDataTypeForTask ( $this->task ['task_hours_worked'] ),
				
				// The actual amount of overtime work already performed
				// by all resources assigned to a task.
				'ActualOvertimeWork' => 'PT0H0M0S',
				
				// The total amount of non-overtime work scheduled
				// to be performed by all resources assigned to a task.
				'RegularWork' => $this->dumpDurationDataTypeForTask ( $this->task ['task_regular_work'] ),
				
				// The amount of time required to complete the unfinished portion of the task.
				'RemainingDuration' => $this->dumpDurationDataTypeForTask ( $this->task ['task_remaining_duration'] ),
				
				// The remaining scheduled expense of a task that will be incurred
				// in completing the remaining scheduled work by all resources assigned to a task.
				'RemainingCost' => 0,
				
				// The remaining work scheduled to complete the task.
				'RemainingWork' => $this->dumpDurationDataTypeForTask ( $this->task ['task_work'] - $this->task ['task_hours_worked'] ),
				
				// The remaining scheduled overtime expense for a task.
				'RemainingOvertimeCost' => 0,
				
				// The amount of remaining overtime scheduled
				// by all assigned resources to complete a task.
				'RemainingOvertimeWork' => 'PT0H0M0S',
				
				// The costs incurred for work already done on a task,
				// up to the project status date or today's date.
				'ACWP' => 0,
				
				// The difference between how much it should have cost
				// to achieve the current level of completion on the task
				// and how much it has actually cost.
				'CV' => 0,
				
				// The constraint on the start or finish date of the task.
				// Values are:
				// 0=As Soon As Possible, 1=As Late As Possible, 2=Must Start On,
				// 3=Must Finish On, 4=Start No Earlier Than, 5=Start No Later Than,
				// 6=Finish No Earlier Than and 7=Finish No Later Than
				'ConstraintType' => 0,
				
				// Refers to a valid UID (unique ID) for a calendar used in the project.
				'CalendarUID' => 1,
				
				// Indicates the constrained start or finish date as defined
				// in the task <ConstraintType>.
				// Required unless the constraint type is set to As late as possible
				// or As soon as possible.
				'ConstraintDate' => '',
				
				// The date entered as a deadline for the task.
				'Deadline' => '',
				
				// Indicates whether the leveling function can delay
				// and split individual assignments (rather than the entire task)
				// to resolve overallocations.
				'LevelAssignments' => 1,
				
				// Indicates whether the resource leveling function can cause splits
				// on remaining work on a task.
				'LevelingCanSplit' => 1,
				
				// The amount of time that a task is to be delayed from its early start date
				// as a result of resource leveling.
				'LevelingDelay' => 0,
				
				// The format for expressing the duration of the delay.
				// Values are:
				// 3=m, 4=em, 5=h, 6=eh, 7=d, 8=ed, 9=w, 10=ew, 11=mo, 12=emo, 19=%, 20=e%,
				// 21=null, 35=m?, 36=em?, 37=h?, 38=eh?, 39=d?, 40=ed?, 41=w?, 42=ew?,
				// 43=mo?, 44=emo?, 51=%?, 52=e%? and 53=null.
				'LevelingDelayFormat' => 8,
				
				// The start date of a task as it was before resource leveling was done.
				'PreLeveledStart' => $this->dumpDateTime ( $this->task ['task_start_date'] ),
				
				// The finish date of a task as it was before resource leveling was done.
				'PreLeveledFinish' => $this->dumpDateTime ( $this->task ['task_end_date'] ),
				
				// The title or explanatory text for a hyperlink associated with a task.
				'Hyperlink' => '',
				
				// The address for a hyperlink associated with a task.
				'HyperlinkAddress' => $this->task ['task_related_url'],
				
				// The specific location in a document within a hyperlink associated with a task.
				'HyperlinkSubAddress' => '',
				
				// Indicates whether the scheduling of the task takes into account
				// the calendars of the resources assigned to the task.
				'IgnoreResourceCalendar' => 0,
				
				// Notes entered about a task
				'Notes' => '<![CDATA[' . html_entity_decode ( $this->task ['task_description'], ENT_QUOTES, "UTF-8" ) . ']]>',
				
				// Indicates whether the Gantt bars and Calendar bars for a task are hidden.
				'HideBar' => 0,
				
				// Indicates whether the summary task bar displays rolled-up bars
				// or whether information on the subtask Gantt bars will be rolled up
				// to the summary task bar.
				// <Rollup> must be set to True for subtasks to be rolled up to summary tasks.
				'Rollup' => ($this->task ['task_id'] == $this->task ['task_parent']) ? 0 : 1,
				
				// BCWS is the Budgeted Cost of Work Scheduled.
				// The cumulative timephased baseline costs up to the status date
				// or today's date; also known as budgeted cost of work scheduled.
				'BCWS' => 0,
				
				// BCWP is the Budgeted Cost of Work Performed.
				// The cumulative value of the task's timephased percent complete multiplied by
				// the task's timephased baseline cost, up to the status date or today's date;
				// also known as budgeted cost of work performed.
				'BCWP' => 0,
				
				// The physical percent of the total work on a task that has been completed.
				// <PhysicalPercentComplete> can be used as an alternative for
				// calculating <BCWP> (the budgeted cost of work performed).
				'PhysicalPercentComplete' => 0,
				
				// The method for calculating earned value.
				// Values are: 0=Percent Complete, 1=Physical Percent Complete
				'EarnedValueMethod' => 0,
				
				// Defines the predecessor tasks on which this task depends
				// for its start or finish date.
				'#PredecessorLinks' => $this->dumpPredecessorLinks (),
				
				// Specifies the duration through which actual work is protected.
				'ActualWorkProtected' => '',
				
				// Specifies the duration through which actual overtime work is protected.
				// 'ActualOvertimeWorkProtected' => '',
				
				// The value of an extended attribute.
				// Two pieces of data are necessary - a pointer back to the extended
				// attribute table which is specified either by the unique ID or the Field ID,
				// and the value which is specified either with the value,
				// or a pointer back to the value list.
				'#ExtendedAttributes' => $this->dumpObjectExtendedAttributeValues ( $this->task ['task_id'] ),
				
				// The collection of baseline values associated with the task
				'#Baselines' => '',
				
				// The collection of outline codes.
				// Two pieces of data are necessary - a pointer back to the outline code table
				// which is specified either by the unique ID or the Field ID,
				// and the value which is specified either with the value,
				// or a pointer back to the value list.
				'#OutlineCodes' => '',
				
				// Whether the task is published.
				'IsPublished' => 1,
				
				// The Status Manager field contains the name of the enterprise resource
				// who is to receive status updates for the current task from resources working
				// in Microsoft Office Project Web Access.
				// By default, the status manager is the user who originally published the new task
				'StatusManager' => html_entity_decode ( $this->task ['task_owner_contact'], ENT_QUOTES, "UTF-8" ),
				
				// The start date of the deliverable.
				'CommitmentStart' => '',
				
				// The finish date of the deliverable.
				'CommitmentFinish' => '',
				
				// Whether the task has an associated deliverable
				// or a dependency on an associated deliverable.
				// Values are:
				// 0=The task has no deliverable or dependency on a deliverable,
				// 1=The task has an associated deliverable,
				// 2=The task has a dependency on an associated deliverable.
				'CommitmentType' => 0,
				
				// The time phased data blocks associated with the task.
				'#TimephasedDatas' => $this->dumpTaskLogs () 
		)
		;
		if ($this->task ['task_represents_project'] > 0) {
			$exporterclass = get_class ( $this->exporter );
			$exporter = new $exporterclass ();
			$taskproperties ['Project xmlns="http://schemas.microsoft.com/project/2007"'] = $exporter->exportProject ( $this->task ['task_represents_project'] );
		}
		return $taskproperties;
	}
	protected function dumpObjectExtendedAttributeValues($object_id) {
		$extendedattributevalues = array ();
		$values = $this->getObjectExtendedAttributeValues ( 'tasks', $object_id );
		foreach ( $values as $value ) {
			if ($value ['value_charvalue']) {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'FieldID' => $value ['value_field_id'],
								'Value' => $value ['value_charvalue'] 
						) 
				);
			} elseif ($value ['value_intvalue'] > 0 && $value ['field_htmltype'] == 'select') {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'FieldID' => $value ['value_field_id'],
								'ValueID' => $value ['value_intvalue'] 
						) 
				);
			} else {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'FieldID' => $value ['value_field_id'],
								'Value' => $value ['value_intvalue'] 
						) 
				);
			}
		}
		return $extendedattributevalues;
	}
}
