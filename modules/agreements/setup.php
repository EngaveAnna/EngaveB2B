<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$config = array ();
$config ['mod_name'] = 'Agreements';
$config ['mod_version'] = '1.3.0';
$config ['mod_directory'] = 'agreements'; // tell apmProject where to find this module
$config ['mod_setup_class'] = 'CSetupAgreements'; // the name of the PHP setup class (used below)
$config ['mod_type'] = 'user'; // 'core' for modules distributed with apm by standard, 'user' for additional modules from dotmods
$config ['mod_ui_name'] = 'Agreements';
$config ['mod_ui_icon'] = 'communicate.gif';
$config ['mod_description'] = 'Agreements related to tasks and projects';
$config ['mod_config'] = false; // show 'configure' agreement in viewmods
$config ['mod_main_class'] = 'CAgreement';
$config ['permissions_item_table'] = 'agreements';
$config ['permissions_item_field'] = 'agreement_id';
$config ['permissions_item_label'] = 'agreement_name';

if ($a == 'setup') {
	echo apmshowModuleConfig ( $config );
}

/**
 * Class CSetupAgreements
 *
 * @package apmProject\modules\misc
 */
class CSetupAgreements extends apm_System_Setup {
	public function remove() {
		$q = $this->_getQuery ();
		$q->dropTable ( 'agreements' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->dropTable ( 'agreements_templates' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'AgreementCategory\'' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'AgreementTemplateCategory\'' );
		$q->exec ();
		return parent::remove ();
	}
	
	public function install() {
		$result = $this->_checkRequirements ();
		
		if (! $result) {
			return false;
		}
		
		$q = $this->_getQuery ();
		$q->createTable ( 'agreements' );
		/*Hidden creator create_date*/
		$q->createDefinition ( '(
			agreement_id int(11) NOT NULL AUTO_INCREMENT
			agreement_name varchar(255) NOT NULL DEFAULT "",
			agreement_date datetime DEFAULT NULL,
			agreement_place varchar(255) NOT NULL DEFAULT "",
			agreement_project text DEFAULT NULL,
			agreement_task text DEFAULT NULL,
			agreement_start_date datetime DEFAULT NULL,
			agreement_end_date datetime DEFAULT NULL,
			agreement_payment_type int(11) DEFAULT "0",
			agreement_payment_amount decimal(10,2) NOT NULL DEFAULT "0",
			agreement_parties_owner text DEFAULT NULL,
			agreement_parties_client text DEFAULT NULL,
			agreement_template int(11) DEFAULT NULL,
			agreement_source text,
			agreement_description text,
			agreement_category int(11) NOT NULL DEFAULT "0",
			agreement_owner int(11) DEFAULT "0",
			agreement_create_date datetime DEFAULT NULL,
			sign_src text DEFAULT NULL,
			sign_u text DEFAULT NULL,
			sign_ued text DEFAULT NULL,
            PRIMARY KEY ( agreement_id )) ENGINE = MYISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ' );
		if (! $q->exec ()) {
			return false;
		}


		$q = $this->_getQuery ();
		$q->createTable ( 'agreements_templates' );
		/*Hidden creator create_date*/
		$q->createDefinition ( '(
			template_id int(11) NOT NULL AUTO_INCREMENT,
			template_name varchar(255) NOT NULL DEFAULT "",
			template_date datetime DEFAULT NULL,
			template_owner int(11) DEFAULT "0",
			template_source text,
			template_description text,
			template_category int(11) NOT NULL DEFAULT "0",
            PRIMARY KEY ( template_id )
            ) ENGINE =MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8' );
		if (! $q->exec ()) {
			return false;
		}

		
		$i = 0;
		$agreementPaymentTypes = array (
			'perhour',
			'mounthly',
			'total'
		);
		foreach ( $agreementPaymentTypes as $agreementPaymentType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'AgreementPaymentType');
			$q->addInsert ( 'sysval_value', $agreementPaymentType);
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		$i = 0;
		$agreementTypes = array (
			'Draft',
			'Issue',
			'In signing',
			'Approve',
			'Archive'
		);
		foreach ( $agreementTypes as $agreementType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'AgreementCategory' );
			$q->addInsert ( 'sysval_value', $agreementType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		$i = 0;
		$agreementTemplatesTypes = array (
			'Private',
			'Public',
		);
		foreach ( $agreementTemplatesTypes as $agreementTemplateType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'AgreementTemplateCategory' );
			$q->addInsert ( 'sysval_value', $agreementTemplateType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		return parent::install ();
	}
}