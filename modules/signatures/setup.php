<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$config = array ();
$config ['mod_name'] = 'Signatures';
$config ['mod_version'] = '3.0.0';
$config ['mod_directory'] = 'signatures'; // tell apmProject where to find this module
$config ['mod_setup_class'] = 'CSetupSignatures'; // the name of the PHP setup class (used below)
$config ['mod_type'] = 'user'; // 'core' for modules distributed with apm by standard, 'user' for additional modules from dotmods
$config ['mod_ui_name'] = 'Signatures';
$config ['mod_ui_icon'] = 'communicate.gif';
$config ['mod_description'] = 'Based on SafeSignatures software by Softmag sp. zo.o.';
$config ['mod_config'] = false; // show 'configure' signature in viewmods
$config ['mod_main_class'] = 'CSignature';
$config ['permissions_item_table'] = 'signatures';
$config ['permissions_item_field'] = 'signature_id';
$config ['permissions_item_label'] = 'signature_name';

if ($a == 'setup') {
	echo apmshowModuleConfig ( $config );
}

/**
 * Class CSetupSignatures
 *
 * @package apmProject\modules\misc
 */
class CSetupSignatures extends apm_System_Setup {
	public function remove() {
		$q = $this->_getQuery ();
		$q->dropTable ( 'signatures' );
		$q->exec ();
	
		return parent::remove ();
	}
	public function install() {
		$result = $this->_checkRequirements ();
		
		if (! $result) {
			return false;
		}
		
		$q = $this->_getQuery ();
		$q->createTable ( 'signatures' );
		$q->createDefinition ( '(
            signature_id int( 11 ) NOT NULL AUTO_INCREMENT ,
			signature_name varchar(255) NOT NULL DEFAULT "",
			signature_mod int(11) NOT NULL DEFAULT "0",
			signature_row int(11) NOT NULL DEFAULT "0",
			signature_description DEFAULT NULL,
			signature_source text,
			signature_owner int(11) DEFAULT "0",
			signature_date datetime DEFAULT NULL,
			signature_category int(11) NOT NULL DEFAULT "0"
            PRIMARY KEY ( signature_id ),
            KEY idx_signature_task ( signature_task ) ,
            KEY idx_signature_mod ( signature_mod ) ,
            KEY idx_signature_owner ( signature_owner )
            ) ENGINE = MYISAM DEFAULT CHARSET=utf8' );
		if (! $q->exec ()) {
			return false;
		}

		return parent::install ();
	}
}