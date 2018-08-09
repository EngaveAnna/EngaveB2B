<?php
/**
 * @package     apmProject\modules\misc
 */
class CAgreement extends apm_Core_BaseObject {
	public $agreement_id = null;
	public $agreement_name = null;
	public $agreement_date = null;		
	public $agreement_place = null;
	public $agreement_project = null;
	public $agreement_task = null;
	public $agreement_start_date = null;
	public $agreement_end_date = null;
	public $agreement_payment_type = null;
	public $agreement_payment_amount = null;
	public $agreement_parties_owner = null;	
	public $agreement_parties_client = null;
	public $agreement_template = null;
	public $agreement_source = null;
	public $agreement_category = null;
	public $agreement_priority = null;
	public $sign_src = null;
	public $agreement_description = null;
	public $agreement_owner = null;
	public $agreement_create_date = null;
	public $sign_u = null;
	public $sign_ued = null;
	
	public function __construct() {
		parent::__construct ( 'agreements', 'agreement_id' );
	}
	public function getAgreementsByCategory($notUsed = null, $category_id = 0, $search = '', $userid=1) {
		$q = $this->_getQuery ();
		$q->addTable ( 'agreements','a' );
		$q->addQuery ( 'a.*' );
		
		if ($search != '') {
			$q->addWhere ( '(a.agreement_name LIKE \'%' . $search . '%\' OR a.agreement_description LIKE \'%' . $search . '%\')' );
		}
		
		if ($category_id >= 0) { // Category
			$q->addWhere ( 'a.agreement_category = ' . $category_id );
		}

		$q->addOrder ( 'a.agreement_name' );
		return $q->loadList ();
	}

	public function getAgreementById($agreement_id = 0) {

		if(is_numeric($agreement_id)) 
		$where='agreement_id = ' . $agreement_id;
		else
		$where='agreement_id = IN('.$agreement_id.')';

		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'agreements' );
		$q->addWhere($where);
		return $q->loadList ();
	}	
	

	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->agreement_name )) {
			$this->_error ['agreement_name'] = $baseErrorMsg . 'agreement name is not set';
		}
		
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		$this->agreement_create_date = $q->dbfnNowWithTZ ();
		$this->agreement_owner = ( int ) $this->agreement_owner ? $this->agreement_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'agreements';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'agreements';
		$search ['table_key'] = 'agreement_id'; // primary key in searched table
		$search ['table_agreement'] = 'index.php?m=agreements&a=addedit&agreement_id='; // first part of agreement
		$search ['table_title'] = 'Agreements';
		$search ['table_orderby'] = 'agreement_name';
		$search ['search_fields'] = array(
				'l.agreement_name',
				'l.agreement_description' 
		);
		$search ['display_fields'] = $search ['search_fields'];
		return $search;
	}
	
	function sourceInterpreter($source, $markerData)
	{
			$document = new DOMDocument();
			$document->loadHTML(mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8'));
			//$document->loadHTML($source);
			$labels= $document->getElementsByTagName('label');
			$labelsCount= $labels->length;
			
			$sysVals=array(
				'agreement_category' => apmgetSysVal('AgreementCategory'),
				'agreement_paymnet_type' => apmgetSysVal('AgreementPaymentType'),
				'agreement_priority' => apmgetSysVal ('ProjectPriority'),
			);
			
			//print_r($sysVals['agreement_category']); exit;
			$markerData['agreement_priority'] = lcfirst($sysVals['agreement_priority'][$markerData['agreement_priority']]);
			$markerData['agreement_category'] = lcfirst($sysVals['agreement_category'][$markerData['agreement_category']]);
			$markerData['agreement_paymnet_type'] = lcfirst($sysVals['agreement_paymnet_type'][$markerData['agreement_paymnet_type']]);

			$markerData['sign_u'] = implode(', ', loadModal('contacts', $markerData['sign_u']));
			
			
			$cc=new CCompany();
			if(!(empty($markerData['invoice_parties_owner'])&&empty($markerData['invoice_parties_client'])))
				$parties=trim(join(',',array($markerData['invoice_parties_owner'],$markerData['invoice_parties_client'])),",");
			$obj = new CCompany ();
			$q = new apm_Database_Query ();
			$q->addTable('companies');
			$q->addQuery ( '*' );
			if(!empty($parties)) $q->addWhere ( 'company_id IN ('.$parties.')');
			$res = $q->loadList ();
				
			$markerData['agreement_parties_owner']=$res[0]['company_name'] .'<br>'. $res[0]['company_address1'].', '.$res[0]['company_zip'].' '.$res[0]['company_city'].', '.$this->_AppUI->_('company_tin').': '.$res[0]['company_tin'];
			$markerData['agreement_parties_client']=$res[1]['company_name'].'<br>'.$res[1]['company_address1'].', '.$res[1]['company_zip'].' '.$res[1]['company_city'].', '.$this->_AppUI->_('company_tin').': '.$res[1]['company_tin'];
				
			$markerData['agreement_project'] = implode(', ', loadModal('projects', $markerData['agreement_project']));
			$markerData['agreement_task'] = implode(', ', loadModal('tasks', $markerData['agreement_task']));
			
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
