<?php
/**
 * @package     apmProject\modules\misc
 */
class CValuation extends apm_Core_BaseObject {


	public $valuation_id = null;
	public $valuation_create_date = null;
	public $valuation_name = null;
	public $valuation_category = null;
	public $valuation_owner = null;
	public $valuation_desc = null;
	public $valuation_date = null;
	public $valuation_project = null;
	public $valuation_days = null;
	public $valuation_amount = null;
	public $valuation_real_days = null;
	public $valuation_real_amount = null;	
	
	public function __construct() {
		parent::__construct ( 'valuations', 'valuation_id' );
	}
	public function getValuationsByCategory($category_id = 0, $search = '') {
		// load the following classes to retrieved denied records
		// SETUP FOR LINK LIST
		
		$q = $this->_getQuery ();
		$q->addQuery ( 'valuations.*' );
		$q->addTable ( 'valuations' );
		
		if ($search != '') {
			$q->addWhere ( '(valuation_name LIKE \'%' . $search . '%\' OR valuation_description LIKE \'%' . $search . '%\')' );
		}

		if ($category_id >= 0) {
			// Category
			$q->addWhere ( 'valuation_category = ' . $category_id );
		}
		
		// Permissions
		$q->addOrder ( 'valuation_date' );
		return $q->loadList ();
	}
	
	public function getValuationsByOrderId($order_id, $category=false) {
	
		$q = $this->_getQuery ();
		$q->addTable ( 'valuations' );
		$q->addQuery ( '*' );
		$q->addWhere ( 'valuation_order = '.$order_id );
		if($category)
		$q->addWhere ( 'valuation_category IN ('.$category.')');
		$q->addOrder ( 'valuation_date' );
		return $q->loadList ();
	}
	
	public function getValuationById($id = 0, $load=false) {
		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'valuations' );
		$q->addWhere ( 'valuation_id = ' . $id );
		if($load)
		{
			$hash=$q->loadHash();
			if($hash){
			$q->bindHashToObject ( $hash, $this, null, true );
			}
			return $q->loadList ();
		}
		else
		return $q->loadList ();
	}

	public function updateValuationByPost($valuationId, $status, $result, $paid,$tr_date) {
		$q = $this->_getQuery ();
		$q->addTable ( 'valuations' );
		$q->addUpdate('valuation_category', $status);
		$q->addUpdate('valuation_result', $result);
		$q->addUpdate('valuation_paid', $paid);
		$q->addUpdate('valuation_date', $tr_date);
		$q->addWhere ( 'valuation_id = '.$valuationId );
		return $q->exec();
	}
	
	public function createValuationHelper($settings) {
		$q = $this->_getQuery ();
		$q->addTable ( 'valuations' );
		$q->addInsert('valuation_order', $settings['valuation_order']);
		$q->addInsert('valuation_amount', $settings['valuation_amount']);
		$q->addInsert('valuation_category', $settings['valuation_category']);
		$q->addInsert('valuation_owner', $settings['valuation_owner']);
		$q->addInsert('valuation_type', $settings['valuation_type']);
		$q->addInsert('valuation_date', $settings['valuation_date']);
		$q->addInsert('valuation_name', $settings['valuation_name']);
		return $q->exec();
	}
	
	public function updateValuationHelper($settings, $id) {
		$q = $this->_getQuery ();
		$q->addTable ( 'valuations' );
		$q->addUpdate('valuation_order', $settings['valuation_order']);
		$q->addUpdate('valuation_amount', $settings['valuation_amount']);
		$q->addUpdate('valuation_owner', $settings['valuation_owner']);
		$q->addUpdate('valuation_type', $settings['valuation_type']);
		$q->addUpdate('valuation_date', $settings['valuation_date']);
		$q->addWhere ( 'valuation_id = '.$id);
		return $q->exec();
	}
	
	public function updateValuationStatus($status, $id)
	{
		$q = $this->_getQuery ();
		$q->addTable ( 'valuations' );
		$q->addUpdate('valuation_category', $status);
		$q->addWhere ( 'valuation_id = '.$id);
		return $q->exec();
	}
	

	
	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->valuation_name )) {
			$this->_error ['valuation_name'] = $baseErrorMsg . 'valuation name is not set';
		}
		
		if (!isset($this->valuation_category)) {
			$this->_error ['valuation_category'] = $baseErrorMsg . 'valuation category is not set';
		}
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		//$this->valuation_date = $q->dbfnNowWithTZ(0);
		$this->valuation_owner = ( int ) $this->valuation_owner ? $this->valuation_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'valuations';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'valuations';
		$search ['table_key'] = 'valuation_id'; // primary key in searched table
		$search ['table_valuation'] = 'index.php?m=valuations&a=addedit&valuation_id='; // first part of valuation
		$search ['table_title'] = 'Valuations';
		$search ['table_orderby'] = 'valuation_name';
		$search ['search_fields'] = array (
				'l.valuation_name',
				'l.valuation_description' 
		);
		$search ['display_fields'] = $search ['search_fields'];
		
		return $search;
	}
}
