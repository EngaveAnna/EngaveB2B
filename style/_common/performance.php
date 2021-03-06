<?php
$db_info = $db->ServerInfo ();
print ('<table width="100%" cellspacing="0" cellpadding="4" border="0"  class="system-info">') ;
print ('<tr valign="top">') ;
print ('<td align="center" width="100%">') ;
print ('    <table width="100%" cellspacing="0" cellpadding="4" border="0" class="std">') ;
print ('    <tr valign="top">') ;
print ('        <th width="100%">System Environment</th>') ;
print ('    </tr>') ;
print ('    <tr valign="top">') ;
print ('    <td width="100%">') ;
print ('        <p><b>apmProject ' . $AppUI->getVersion () . '</b></p>') ;
print ('        <p>PHP version nr: ' . PHP_VERSION . '</p>') ;
print ('        <p>DB provider and version nr: ' . $db->dataProvider . ' ' . $db_info ['version'] . ' (' . $db_info ['description'] . ')</p>') ;
print ('        <p>DB Table Prefix: "' . apmgetConfig ( 'dbprefix' ) . '"</p>') ;
print ('        <p>Web Server: ' . safe_get_env ( 'SERVER_SOFTWARE' ) . '</p>') ;
print ('        <p>Server Protocol | Gateway Interface: ' . safe_get_env ( 'SERVER_PROTOCOL' ) . ' | ' . safe_get_env ( 'GATEWAY_INTERFACE' ) . '</p>') ;
print ('        <p>Client Browser: ' . safe_get_env ( 'HTTP_USER_AGENT' ) . '</p>') ;
print ('        <p>URL Query: ' . safe_get_env ( 'QUERY_STRING' ) . '</p>') ;

$right_now_is = new apm_Utilities_Date ();
print ('        <p>Server Time | Timezone: ' . $right_now_is->format ( FMT_DATERFC822 ) . ' | ' . date ( 'T' ) . '</p>') ;
print ('        <p>PHP Max. Execution Time: ' . ini_get ( 'max_execution_time' ) . ' seconds</p>') ;
print ('        <p>Memory Limit: ' . (ini_get ( 'memory_limit' ) ? str_replace ( 'M', ' Mb', ini_get ( 'memory_limit' ) ) : 'Not Defined') . '</p>') ;
print ('    </td>') ;
print ('    </tr>') ;
print ('    <tr valign="top">') ;
print ('        <th width="100%">Performance</th>') ;
print ('    </tr>') ;
print ('    <tr valign="top">') ;
print ('    <td width="100%">') ;
print ('        <p>Memory Used: ' . sprintf ( '%01.2f Mb', memory_get_usage () / pow ( 1024, 2 ) ) . '</p>') ;
print ('        <p>Memory Unused: ' . sprintf ( '%01d Kb', (memory_get_usage () - $apm_performance_memory_marker) / 1024 ) . '</p>') ;
print ('        <p>Memory Peak: ' . sprintf ( '%01d Kb', (memory_get_peak_usage () - $apm_performance_memory_marker) / 1024 ) . '</p>') ;
print ('        <p>Page (Buffer) Size: ' . sprintf ( '%01.2f kb', ob_get_length () / 1024, 2 ) . '</p>') ;
printf ( '        <p>Setup in %.3f seconds</p>', $apm_performance_setuptime );
printf ( '        <p>ACLs checked in %.3f seconds</p>', $apm_performance_acltime );
print ('        <p>ACLs nr of checks: ' . $apm_performance_aclchecks . '</p>') ;
printf ( '        <p>apm Data checked in %.3f seconds</p>', $apm_performance_dbtime );
print ('        <p>apm DBQueries executed: ' . $apm_performance_dbqueries . ' queries</p>') ;
print ('        <p>apm Old Queries executed: ' . $apm_performance_old_dbqueries . ' queries</p>') ;
print ('        <p>apm Total Queries executed: ' . ( int ) ($apm_performance_old_dbqueries + $apm_performance_dbqueries) . ' queries</p>') ;
printf ( '        <p><b>Page generated in %.3f seconds</b></p>', (array_sum ( explode ( ' ', microtime () ) ) - $apm_performance_time) );
print ('    </td>') ;
print ('    </tr>') ;
print ('    </table>') ;
print ('</td>') ;
print ('</tr>') ;
print ('</table>') ;