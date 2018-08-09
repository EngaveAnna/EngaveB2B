<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "msobject.class.php";
class MSAssignment2003 extends MSObject {
	protected $assignment = array ();
	public function __construct($exporter, &$assignment) {
		parent::__construct ( $exporter );
		$this->assignment = $assignment;
	}
	public function dump() {
		return array (
				
				// The unique ID for the assignment.
				"UID" => $this->assignment ['uid'],
				
				// The unique ID for the task.
				"TaskUID" => $this->assignment ['task_id'],
				
				// The unique ID for the resource.
				"ResourceUID" => $this->assignment ['resource_id'],
				
				// The current status of the assignment,
				// expressed as the percentage of the assignment's work
				// that has been completed.
				"PercentWorkComplete" => 0,
				
				// The cost incurred for work already performed
				// by a resource on a task.
				"ActualCost" => 0,
				
				// The date and time when the assignment was actually completed.
				"ActualFinish" => "",
				
				// The cost incurred for overtime work already performed by a resource
				// on a task.
				"ActualOvertimeCost" => 0,
				
				// The actual amount of overtime work already performed by a resource
				// on the assigned task.
				"ActualOvertimeWork" => "PT0H0M0S",
				
				// The date and time that the assignment actually began.
				"ActualStart" => $this->dumpDateTime ( $this->assignment ['task_start_date'] ),
				
				// The costs incurred for work already performed by a resource
				// on a task up to the project status date or today's date;
				// also called actual cost of work performed.
				"ACWP" => 0,
				
				// Indicates whether a resource assigned to a task has accepted
				// or rejected the task assignment.
				"Confirmed" => 1,
				
				// The total scheduled (or projected) cost for the assignment.
				"Cost" => 0,
				
				// Indicates which cost rate table to use for a resource on the assignment.
				"CostRateTable" => 0,
				
				// The difference between the baseline cost
				// and total cost for the assignment.
				"CostVariance" => 0,
				
				// The difference between how much it should have cost to achieve
				// the current level of completion on the assignment
				// and how much it has actually cost.
				"CV" => 0,
				
				// The amount of time a resource is to wait
				// after the task start date before starting work on the assignment.
				"Delay" => 0,
				
				// The date and time that the assigned resource is scheduled
				// to complete work on a task.
				"Finish" => $this->dumpDateTime ( $this->assignment ['task_end_date'] ),
				
				// The difference between the assignment's baseline finish date
				// and its scheduled finish date.
				"FinishVariance" => 0,
				
				// The title or explanatory text for a hyperlink
				// associated with the assignment.
				'Hyperlink' => '',
				
				// The address for a hyperlink associated with the assignment.
				'HyperlinkAddress' => '',
				
				// The specific location in a document within a hyperlink
				// associated with the assignment.
				'HyperlinkSubAddress' => '',
				
				// The difference between the assignment's baseline work
				// and the currently scheduled work.
				"WorkVariance" => 0,
				
				// Indicates whether the assignment has fixed rate units.
				"HasFixedRateUnits" => 1,
				
				// Indicates whether the consumption of the assigned material resource
				// occurs in a single, fixed amount.
				"FixedMaterial" => 0,
				
				// The amount of time that the assignment is to be delayed from
				// the scheduled start date as a result of resource leveling.
				"LevelingDelay" => 0,
				
				// The format for expressing the duration of the delay.
				"LevelingDelayFormat" => 5,
				
				// Indicates whether there are OLE links to the assignment.
				"LinkedFields" => 0,
				
				// Indicates whether the assignment task is a milestone.
				"Milestone" => $this->assignment ['task_milestone'],
				
				// Notes about the assignment.
				"Notes" => "",
				
				// Indicates whether a resource is assigned to more work
				// on a specific task than can be done within the
				// resource's normal working capacity.
				"Overallocated" => 0,
				
				// The total overtime cost for a resource assignment.
				"OvertimeCost" => 0,
				
				// The amount of overtime to be performed by a resource on a task;
				// charged at the resource's overtime rate.
				"OvertimeWork" => "PT0H0M0S",
				
				// The total amount of non-overtime work scheduled to be performed
				// by a resource assigned to a task.
				"RegularWork" => "PT0H0M0S",
				
				// The costs associated with completing all remaining scheduled work
				// by any resources on a specific task.
				"RemainingCost" => 0,
				
				// The remaining scheduled overtime expense for the assignment.
				"RemainingOvertimeCost" => 0,
				
				// The amount of overtime work that remains on the assignment.
				"RemainingOvertimeWork" => "PT0H0M0S",
				
				// The amount of time required by a resource assigned to a task
				// to complete the assignment.
				"RemainingWork" => "PT0H0M0S",
				
				// Indicates whether an answer has been received from a message sent to
				// the resource assigned to the task
				// notifying the resource of the assignment.
				"ResponsePending" => 0,
				
				// The date and time that the assigned resource is scheduled
				// to begin working on the task.
				"Start" => $this->dumpDateTime ( $this->assignment ['task_start_date'] ),
				
				// The date the assignment was stopped.
				"Stop" => "",
				
				// The date the assignment was resumed.
				"Resume" => "",
				
				// The difference between the assignment's baseline start date
				// and its currently scheduled start date.
				"StartVariance" => 0,
				
				// The number of units for which the resource is assigned to the task,
				// expressed as a percentage.
				"Units" => $this->assignment ['perc_assignment'] / 100,
				
				// Indicates whether a TeamUpdate message should be sent
				// to the resource assigned to a task because of changes to
				// the start date, finish date, or resource reassignments.
				"UpdateNeeded" => 0,
				
				// The variance at completion (VAC) between the baseline cost
				// and the total cost for the assignment on the task.
				"VAC" => 0,
				
				// The total amount of work scheduled to be performed by the resource
				// on the task.
				"Work" => "PT0H0M0S",
				
				// Indicates how work for the assignment is to be distributed across
				// the duration of the assignment.
				"WorkContour" => 0,
				
				// The budgeted cost of work scheduled on the assignment.
				"BCWS" => 0,
				
				// The budgeted cost of the work performed on the assignment to-date.
				"BCWP" => 0,
				
				// Specifies the booking type of the assignment (committed or proposed).
				"BookingType" => 0,
				
				// Specifies the duration through which actual work is protected.
				"ActualWorkProtected" => "PT0H0M0S",
				
				// Specifies the duration through which actual overtime work is protected.
				"ActualOvertimeWorkProtected" => "PT0H0M0S",
				
				// The date that the assignment was created.
				"CreationDate" => $this->dumpDateTime ( array (
						$this->assignment ['task_created'],
						$this->assignment ['task_updated'] 
				) ),
				
				// The value of extended attributes (custom fields).
				"#ExtendedAttributes" => "",
				
				// The collection of baseline values associated with the assignment.
				"#Baselines" => "",
				
				// The time phased data associated with the assignment.
				"#TimephasedDatas" => "" 
		)
		;
	}
}
