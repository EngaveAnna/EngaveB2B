<?php 
header("Content-Type: text/xml");
$obj = new CSignature ();
$signature_mod_name=apmgetParam ( $_GET, 'mod', 0 );
$signature_row =apmgetParam( $_GET, 'id');

$get=$obj->getSignData($signature_mod_name, $signature_row);
$string = "<?xml version='1.0'?>
<document>
<body>". base64_encode($get[0]['sign_src'])."</body>
</document>";
echo $string;
?>