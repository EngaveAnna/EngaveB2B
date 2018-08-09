<?php
/**
 * @package     apmProject\modules\misc
 */
class CInvoice extends apm_Core_BaseObject {
	
	public $invoice_id = null;
	public $invoice_name = null;
	public $invoice_issue_date = null;
	public $invoice_sale_date = null;
	public $invoice_pay_date = null;		
	public $invoice_place = null;
	public $invoice_parties_owner = null;
	public $invoice_parties_client = null;
	
	public $invoice_items = null;
	
	public $invoice_total_pay = null;
	public $invoice_payed = null;
	public $invoice_payment_type = null;
	public $invoice_bank_account = null;
	
	public $invoice_authorized_issue = null;
	public $invoice_authorized_receive = null;
	public $invoice_description = null;
	
	public $invoice_template = null;
	public $invoice_source = null;
	
	public $invoice_category = null;
	public $invoice_owner = null;
	public $invoice_create_date = null;
	public $invoice_paystatus = null;
	public $sign_src = null;
	public $sign_u = null;
	public $sign_ued = null;
	
	public function __construct() {
		parent::__construct ( 'invoices', 'invoice_id' );
	}

	public function getInvoicesByCategory($notUsed = null, $category_id = 0, $search = '', $userid=1) {
		$q = $this->_getQuery ();
		$q->addTable ( 'invoices','a' );
		$q->addTable ( 'companies','c' );
		$q->addQuery ( 'a.*, c.company_name' );
		$q->addWhere('c.company_id=a.invoice_parties_client');
	
		if ($search != '') {
			$q->addWhere ( '(a.invoice_name LIKE \'%' . $search . '%\' OR a.invoice_description LIKE \'%' . $search . '%\')' );
		}
	
		if ($category_id >= 0) { // Category
			$q->addWhere ( 'a.invoice_category = ' . $category_id );
		}
	
		$q->addOrder ( 'a.invoice_create_date' );
		return $q->loadList ();
	}
	public function getInvoiceById($invoice_id = 0, $load=false) {
		if(is_numeric($invoice_id)) 
		$where='invoice_id = ' . $invoice_id;
		else
		$where='invoice_id = IN('.$invoice_id.')';

		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'invoices' );
		$q->addWhere($where);
		if($load)
		{
			$hash=$q->loadHash();
			$q->bindHashToObject ( $hash, $this, null, true );
			return $q->loadList ();
		}
		else
		return $q->loadList ();
	}

	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->invoice_name )) {
			$this->_error ['invoice_name'] = $baseErrorMsg . 'invoice name is not set';
		}
		
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		$this->invoice_create_date = $q->dbfnNowWithTZ (0);
/* 		$this->invoice_issue_date = $q->dbfnNowWithTZ (1);
		$this->invoice_pay_date = $q->dbfnNowWithTZ (1);
		$this->invoice_sale_date = $q->dbfnNowWithTZ (1); */				
		$this->invoice_owner = ( int ) $this->invoice_owner ? $this->invoice_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'invoices';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'invoices';
		$search ['table_key'] = 'invoice_id'; // primary key in searched table
		$search ['table_invoice'] = 'index.php?m=invoices&a=addedit&invoice_id='; // first part of invoice
		$search ['table_title'] = 'Invoices';
		$search ['table_orderby'] = 'invoice_name';
		$search ['search_fields'] = array(
				'l.invoice_name',
				'l.invoice_description' 
		);
		$search ['display_fields'] = $search ['search_fields'];
		return $search;
	}
	
	function getTopayVal($itemSchema, $invoice_items)
	{
		$r=null;
		$sysVals['item_tax_rare']=explode(',',apmgetConfig('item_tax_rare'));
	
		//APM item_properties join to invoice_items
		foreach($invoice_items as $dataKey=>$dataVal)
		{
			if($dataVal['item_properties'])
				$dataVal=array_merge($dataVal, unserialize($dataVal['item_properties']));
			$invoice_items[$dataKey]=$dataVal;
		}
	
		foreach($sysVals['item_tax_rare'] as $tkey=>$tval)
		{
			foreach($invoice_items as $dataKey=>$dataVal)
			{
				if($dataVal['item_tax_rare']==$tkey)
					$row[$tkey][]=$dataVal;
			}
			
	
			if($row[$tkey])
			{
				$sum[$tkey]['resume_tax_rare']=$tval;
				foreach($row[$tkey] as $rowKey=>$rowVal)
				{
					$sum[$tkey]['resume_net']+=$rowVal['item_quantity']*$rowVal['item_unit_price'];
				}
			}
		}
	
		foreach($sum as $dataKey=>$dataVal)
		{

			foreach($itemSchema as $key=>$val)
			{
				switch($val['name'])
				{
					case 'resume_net':
						$resume['resume_net']+=$dataVal['resume_net'];
					break;
					case 'resume_tax_val':
						$t=str_replace('%', '', $dataVal['resume_tax_rare']) / 100;
						$d= (is_numeric($t))? $t:0;
						$taxval=$dataVal['resume_net']*$d;
						$resume['resume_tax_val']+=$taxval;
					break;
					case 'resume_gross':
						$grossval=$taxval+$dataVal['resume_net'];
						$resume['resume_gross']+=$grossval;
						break;
				}
			}
		}
		return $resume;
	}	
	function slownie ($kw) {
	
		$t_a = array('','sto','dwieście','trzysta','czterysta','pięćset','sześćset','siedemset','osiemset','dziewięćset');
		$t_b = array('','dziesięć','dwadzieścia','trzydzieści','czterdzieści','pięćdziesiąt','sześćdziesiąt','siedemdziesiąt','osiemdziesiąt','dziewięćdziesiąt');
		$t_c = array('','jeden','dwa','trzy','cztery','pięć','sześć','siedem','osiem','dziewięć');
		$t_d = array('dziesięć','jedenaście','dwanaście','trzynaście','czternaście','piętnaście','szesnaście','siednaście','osiemnaście','dziewiętnaście');
	
		$t_kw_15 = array('septyliard','septyliardów','septyliardy');
		$t_kw_14 = array('septylion','septylionów','septyliony');
		$t_kw_13 = array('sekstyliard','sekstyliardów','sekstyliardy');
		$t_kw_12 = array('sekstylion','sekstylionów','sepstyliony');
		$t_kw_11 = array('kwintyliard','kwintyliardów','kwintyliardy');
		$t_kw_10 = array('kwintylion','kwintylionów','kwintyliony');
		$t_kw_9 = array('kwadryliard','kwadryliardów','kwaryliardy');
		$t_kw_8 = array('kwadrylion','kwadrylionów','kwadryliony');
		$t_kw_7 = array('tryliard','tryliardów','tryliardy');
		$t_kw_6 = array('trylion','trylionów','tryliony');
		$t_kw_5 = array('biliard','biliardów','biliardy');
		$t_kw_4 = array('bilion','bilionów','bilony');
		$t_kw_3 = array('miliard','miliardów','miliardy');
		$t_kw_2 = array('milion','milionów','miliony');
		$t_kw_1 = array('tysiąc','tysięcy','tysiące');
		$t_kw_0 = array('złoty','złotych','złote');
	
		if ($kw!='') {
			$kw=(substr_count($kw,'.')==0) ? $kw.'.00':$kw;
			$tmp=explode(".",$kw);
			$ln=strlen($tmp[0]);
			$tmp_a=($ln%3==0) ? (floor($ln/3)*3):((floor($ln/3)+1)*3);
			for($i = $ln; $i < $tmp_a; $i++) {
		  $l_pad .= '0';
		  $kw_w = $l_pad.$tmp[0];
			}
			$kw_w=($kw_w=='') ? $tmp[0]:$kw_w;
			$paczki=(strlen($kw_w)/3)-1;
			$p_tmp=$paczki;
			for($i=0;$i<=$paczki;$i++) {
		  $t_tmp='t_kw_'.$p_tmp;
		  $p_tmp--;
		  $p_kw=substr($kw_w,($i*3),3);
		  $kw_w_s=($p_kw{1}!=1) ? $t_a[$p_kw{0}].' '.$t_b[$p_kw{1}].' '.$t_c[$p_kw{2}]:$t_a[$p_kw{0}].' '.$t_d[$p_kw{2}];
		  if(($p_kw{0}==0)&&($p_kw{2}==1)&&($p_kw{1}<1)) $ka=${$t_tmp}[0]; //możliwe że $p_kw{1}!=1
		  else if (($p_kw{2}>1 && $p_kw{2}<5)&&$p_kw{1}!=1) $ka=${$t_tmp}[2];
		  else $ka=${$t_tmp}[1];
		  $kw_slow.=$kw_w_s.' '.$ka.' ';
			}
		}
		$text = $kw_slow.' '.$tmp[1].'/100 gr.';
		return $text;
	}

	
	function sourceInterpreter($source, $markerData, $itemSchema=null , $resumeSchema=null)
	{
			$document = new DOMDocument();
			libxml_use_internal_errors(true);
			$document->loadHTML(mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8'));
			$labels= $document->getElementsByTagName('label');
			$labelsCount= $labels->length;
			
			$sysVals=array(
				'invoice_category' => apmgetSysVal('InvoiceCategory'),
				'invoice_payment_type' => apmgetSysVal('InvoicePaymentType'),
				'invoice_priority' => apmgetSysVal ('ProjectPriority'),
			);
			
			//print_r($sysVals['invoice_category']); exit;
			$marker_textEditor_dict=array('invoice_name', 'invoice_place', 'invoice_issue_date', 'invoice_pay_date', 'invoice_sale_date', 'invoice_parties_owner', 'invoice_parties_client', 'invoice_total_pay', 'invoice_payed', 'invoice_payment_type', 'invoice_bank_account', 'invoice_authorized_issue', 'invoice_authorized_receive', 'invoice_description', 'invoice_items', 'invoice_resume');			
			
			
			$markerData['invoice_issue_date']=$markerData['issue_date'];
			$markerData['invoice_pay_date']=$markerData['pay_date'];
			$markerData['invoice_sale_date']=$markerData['sale_date'];
			
			$cc=new CCompany();
			if(!(empty($markerData['invoice_parties_owner'])&&empty($markerData['invoice_parties_client'])))
			$parties=trim(join(',',array($markerData['invoice_parties_owner'],$markerData['invoice_parties_client'])),",");
			$obj = new CCompany ();
			$q = new apm_Database_Query ();
			$q->addTable('companies');
			$q->addQuery ( '*' );
			if(!empty($parties)) $q->addWhere ( 'company_id IN ('.$parties.')');
			$res = $q->loadList ();
			
			$markerData['invoice_parties_owner']=$res[0]['company_name'] .'<br>'. $res[0]['company_address1'].', '.$res[0]['company_zip'].' '.$res[0]['company_city'].', '.$this->_AppUI->_('company_tin').': '.$res[0]['company_tin'];
			$markerData['invoice_parties_client']=$res[1]['company_name'].'<br>'.$res[1]['company_address1'].', '.$res[1]['company_zip'].' '.$res[1]['company_city'].', '.$this->_AppUI->_('company_tin').': '.$res[1]['company_tin'];
			$invoice_parties_client = $markerData['invoice_parties_client'];
			//APM Item table
			$it=new CItem();
			
			$invoice_items=null;
			
			//APM unused, interpreted sourece is in 'sign_src' column
			//$invoice_items=$it->getItemsByInvoice($markerData['invoice_id'], $this->_AppUI->user_id);

			$invoice_items=$markerData['invoice_items'];

			
			$tableHeader=$it->getDocTableHead($itemSchema);
			$tableBody=$it->getDocTableBody($itemSchema, $invoice_items);
			$colspan=count($itemSchema)+1;
			$t='<div class="table-responsive">
			<table class="table table-bordered table-hover" id="js_0_5_7_tabela" style="width:100%;">
			<tbody><tr><td colspan="'.$colspan.'"><span class="naglowek2">'.$this->_AppUI->_('Invoice items').'</span></td></tr>';
			$t.=$tableHeader;
			$t.=$tableBody;
			$t.="</tbody></table></div>";
			$markerData['invoice_items']=$t;
			
			//APM Items resume table (using $it object)
			$tableResumHeader=$it->getResumTableHead($resumeSchema);
			$tableResumBody=$it->getResumTableBody($resumeSchema, $invoice_items);
			$colspan2=count($resumeSchema);
			$r='<div class="No-Break">
			<div class="table-responsive">
			<table class="table table-bordered table-hover" id="js_0_5_9_tabela" style="width:100%;">
			<tbody class="No-Break"><tr><td colspan="'.$colspan2.'"><span class="naglowek2">'.$this->_AppUI->_('Invoice resume').'</span></td></tr>';
			$r.=$tableResumHeader;
			$r.=$tableResumBody;		
			$r.='</tbody></table></div></div>';
			$markerData['invoice_resume']=$r;
			
			$markerData['invoice_topay']=$_POST['invoice_total_pay']-$_POST['invoice_payed'];
			$markerData['invoice_topay']=number_format($markerData['invoice_topay'], 2, ',', ' ');
			$markerData['invoice_authorized_issue'] = implode(', ', loadModal('contacts', $markerData['invoice_authorized_issue']));
			$markerData['invoice_authorized_receive'] = implode(', ', loadModal('contacts', $markerData['invoice_authorized_receive']));
			

			$markerData['invoice_priority'] = lcfirst($sysVals['invoice_priority'][$markerData['invoice_priority']]);
			$markerData['invoice_category'] = lcfirst($sysVals['invoice_category'][$markerData['invoice_category']]);
			$markerData['invoice_payment_type'] = lcfirst($sysVals['invoice_payment_type'][$markerData['invoice_payment_type']]);

			$markerData['sign_u'] = implode(', ', loadModal('contacts', $markerData['sign_u']));

			$markerData['invoice_project'] = implode(', ', loadModal('projects', $markerData['invoice_project']));
			$markerData['invoice_task'] = implode(', ', loadModal('tasks', $markerData['invoice_task']));

			for($i=$labelsCount;$i>0;--$i)
			{
				$label=$labels->item($i-1);
				$labelId= $label->getAttribute('id');
				$link= $document->createElement('a');
				$link->setAttribute('id', $labelId);
				$link->nodeValue=$markerData[$labelId];
				$label->parentNode->replaceChild($link, $label);
			}
			return htmlspecialchars_decode($document->saveHTML($document->documentElement->firstChild));
	}
	
}
