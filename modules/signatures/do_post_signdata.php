<?php //header("Content-Type: text/xml");
$obj = new CSignature ();
$mod_name=apmgetParam ( $_GET, 'mod', 0 );
$mod_id = $obj->getModuleName($mod_name,null);
$row = apmgetParam( $_GET, 'id', 0);
$owner = apmgetParam( $_GET, 'oid', 0);
$signature_name=md5(time());
$obj->postSignData($signature_name, $mod_name, $mod_id, $row, $owner, file_get_contents("php://input"));
?>