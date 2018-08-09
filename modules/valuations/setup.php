<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$config = array ();
$config ['mod_name'] = 'Valuations';
$config ['mod_version'] = '4.1.0';
$config ['mod_directory'] = 'valuations'; // tell apmProject where to find this module
$config ['mod_setup_class'] = 'CSetupValuations'; // the name of the PHP setup class (used below)
$config ['mod_type'] = 'user'; // 'core' for modules distributed with apm by standard, 'user' for additional modules from dotmods
$config ['mod_ui_name'] = 'Valuations';
$config ['mod_ui_icon'] = '';
$config ['mod_description'] = 'Valuations related to tasks and projects';
$config ['mod_config'] = false; // show 'configure' valuation in viewmods
$config ['mod_main_class'] = 'CValuation';
$config ['permissions_item_table'] = 'valuations';
$config ['permissions_item_field'] = 'valuation_id';
$config ['permissions_item_label'] = 'valuation_name';

if ($a == 'setup') {
	echo apmshowModuleConfig ( $config );
}

/**
 * Class CSetupValuations
 *
 * @package apmProject\modules\misc
 */
class CSetupValuations extends apm_System_Setup {
	public function remove() {
		$q = $this->_getQuery ();
		$q->dropTable ( 'valuations' );
		$q->exec ();
		
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'ValuationType\'' );
		$q->exec ();
		
		return parent::remove ();
	}
	public function install() {
		$result = $this->_checkRequirements ();
		
		if (! $result) {
			return false;
		}
		
		$q = $this->_getQuery ();
		$q->createTable ( 'valuations' );
		$q->createDefinition ( '(

		valuation_id int(11) NOT NULL AUTO_INCREMENT,
		valuation_create_date datetime DEFAULT NULL,
		valuation_name varchar(255) NOT NULL DEFAULT "",
		valuation_category int(11) NOT NULL DEFAULT "0",
		valuation_owner int(11) DEFAULT "0",
		valuation_desc text,
		valuation_date datetime DEFAULT NULL,
		valuation_project int(11) DEFAULT NULL,
		valuation_days decimal(9,2) DEFAULT NULL,
		valuation_real_days decimal(9,2) DEFAULT NULL,
		valuation_amount decimal(9,2) DEFAULT NULL,
		valuation_real_amount decimal(9,2) DEFAULT NULL,
        PRIMARY KEY ( valuation_id )) ENGINE = MYISAM DEFAULT CHARSET=utf8 ' );
		
		if (! $q->exec ()) {
			return false;
		}
		
		$i = 0;
		
		$valuationTypes = array (
				'Order',
				'Valuation',
		);

		foreach ( $valuationTypes as $valuationType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'ValuationType' );
			$q->addInsert ( 'sysval_value', $valuationType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}		
		
		return parent::install ();
	}
}