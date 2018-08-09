<?php
/**
 * @package     apmProject\modules\misc
 */
class CPayment extends apm_Core_BaseObject {
	public $payment_id = null;
	public $payment_name = null;
	public $payment_description = null;
	public $payment_owner = null;
	public $payment_date = null;
	public $payment_category = null;
	public $payment_order = null;
	public $payment_amount = null;
	public $payment_type = null;
	public $payment_paid = null;
	public $payment_result = null;

	
	public function __construct() {
		parent::__construct ( 'payments', 'payment_id' );
	}
	public function getPaymentsByCategory($category_id = 0, $search = '') {
		// load the following classes to retrieved denied records
		// SETUP FOR LINK LIST
		
		$q = $this->_getQuery ();
		$q->addQuery ( 'payments.*' );
		$q->addTable ( 'payments' );
		
		if ($search != '') {
			$q->addWhere ( '(payment_name LIKE \'%' . $search . '%\' OR payment_description LIKE \'%' . $search . '%\')' );
		}

		if ($category_id >= 0) {
			// Category
			$q->addWhere ( 'payment_category = ' . $category_id );
		}
		
		// Permissions
		$q->addOrder ( 'payment_date' );
		return $q->loadList ();
	}
	
	public function getPaymentsByOrderId($order_id, $category=false) {
	
		$q = $this->_getQuery ();
		$q->addTable ( 'payments' );
		$q->addQuery ( '*' );
		$q->addWhere ( 'payment_order = '.$order_id );
		if($category)
		$q->addWhere ( 'payment_category IN ('.$category.')');
		$q->addOrder ( 'payment_date' );
		return $q->loadList ();
	}
	
	public function getPaymentById($id = 0, $load=false) {
		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'payments' );
		$q->addWhere ( 'payment_id = ' . $id );
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

	public function updateInvoicePayStatus($invoiceId, $status) {
		$q = $this->_getQuery ();
		$q->addTable ( 'invoices' );
		$q->addUpdate('invoice_paystatus', $status);
		$q->addWhere ( 'invoice_id = '.$invoiceId );
		return $q->exec();
	}
	
	public function updatePaymentByPost($paymentId, $status, $result, $paid,$tr_date) {
		$q = $this->_getQuery ();
		$q->addTable ( 'payments' );
		$q->addUpdate('payment_category', $status);
		$q->addUpdate('payment_result', $result);
		$q->addUpdate('payment_paid', $paid);
		$q->addUpdate('payment_date', $tr_date);
		$q->addWhere ( 'payment_id = '.$paymentId );
		return $q->exec();
	}
	
	public function createPaymentHelper($settings) {
		$q = $this->_getQuery ();
		$q->addTable ( 'payments' );
		$q->addInsert('payment_order', $settings['payment_order']);
		$q->addInsert('payment_amount', $settings['payment_amount']);
		$q->addInsert('payment_category', $settings['payment_category']);
		$q->addInsert('payment_owner', $settings['payment_owner']);
		$q->addInsert('payment_type', $settings['payment_type']);
		$q->addInsert('payment_date', $settings['payment_date']);
		$q->addInsert('payment_name', $settings['payment_name']);
		return $q->exec();
	}
	
	public function updatePaymentHelper($settings, $id) {
		$q = $this->_getQuery ();
		$q->addTable ( 'payments' );
		$q->addUpdate('payment_order', $settings['payment_order']);
		$q->addUpdate('payment_amount', $settings['payment_amount']);
		$q->addUpdate('payment_owner', $settings['payment_owner']);
		$q->addUpdate('payment_type', $settings['payment_type']);
		$q->addUpdate('payment_date', $settings['payment_date']);
		$q->addWhere ( 'payment_id = '.$id);
		return $q->exec();
	}
	
	public function updatePaymentStatus($status, $id)
	{
		$q = $this->_getQuery ();
		$q->addTable ( 'payments' );
		$q->addUpdate('payment_category', $status);
		$q->addWhere ( 'payment_id = '.$id);
		return $q->exec();
	}
	
	public function getCompanyPaymentByInviceId($id)
	{
		$inv=new CInvoice();
		$invoice=$inv->getInvoiceById($id);
		$cmp=new CCompany();
		return $cmp->getCompanyPayment($invoice[0]['invoice_parties_owner']);
	}
	
	
	public function getPaymantCalculate($order_id)
	{
		
		$res=$this->getPaymentsByOrderId($order_id, '2,3,4');
		if(!empty($res))
		{
			$sum=0;
			foreach($res as $key => $val)
			{
				switch($val['payment_type'])
				{
					case 0:
						$sum+=$val['payment_paid'];
						break;
					case 1:
						$sum-=$val['payment_paid'];
						break;
				}
			}
			return $sum;
		}
		else
		return 0;
	}
	
	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->payment_name )) {
			$this->_error ['payment_name'] = $baseErrorMsg . 'payment name is not set';
		}
		
		if (!isset($this->payment_type)) {
			$this->_error ['payment_type'] = $baseErrorMsg . 'payment type is not set';
		}
	
		if (!is_numeric($this->payment_amount)||$this->payment_amount<0) {
			$this->_error ['payment_amount'] = $baseErrorMsg . 'payment amount must be a possitive number';
		}
			
		if (!is_numeric($this->payment_paid)||$this->payment_paid<0) {
			$this->_error ['payment_paid'] = $baseErrorMsg . 'paid must be a possitive number';
		}
		
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		//$this->payment_date = $q->dbfnNowWithTZ(0);
		$this->payment_owner = ( int ) $this->payment_owner ? $this->payment_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'payments';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'payments';
		$search ['table_key'] = 'payment_id'; // primary key in searched table
		$search ['table_payment'] = 'index.php?m=payments&a=addedit&payment_id='; // first part of payment
		$search ['table_title'] = 'Payments';
		$search ['table_orderby'] = 'payment_name';
		$search ['search_fields'] = array (
				'l.payment_name',
				'l.payment_description' 
		);
		$search ['display_fields'] = $search ['search_fields'];
		
		return $search;
	}
}
