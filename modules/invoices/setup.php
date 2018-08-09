<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$config = array ();
$config ['mod_name'] = 'Invoices';
$config ['mod_version'] = '2.4.1';
$config ['mod_directory'] = 'invoices'; // tell apmProject where to find this module
$config ['mod_setup_class'] = 'CSetupInvoices'; // the name of the PHP setup class (used below)
$config ['mod_type'] = 'user'; // 'core' for modules distributed with apm by standard, 'user' for additional modules from dotmods
$config ['mod_ui_name'] = 'Invoices';
$config ['mod_ui_icon'] = 'communicate.gif';
$config ['mod_description'] = 'Invoices to logs and projects';
$config ['mod_config'] = false; // show 'configure' invoice in viewmods
$config ['mod_main_class'] = 'CInvoice';
$config ['permissions_item_table'] = 'invoices';
$config ['permissions_item_field'] = 'invoice_id';
$config ['permissions_item_label'] = 'invoice_name';

if ($a == 'setup') {
	echo apmshowModuleConfig ( $config );
}

/**
 * Class CSetupInvoices
 *
 * @package apmProject\modules\misc
 */
class CSetupInvoices extends apm_System_Setup {
	public function remove() {
		$q = $this->_getQuery ();
		$q->dropTable ( 'invoices' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->dropTable ( 'invoices_templates' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->dropTable ( 'invoices_items' );
		$q->exec ();
		
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'InvoiceCategory\'' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'InvoicePaymentType\'' );
		$q->exec ();
		$q = $this->_getQuery ();
		$q->setDelete ( 'sysvals' );
		$q->addWhere ( 'sysval_title = \'InvoiceTemplateCategory\'' );
		$q->exec ();
		return parent::remove ();
	}
	
	public function install() {
		$result = $this->_checkRequirements ();
		
		if (! $result) {
			return false;
		}
		
		$q = $this->_getQuery ();
		$q->createTable ( 'invoices' );
		$q->createDefinition ( '(
			invoice_id int(11) NOT NULL AUTO_INCREMENT,
			invoice_name varchar(255) NOT NULL DEFAULT "",
			invoice_issue_date datetime DEFAULT NULL,
			invoice_sale_date datetime DEFAULT NULL,
			invoice_pay_datement datetime DEFAULT NULL,
			invoice_place varchar(255) NOT NULL DEFAULT "",			
			invoice_parties_owner text DEFAULT NULL,
			invoice_parties_client text DEFAULT NULL,

			invoice_items text DEFAULT NULL,
			
			invoice_total_pay decimal(10,2) NOT NULL DEFAULT "0",	
			invoice_payed decimal(10,2) NOT NULL DEFAULT "0",
			invoice_payment_type int(11) DEFAULT "0",
			invoice_bank_account varchar(255) NOT NULL DEFAULT "",
			
			invoice_authorized_issue text DEFAULT NULL,
			invoice_authorized_receive text DEFAULT NULL,
			invoice_description text,
			
			invoice_template int(11) DEFAULT NULL,
			invoice_source text,

			invoice_category int(11) NOT NULL DEFAULT "0",
			invoice_owner int(11) DEFAULT "0",
			invoice_create_date datetime DEFAULT NULL,
			sign_src text DEFAULT NULL,
			sign_u text DEFAULT NULL,
			sign_ued text DEFAULT NULL,
            PRIMARY KEY ( invoice_id )) ENGINE = MYISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ' );
		if (! $q->exec ()) {
			return false;
		}

		$q = $this->_getQuery ();
		$q->createTable ( 'invoices_templates' );
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

		$q = $this->_getQuery ();
		$q->createTable ( 'invoices_items' );
		$q->createDefinition ( '(
			item_id int(11) NOT NULL AUTO_INCREMENT,
			item_invoice int(11) NOT NULL DEFAULT "0",
			item_name varchar(255) NOT NULL DEFAULT "",
			item_quantity int(11) NOT NULL DEFAULT "0",
			item_unit varchar(255) NOT NULL DEFAULT "",
			item_unit_price varchar(255) NOT NULL DEFAULT "",
			item_tax_rare int(11) NOT NULL DEFAULT "0",
			
			item_date datetime DEFAULT NULL,
			item_owner int(11) DEFAULT "0",
			item_properties text,

            PRIMARY KEY ( item_id )
            ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8' );
		if (! $q->exec ()) {
			echo "dupa"; exit;
			return false;
		}
		
		$i = 0;
		$invoicePaymentTypes = array (
			'cash',
			'bank transfer',
			'electronic payment'
		);
		foreach ( $invoicePaymentTypes as $invoicePaymentType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'InvoicePaymentType');
			$q->addInsert ( 'sysval_value', $invoicePaymentType);
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		$i = 0;
		$invoiceTypes = array (
			'Draft',
			'Issue',
			'In signing',
			'Approve',
			'Archive'
		);
		foreach ( $invoiceTypes as $invoiceType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'InvoiceCategory' );
			$q->addInsert ( 'sysval_value', $invoiceType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		$i = 0;
		$invoiceTemplatesTypes = array (
			'Private',
			'Public'
		);
		foreach ( $invoiceTemplatesTypes as $invoiceTemplateType ) {
			$q = $this->_getQuery ();
			$q->addTable ( 'sysvals' );
			$q->addInsert ( 'sysval_key_id', 1 );
			$q->addInsert ( 'sysval_title', 'InvoiceTemplateType' );
			$q->addInsert ( 'sysval_value', $invoiceTemplateType );
			$q->addInsert ( 'sysval_value_id', $i );
			$q->exec ();
			$i ++;
		}
		
		return parent::install ();
	}
}