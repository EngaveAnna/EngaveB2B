<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$apmconfig ['dbtype'] = 'mysqli'; 
$apmconfig ['dbchar'] = 'utf8'; 
$apmconfig ['dbhost'] = 'localhost';
$apmconfig ['dbname'] = 'newdatabase'; 
$apmconfig ['dbuser'] = 'root'; 
$apmconfig ['dbpass'] = 'P@ssw0rd14'; 
$apmconfig ['dbprefix'] = ''; 
                             
// set this value to true to use persistent database connections
$apmconfig ['dbpersist'] = false;

/**
 * *************** Configuration for DEVELOPERS use only! *****
 */
// Root directory and base_url are automatically set to avoid
$apmconfig ['root_dir'] = apm_BASE_DIR;
$apmconfig ['base_url'] = apm_BASE_URL;