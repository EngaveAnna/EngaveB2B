<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
require_once("./lib/dompdf/dompdf_config.inc.php");
//APM index.php header must be clean otherwise will be added to .pdf source
//APM here because $uistyle

$outputName=$AppUI->_('Report').'_ID'.$rid.'.pdf';

$uistyle = $AppUI->getPref ( 'UISTYLE' ) ? $AppUI->getPref ( 'UISTYLE' ) : apmgetConfig ( 'host_style' );
$html ='<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="./style/common.css" media="all" />
<link rel="stylesheet" type="text/css" href="./style/'.$uistyle.'/main.css" media="all"	/>
<link rel="stylesheet" type="text/css" href="./style/'.$uistyle.'/pdf.css" media="all" />
</head>';
$html .=$source;
$html .='</html>';

$dompdf = new DOMPDF();

$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream($outputName);
?>