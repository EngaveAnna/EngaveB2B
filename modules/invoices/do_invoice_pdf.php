<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
require_once("./lib/dompdf/dompdf_config.inc.php");
//APM index.php header must be clean otherwise will be added to .pdf source
//APM here because $uistyle

$itemSchema=array(
		'0'=>array('name'=>'item_name', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'30%'),
		'1'=>array('name'=>'item_pkwiu', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left'),
		'2'=>array('name'=>'item_quantity', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right'),
		'3'=>array('name'=>'item_unit', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left'),
		'4'=>array('name'=>'item_unit_price', 'regex'=>'/^[\d]{1,8}$|^[\d]{1,8}[,.]{1}[\d]{1,2}$/', 'type'=>'text', 'align'=>'right'),
		'5'=>array('name'=>'item_tax_rare', 'regex'=>'/^[0-9]{1,9}$/', 'type'=>'select', 'align'=>'right')
);

$itemDocSchema=array(
		'0'=>array('name'=>'item_name', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'30%'),
		'1'=>array('name'=>'item_pkwiu', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left'),
		'2'=>array('name'=>'item_quantity', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right'),
		'3'=>array('name'=>'item_unit', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left'),
		'4'=>array('name'=>'item_unit_price', 'regex'=>'/^[\d]{1,8}$|^[\d]{1,8}[,.]{1}[\d]{1,2}$/', 'type'=>'price', 'align'=>'right'),
		'5'=>array('name'=>'item_net_val', 'regex'=>'/^[0-9]{1,9}$/', 'type'=>'item_net_val', 'align'=>'right'),
		'6'=>array('name'=>'item_tax_rare', 'regex'=>'/^[0-9]{1,9}$/', 'type'=>'select', 'align'=>'right')
);

$resumeSchema=array(
		'0'=>array('name'=>'resume_tax_rare', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
		'1'=>array('name'=>'resume_net', 'regex'=>'/^[\S]{0,255}$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),
		'2'=>array('name'=>'resume_tax_val', 'regex'=>'/^.*\S.*$/', 'type'=>'text', 'align'=>'left', 'width'=>'25%'),		
		'3'=>array('name'=>'resume_gross', 'regex'=>'/^[1-9][0-9]*$/', 'type'=>'text', 'align'=>'right', 'width'=>'25%')	
);


if(!isset($_REQUEST['typePDF']))
$typePDF=null;
else 
$typePDF = apmgetParam ( $_REQUEST, 'typePDF', 0 );


switch($typePDF)
{
	case "preview":

		$object=new CInvoice();
		global $AppUI, $cal_sdf;
		$pre=apmgetParam ( $_POST, 'pre', 0 );
			
		$object_id=apmgetParam ( $_POST, $pre.'_id', 0 );
		$object->setId ( $object_id );

		$obj = $object;
		$canAddEdit = $obj->canAddEdit ();
		$canAuthor = $obj->canCreate ();
		$canEdit = $obj->canEdit ();
		$canDelete = $object->canDelete ();
		if (! $canAddEdit) {
			$AppUI->redirect ( ACCESS_DENIED );
		}
		
		if (!$object && $object_id > 0) {
			$AppUI->setMsg ( 'Invoice' );
			$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}

		if (empty($_POST['invoice_template'])) {
			$AppUI->setMsg ( 'Invoice' );
			$AppUI->setMsg ( $AppUI->_('Select invoice template'), UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}
		else
		{
			require_once 'invoices_tpl.class.php';
			require_once('items.class.php');
			$ct= new CInvoiceTemplate();
			$tpl=$ct->getTemplatesById($_POST['invoice_template'], false);
			$topay=$object->getTopayVal($resumeSchema, $_POST['invoice_items']);

			$_POST['invoice_total_pay']=$topay['resume_gross'];
			$_POST['invoice_topay']=$_POST['invoice_total_pay']-$_POST['invoice_payed'];
			$_POST['invoice_topay_say']=$object->slownie(number_format($_POST['invoice_topay'], 2, '.', ''));

			$_POST['sign_src']=$_POST['invoice_source']=$object->sourceInterpreter($tpl[0]['template_source'], $_POST, $itemDocSchema, $resumeSchema);
			$_POST['sign_u']=trim(join(',', array($_POST['invoice_authorized_issue'],$_POST['invoice_authorized_receive'])), ",");
		}		
		$source=$object->sourceInterpreter($agrData[0]['invoice_source'], $agrData[0], $itemSchema, $resumeSchema);

		$source=$_POST['invoice_source'];	
#file_put_contents("/tmp/test.txt","Source1: $source\n");
	
		$outputName=$AppUI->_('Invoice').'_ID'.$object_id.'_preview.pdf';
	break;
	case "tosign":
		$object_id = ( int ) apmgetParam($_GET, 'id', 0 );
		$object = new CInvoice();
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
		
		$agrData=$object->getInvoiceById($object->getId());
		
		if (! $object && $object_id > 0) {
			$AppUI->setMsg ( 'Invoice' );
			$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}
		
		$source=$agrData[0]['sign_src'];
		$outputName=$AppUI->_('Invoice').'_ID'.$object_id.'.pdf';
	break;
	default:
		$object_id = ( int ) apmgetParam($_GET, 'id', 0 );
		$object = new CInvoice();
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
		
		$agrData=$object->getInvoiceById($object->getId());
		
		if (! $object && $object_id > 0) {
			$AppUI->setMsg ( 'Invoice' );
			$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
			$AppUI->redirect ( 'm=' . $m );
		}

		$source=$object->sourceInterpreter($agrData[0]['invoice_source'], $agrData[0], $itemSchema, $resumeSchema);
		$outputName=$AppUI->_('Invoice').'_ID'.$object_id.'.pdf';
	break;		
}


$uistyle = $AppUI->getPref ( 'UISTYLE' ) ? $AppUI->getPref ( 'UISTYLE' ) : apmgetConfig ( 'host_style' );
$html ='<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="./style/common.css" media="all" />
<link rel="stylesheet" type="text/css" href="./style/'.$uistyle.'/main.css" media="all"	/>
<link rel="stylesheet" type="text/css" href="./style/'.$uistyle.'/pdf.css" media="all" />
<link rel="stylesheet" type="text/css" href="./style/'.$uistyle.'/invoices.css" media="all" />
</head>';
$html .=$source;
$html .='</html>';

$dompdf = new DOMPDF();

$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream($outputName);
?>
