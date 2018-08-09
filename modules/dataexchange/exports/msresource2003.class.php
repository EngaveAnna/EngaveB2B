<?php 
/* $Id$ $URL$ */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once "msobject.class.php";
class MSResource2003 extends MSObject {
	protected $resource = array ();
	public function __construct($exporter, &$resource) {
		parent::__construct ( $exporter );
		$this->resource = $resource;
	}
	public function dump() {
		$name = html_entity_decode ( $this->resource ['resource_name'], ENT_COMPAT, "UTF-8" );
		return array (
				
				// The unique ID for the resource.
				'UID' => $this->resource ['resource_id'],
				
				// The position identifier of the resource within the list of resources.
				'ID' => $this->resource ['id'],
				
				// The name of the resource; must be unique within the enterprise,
				// whether or not the resource is active.
				'Name' => $name,
				
				// The resource type (work or material). Values are: 0=Material, 1=Work
				'Type' => $this->resource ['resource_type'],
				
				// Indicates whether the resource is a null resource.
				'IsNull' => 0,
				
				// The initials of a resource name.
				'Initials' => $this->getInitials ( $name ),
				
				// Contains phonetic information in either Hiragana or Katakana for
				// resource names;
				// used only in the Japanese version of Microsoft Office Project 2003.
				'Phonetics' => "",
				
				// The Microsoft Windows NT Account name for a resource.
				'NTAccount' => "",
				
				// The unit of measurement entered for a material resource,
				// for example, tons, boxes, or cubic yards.
				'MaterialLabel' => "",
				
				// A code, abbreviation, or number entered as part of
				// a resource's information.
				'Code' => html_entity_decode ( $this->resource ['resource_code'], ENT_COMPAT, "UTF-8" ),
				
				// The name of the group the resource belongs to.
				'Group' => html_entity_decode ( $this->resource ['resource_group'], ENT_COMPAT, "UTF-8" ),
				
				// The messaging method used to communicate with a project team.
				// Values are: 0=Default, 1=None, 2=Email, 3=Web
				'WorkGroup' => 0,
				
				// The e-mail address of a resource.
				'EmailAddress' => $this->resource ['contact_email'],
				
				// The title or explanatory text for a hyperlink associated with a resource.
				'Hyperlink' => "",
				
				// The address for a hyperlink associated with a resource.
				'HyperlinkAddress' => $this->resource ['contact_url'],
				
				// The specific location in a document within a hyperlink associated
				// with a resource.
				'HyperlinkSubAddress' => "",
				
				// The maximum percentage, or number of units, that represents
				// the maximum amount that a resource is available to accomplish
				// any tasks during the current time period.
				'MaxUnits' => $this->resource ['resource_max_units'],
				
				// The maximum percentage, or number of units, that a resource
				// is assigned at any one time for all tasks assigned to that resource.
				'PeakUnits' => $this->resource ['resource_peak_units'],
				
				// Indicates whether a resource is assigned to do more work on all
				// assigned tasks than can be done within the resource's normal work capacity.
				'OverAllocated' => 0,
				
				// The starting date that a resource is available for work at
				// the units specified for the current time period.
				"AvailableFrom" => "",
				
				// The ending date that a resource will be available for work at
				// the units specified for the current time period.
				"AvailableTo" => "",
				
				// The date and time that a resource is scheduled to start work
				// on all assigned tasks.
				"Start" => "",
				
				// The date and time that a resource is scheduled to complete work
				// on all assigned tasks.
				"Finish" => "",
				
				// Indicates whether resource leveling can be performed with a resource.
				'CanLevel' => 1,
				
				// Indicates how and when resource standard and overtime costs are to be
				// charged, or accrued, to the cost of a task.
				// Values are: 1=Start, 2=End, 3=Prorated
				'AccrueAt' => 3,
				
				// The total amount of work scheduled to be performed by a resource
				// on all assigned tasks.
				'Work' => "PT0H0M0S",
				
				// The total amount of non-overtime work scheduled to be performed
				// for all assignments assigned to a resource.
				'RegularWork' => "PT0H0M0S",
				
				// The amount of overtime to be performed for all tasks assigned
				// to a resource and charged at the resource's overtime rate.
				'OvertimeWork' => "PT0H0M0S",
				
				// The actual amount of work that has already been done for
				// all assignments assigned to a resource.
				'ActualWork' => "PT0H0M0S",
				
				// The amount of time, or person-hours, still required by a resource to
				// complete all assigned tasks.
				'RemainingWork' => "PT0H0M0S",
				
				// The actual amount of overtime work already performed for all
				// assignments assigned to a resource.
				'ActualOvertimeWork' => "PT0H0M0S",
				
				// The remaining amount of overtime required by a resource
				// to complete all tasks.
				'RemainingOvertimeWork' => "PT0H0M0S",
				
				// The current status of all tasks assigned to a resource,
				// expressed as the total percentage of the resource's work
				// that has been completed.
				'PercentWorkComplete' => 0,
				
				// The rate of pay for regular, non-overtime work performed by a resource.
				'StandardRate' => 0,
				
				// The units used to display the standard rate.
				// Values are :
				// 1=m, 2=h, 3=d, 4=w, 5=mo, 7=y,
				// 8=material resource rate (or blank symbol specified)
				'StandardRateFormat' => 2,
				
				// The total scheduled cost for a resource for all assigned tasks,
				// based on costs already incurred for work performed by the resource on
				// all assigned tasks in addition to the costs planned
				// for all remaining work.
				'Cost' => 0,
				
				// The rate of pay for overtime work performed by a resource.
				'OvertimeRate' => 0,
				
				// The units used to display the overtime rate.
				// Values are : 1=m, 2=h, 3=d, 4=w, 5=mo, 7=y
				'OvertimeRateFormat' => 2,
				
				// The total overtime cost for a resource on all assigned tasks.
				'OvertimeCost' => 0,
				
				// The cost that accrues each time a resource is used.
				'CostPerUse' => 0,
				
				// The sum of costs incurred for the work already performed
				// by a resource for all assigned tasks.
				'ActualCost' => 0,
				
				// The cost incurred for overtime work already performed
				// by a resource for all assigned tasks.
				'ActualOvertimeCost' => 0,
				
				// The remaining scheduled expense that will be incurred in
				// completing the remaining work assigned to a resource.
				'RemainingCost' => 0,
				
				// The remaining scheduled overtime expense of a resource
				// that will be incurred in completing the remaining planned
				// overtime work by a resource on all assigned tasks.
				'RemainingOvertimeCost' => 0,
				
				// The difference between a resource's total baseline work
				// and the currently scheduled work.
				'WorkVariance' => 0,
				
				// The difference between the baseline cost and total cost for a resource.
				'CostVariance' => 0,
				
				// The difference in cost between the current progress
				// and the baseline plan of all the resource's assigned tasks up to the
				// status date or today's date; also called earned value schedule variance.
				'SV' => 0,
				
				// The difference between how much it should have cost for
				// the resource to achieve the current level of completion,
				// and how much it has actually cost to achieve the current
				// level of completion, up to the status date or today's date.
				'CV' => 0,
				
				// The sum of actual cost of work performed (ACWP) values for all
				// of a resource's assignments, up to the status date or today's date.
				'ACWP' => 0,
				
				// The unique ID for the calendar associated with this resource.
				'CalendarUID' => $this->resource ['resource_calendar_id'],
				
				// Notes about a resource.
				"Notes" => '<![CDATA[' . $this->resource ['resource_notes'] . ']]>',
				
				// The rolled-up summary of a resource's BCWS values for
				// all assigned tasks; also called budgeted cost of work scheduled.
				'BCWS' => 0,
				
				// The rolled-up summary of a resource's BCWP values for
				// all assigned tasks, calculated up to the status date or today's date;
				// also called budgeted cost of work performed.
				'BCWP' => 0,
				
				// Indicates whether the resource is an enterprise-level generic resource.
				'IsGeneric' => 0,
				
				// Indicates whether the resource is an active (enabled) or inactive user.
				'IsInactive' => $this->resource ['resource_inactive'],
				
				// Specifies whether the resource is an enterprise resource.
				'IsEnterprise' => 0,
				
				// Specifies the booking type of the resource.
				// values are : 1=Commited, 2=Proposed.
				"BookingType" => 1,
				
				// Specifies the duration through which actual work is protected.
				"ActualWorkProtected" => "PT0H0M0S",
				
				// Specifies the duration through which actual overtime work is protected.
				"ActualOvertimeWorkProtected" => "PT0H0M0S",
				
				// The Active Directory GUID for the resource.
				'ActiveDirectoryGUID' => "",
				
				// The date that the resource was created.
				'CreationDate' => $this->dumpDateTime ( $user ['contact_lastupdate'] ),
				
				// A set of custom fields definition associated with a resource.
				'#ExtendedAttributes' => $this->dumpObjectExtendedAttributeValues ( $this->resource ['contact_id'] ),
				
				// A set of project estimates used for tracking purposes.
				'#Baselines' => "",
				
				// A custom tag defined for a resource that enables
				// an alternate project structure.
				'#OutlineCodes' => "",
				
				// The collection of dates the resource is available.
				'AvailabilityPeriods' => "",
				
				// The collection of pay rates for the resource.
				'#Rates' => "",
				
				// The timephased data block associated with the resource.
				'#TimephasedDatas' => "" 
		)
		;
	}
	protected function dumpObjectExtendedAttributeValues($object_id) {
		$extendedattributevalues = array ();
		$values = $this->getObjectExtendedAttributeValues ( 'contacts', $object_id );
		foreach ( $values as $value ) {
			if ($value ['value_charvalue']) {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'UID' => $value ['value_id'],
								'FieldID' => $value ['value_field_id'],
								'Value' => $value ['value_charvalue'] 
						) 
				);
			} elseif ($value ['value_intvalue'] > 0 && $value ['field_htmltype'] == 'select') {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'UID' => $value ['value_id'],
								'FieldID' => $value ['value_field_id'],
								'ValueID' => $value ['value_intvalue'] 
						) 
				);
			} else {
				$extendedattributevalues [] = array (
						'ExtendedAttribute' => array (
								'UID' => $value ['value_id'],
								'FieldID' => $value ['value_field_id'],
								'Value' => $value ['value_intvalue'] 
						) 
				);
			}
		}
		return $extendedattributevalues;
	}
	private function getInitials($name) {
		$initials = "";
		$parts = preg_split ( "/[- ]/", $name, - 1, PREG_SPLIT_DELIM_CAPTURE );
		foreach ( $parts as $part ) {
			if ($part != " ") {
				$initials .= strtoupper ( $part {0} );
			}
		}
		return $initials;
	}
}
