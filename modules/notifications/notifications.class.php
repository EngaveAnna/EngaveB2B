<?php
/**
 * @package     apmProject\modules\misc
 */
class CNotification extends apm_Core_BaseObject {
	public $notification_id = null;
	public $notification_project = null;
	public $notification_task = null;
	public $notification_name = null;
	public $notification_parent = null;
	public $notification_description = null;
	public $notification_owner = null;
	// @todo this should be notification_datetime to take advantage of our templating
	public $notification_date = null;
	public $notification_status = null;
	public $notification_icon = null;
	public $notification_category = null;
	public $notification_priority = null;
	public function __construct() {
		parent::__construct ( 'notifications', 'notification_id' );
	}
	public function getProjectTaskNotificationsByCategory($notUsed = null, $project_id = 0, $task_id = 0, $category_id = 0, $search = '') {
		// load the following classes to retrieved denied records
		$project = new CProject ();
		$project->overrideDatabase ( $this->_query );
		$task = new CTask ();
		$task->overrideDatabase ( $this->_query );
		
		// SETUP FOR LINK LIST
		$q = $this->_getQuery ();
		$q->addQuery ( 'notifications.*' );
		$q->addTable ( 'notifications' );
		
		$q->leftJoin ( 'projects', 'pr', 'project_id = notification_project' );
		$q->leftJoin ( 'tasks', 't', 'task_id = notification_task' );
		
		if ($search != '') {
			$q->addWhere ( '(notification_name LIKE \'%' . $search . '%\' OR notification_description LIKE \'%' . $search . '%\')' );
		}
		if ($project_id > 0) { // Project
			$q->addQuery ( 'project_name, project_color_identifier, project_status' );
			$q->addWhere ( 'notification_project = ' . ( int ) $project_id );
		}
		if ($task_id > 0) { // Task
			$q->addQuery ( 'task_name, task_id' );
			$q->addWhere ( 'notification_task = ' . ( int ) $task_id );
		}
		if ($category_id >= 0) { // Category
			$q->addWhere ( 'notification_category = ' . $category_id );
		}
		// Permissions
		$q = $project->setAllowedSQL ( $this->_AppUI->user_id, $q, 'notification_project' );
		$q = $task->setAllowedSQL ( $this->_AppUI->user_id, $q, 'notification_task and task_project = notification_project' );
		$q->addOrder ( 'project_name, notification_name' );
		
		return $q->loadList ();
	}
	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->notification_name )) {
			$this->_error ['notification_name'] = $baseErrorMsg . 'notification name is not set';
		}
		
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		$this->notification_date = $q->dbfnNowWithTZ ();
		$this->notification_owner = ( int ) $this->notification_owner ? $this->notification_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'notifications';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'notifications';
		$search ['table_key'] = 'notification_id'; // primary key in searched table
		$search ['table_notification'] = 'index.php?m=notifications&a=addedit&notification_id='; // first part of notification
		$search ['table_title'] = 'Notifications';
		$search ['table_orderby'] = 'notification_name';
		$search ['search_fields'] = array (
				'l.notification_name',
				'l.notification_description' 
		);
		$search ['display_fields'] = $search ['search_fields'];
		
		return $search;
	}
}
