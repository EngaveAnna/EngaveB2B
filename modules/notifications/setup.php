<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$config = array ();
$config ['mod_name'] = 'Notifications';
$config ['mod_version'] = '1.3.0';
$config ['mod_directory'] = 'notifications'; // tell apmProject where to find this module
$config ['mod_setup_class'] = 'CSetupNotifications'; // the name of the PHP setup class (used below)
$config ['mod_type'] = 'user'; // 'core' for modules distributed with apm by standard, 'user' for additional modules from dotmods
$config ['mod_ui_name'] = 'Notifications';
$config ['mod_ui_icon'] = 'communicate.gif';
$config ['mod_description'] = 'Notifications related to tasks and projects';
$config ['mod_config'] = false; // show 'configure' notification in viewmods
$config ['mod_main_class'] = 'CNotification';
$config ['permissions_item_table'] = 'notifications';
$config ['permissions_item_field'] = 'notification_id';
$config ['permissions_item_label'] = 'notification_name';

if ($a == 'setup') {
	echo apmshowModuleConfig ( $config );
}

/**
 * Class CSetupNotifications
 *
 * @package apmProject\modules\misc
 */
class CSetupNotifications extends apm_System_Setup {
	public function remove() {
		$q = $this->_getQuery ();
		$q->dropTable ( 'notifications' );
		$q->exec ();
		
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'NotificationType\'' );
		$q->exec ();
		
		return parent::remove ();
	}
	public function install() {
		$result = $this->_checkRequirements ();
		
		if (! $result) {
			return false;
		}
		
		$q = $this->_getQuery ();
		$q->createTable ( 'notifications' );
		$q->createDefinition ( '(
				
			notification_id int(11) NOT NULL,

			notification_project int(11) NOT NULL DEFAULT "0",
			notification_task int(11) NOT NULL DEFAULT "0",
			notification_name varchar(255) NOT NULL DEFAULT "",
			notification_parent int(11) DEFAULT "0",
			notification_description text,
			notification_owner int(11) DEFAULT "0",
			notification_date datetime DEFAULT NULL,
			notification_category int(11) NOT NULL DEFAULT "0",
			notification_status int(10) DEFAULT "0",
			notification_priority tinyint(4) DEFAULT "0",

            PRIMARY KEY ( notification_id ) ,
            KEY idx_notification_task ( notification_task ) ,
            KEY idx_notification_project ( notification_project ) ,
            KEY idx_notification_parent ( notification_parent )
            ) ENGINE = MYISAM DEFAULT CHARSET=utf8 ' );
		if (! $q->exec ()) {
			return false;
		}
		
		$i = 0;
		$notificationTypes = array (
				'Bug',
				'Opinion',
				'Proposal',
		);
		
		// TODO: refactor as proper sysvals handling
		foreach ( $notificationTypes as $notificationType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'NotificationType' );
			$q->addInsert ( 'sysval_value', $notificationType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		return parent::install ();
	}
}