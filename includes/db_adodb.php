<?php
/*
 * Based on Leo West's (west_leo@yahooREMOVEME.com):
 * lib.DB
 * Database abstract layer
 * -----------------------
 * ADODB VERSION
 * -----------------------
 * A generic database layer providing a set of low to middle level functions
 * originally written for WEBO project, see webo source for "real life" usages
 */
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

require_once (apm_BASE_DIR . '/lib/adodb/adodb.inc.php');

$db = NewADOConnection ( apmgetConfig ( 'dbtype' ) );

$connection = new apm_Database_Connection ( $db );
$connection->connect ( apmgetConfig ( 'dbhost' ), apmgetConfig ( 'dbname' ), apmgetConfig ( 'dbuser' ), apmgetConfig ( 'dbpass' ), apmgetConfig ( 'dbpersist' ) );

$charset = apmgetConfig ( 'dbchar', 'utf8' );
/**
 * This explicitly sets the character set of the connection.
 */
// if ('mysql' == apmgetConfig('dbtype') && '' != $charset) {
// $sql = "SET NAMES $charset";
// $db->Execute($sql);
// }

/*
 * Having successfully established the database connection now,
 * we will hurry up to load the system configuration details from the database.
 */

$sql = 'SELECT config_name, config_value, config_type FROM ' . apmgetConfig ( 'dbprefix' ) . 'config';
$rs = $db->Execute ( $sql );

if ($rs) { // Won't work in install mode.
	$rsArr = $rs->GetArray ();
	
	switch (strtolower ( trim ( apmgetConfig ( 'dbtype' ) ) )) {
		case 'oci8' :
		case 'oracle' :
			foreach ( $rsArr as $c ) {
				if ($c ['CONFIG_TYPE'] == 'checkbox') {
					$c ['CONFIG_VALUE'] = ($c ['CONFIG_VALUES'] == 'true') ? true : false;
				}
				$apmconfig [$c ['CONFIG_NAME']] = $c ['CONFIG_VALUE'];
			}
			break;
		default :
			// mySQL
			foreach ( $rsArr as $c ) {
				if ($c ['config_type'] == 'checkbox') {
					$c ['config_value'] = ($c ['config_value'] == 'true') ? true : false;
				}
				$apmconfig [$c ['config_name']] = $c ['config_value'];
			}
	}
}
