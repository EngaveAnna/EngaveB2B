<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$apmconfig ['dbtype'] = '[DBTYPE]'; 
$apmconfig ['dbchar'] = '[DBCHAR]'; 
$apmconfig ['dbhost'] = '[DBHOST]';
$apmconfig ['dbname'] = '[DBNAME]'; 
$apmconfig ['dbuser'] = '[DBUSER]'; 
$apmconfig ['dbpass'] = '[DBPASS]'; 
$apmconfig ['dbprefix'] = '[DBPREFIX]'; 
                                       
// set this value to true to use persistent database connections
$apmconfig ['dbpersist'] = false;

/**
 * *************** Configuration for DEVELOPERS use only! *****
 */
// Root directory and base_url are automatically set to avoid

$apmconfig ['root_dir'] = apm_BASE_DIR;
$apmconfig ['base_url'] = apm_BASE_URL;
