<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
require_once("./lib/dompdf/dompdf_config.inc.php");
//APM index.php header must be clean otherwise will be added to .pdf source
//APM here because $uistyle



if(!isset($_REQUEST['typePDF']))
$typePDF=null;
else 
$typePDF = apmgetParam ( $_REQUEST, 'typePDF', 0 );


switch($typePDF)
{
	case "preview":
		$obj=new CAgreement();
		global $AppUI, $cal_sdf;
		$pre=apmgetParam ( $_POST, 'pre', 0 );
		$src=$_POST[$pre.'_source'];
		$source=$obj->sourceInterpreter($src, $_POST);
		$object_id=apmgetParam ( $_POST, $pre.'_id', 0 );
		$outputName=$AppUI->_('Agreement').'_ID'.$object_id.'_preview.pdf';
	break;
	case "tosign":
		$object_id = ( int ) apmgetParam($_GET, 'id', 0 );
		$object = new CAgreement();
		$object->setId ( $object_id );
		global $AppUI, $cal_sdf;
		
		$obj = $object;
		$canAddEdit = $obj->canAddEdit ();
		$canAuthor = $obj->canCreate ();
		$canEdit = $obj->canEdit ();
		$canDelete = $object->canDelete ();
		if (! $canAddEdit) {
			$AppUI->redirect ( ACCESS_DENIED );
		}
		
		$agrData=$object->getAgreementById($object->getId());
		
		if (! $object && $object_id > 0) {
			$AppUI->setMsg ( 'Agreement' );
			$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}
		
		$source=$agrData[0]['sign_src'];
		$outputName=$AppUI->_('Agreement').'_ID'.$object_id.'.pdf';
	break;
	default:
		$object_id = ( int ) apmgetParam($_GET, 'id', 0 );
		$object = new CAgreement();
		$object->setId ( $object_id );
		global $AppUI, $cal_sdf;
		
		$obj = $object;
		$canAddEdit = $obj->canAddEdit ();
		$canAuthor = $obj->canCreate ();
		$canEdit = $obj->canEdit ();
		$canDelete = $object->canDelete ();
		if (! $canAddEdit) {
			$AppUI->redirect ( ACCESS_DENIED );
		}
		
		$agrData=$object->getAgreementById($object->getId());
		
		if (! $object && $object_id > 0) {
			$AppUI->setMsg ( 'Agreement' );
			$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}

		$source=$object->sourceInterpreter($agrData[0]['agreement_source'], $agrData[0]);
		$source=$source;
		$outputName=$AppUI->_('Agreement').'_ID'.$object_id.'.pdf';
	break;		
}

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