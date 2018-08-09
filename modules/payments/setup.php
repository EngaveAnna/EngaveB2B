<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$config = array ();
$config ['mod_name'] = 'Payments';
$config ['mod_version'] = '2.1.8';
$config ['mod_directory'] = 'payments'; // tell apmProject where to find this module
$config ['mod_setup_class'] = 'CSetupPayments'; // the name of the PHP setup class (used below)
$config ['mod_type'] = 'user'; // 'core' for modules distributed with apm by standard, 'user' for additional modules from dotmods
$config ['mod_ui_name'] = 'Payments';
$config ['mod_ui_icon'] = 'communicate.gif';
$config ['mod_description'] = 'Payments related to tasks and projects';
$config ['mod_config'] = false; // show 'configure' payment in viewmods
$config ['mod_main_class'] = 'CPayment';
$config ['permissions_item_table'] = 'payments';
$config ['permissions_item_field'] = 'payment_id';
$config ['permissions_item_label'] = 'payment_name';

if ($a == 'setup') {
	echo apmshowModuleConfig ( $config );
}

/**
 * Class CSetupPayments
 *
 * @package apmProject\modules\misc
 */
class CSetupPayments extends apm_System_Setup {
	public function remove() {
		$q = $this->_getQuery ();
		$q->dropTable ( 'payments' );
		$q->exec ();
		
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'PaymentType\'' );
		$q->exec ();
		
		return parent::remove ();
	}
	public function install() {
		$result = $this->_checkRequirements ();
		
		if (! $result) {
			return false;
		}
		
		$q = $this->_getQuery ();
		$q->createTable ( 'payments' );
		$q->createDefinition ( '(

		payment_id int(11) NOT NULL AUTO_INCREMENT,
		payment_name varchar(255) NOT NULL DEFAULT "",
		payment_owner int(11) DEFAULT "0",
		payment_order int(11) DEFAULT NULL,
		payment_amount decimal(9,2) DEFAULT NULL,
		payment_date datetime DEFAULT NULL,
		payment_type int(11) NOT NULL DEFAULT "0",
		payment_result text,
		payment_category int(11) NOT NULL DEFAULT "0",		
		payment_description text,


        PRIMARY KEY ( payment_id )) ENGINE = MYISAM DEFAULT CHARSET=utf8 ' );
		
		if (! $q->exec ()) {
			return false;
		}
		
		$i = 0;
		
		$paymentStatuses = array (
				'In progress',
				'To pay',
				'Paid in full',
				'Underpayment',
				'Excess payment',
				'Payment error',
				'Canceled by the user',
	
		);
		
		// TODO: refactor as proper sysvals handling
		foreach ( $paymentStatuses as $paymentStatus ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'PaymentStatus' );
			$q->addInsert ( 'sysval_value', $paymentStatus );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		$paymentTypes = array (
				'Deposit',
				'Payment funds',
		);

		foreach ( $paymentTypes as $paymentType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'PaymentType' );
			$q->addInsert ( 'sysval_value', $paymentType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}		
		
		return parent::install ();
	}
}