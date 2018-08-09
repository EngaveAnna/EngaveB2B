<?php
/**
 * @package     apmProject\modules\misc
 */
class CInvoiceTemplate extends apm_Core_BaseObject {
	public $template_id = null;
	public $template_name = null;
	public $template_date = null;
	public $template_owner = null;
	public $template_source = null;
	public $template_description = null;
	public $template_category = null;
	
	public function __construct() {
		parent::__construct ( 'invoices_templates', 'template_id', 'invoices' );
	}
	public function getTemplatesByCategory($notUsed = null, $category_id = 0, $search = '') {
		
		// SETUP FOR AGREEMENTS LIST
		$q = $this->_getQuery ();
		$q->addQuery ( 'invoices_templates.*' );
		$q->addTable ( 'invoices_templates' );
		
		if ($search != '') {
			$q->addWhere ( '(template_name LIKE \'%' . $search . '%\' OR template_description LIKE \'%' . $search . '%\')' );
		}

		if ($category_id >= 0) { // Category
			$q->addWhere ( 'template_category = ' . $category_id );
		}
		// Permissions
		$q->addOrder ( 'template_name' );
		
		return $q->loadList ();
	}
	
	public function getTemplatesById($Id, $doHash=true) {
	
		// SETUP FOR AGREEMENTS LIST
		$q = $this->_getQuery ();
		$q->addTable ( 'invoices_templates' );
		$q->addQuery ( '*' );
		$q->addWhere ( 'template_id = '.$Id);
		if($doHash)
		{			
			$hash=$q->loadHash ();
			if($hash)
			$q->bindHashToObject ( $hash, $this, null, $strip );
		}
		else
		{
			return $q->loadList ();
		}
	}
	
	public function canEdit() {
		return $this->_perms->checkModuleItem ( 'invoices', 'edit' );
	}
	public function canDelete() {
		return $this->_perms->checkModuleItem ( 'invoices', 'delete' );
	}		
	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->template_name )) {
			$this->_error ['template_name'] = $baseErrorMsg . 'template name is not set';
		}
		
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		$this->template_date = $q->dbfnNowWithTZ ();
		$this->template_owner = ( int ) $this->template_owner ? $this->template_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'invoices_templates';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'invoices_templates';
		$search ['table_key'] = 'template_id'; // primary key in searched table
		$search ['table_invoice'] = 'index.php?m=invoices&submod=template&a=addedit_tpl&template_id='; // first part of invoice
		$search ['table_title'] = 'Invoices templates';
		$search ['table_orderby'] = 'template_name';
		$search ['search_fields'] = array(
				'l.template_name',
				'l.template_description');
		$search ['display_fields'] = $search ['search_fields'];
		return $search;
	}
}
