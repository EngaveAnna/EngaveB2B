<?php
/**
 * @package     apmProject\modules\misc
 */
class CAgreementTemplate extends apm_Core_BaseObject {
	public $template_id = null;
	public $template_name = null;
	public $template_date = null;
	public $template_owner = null;
	public $template_source = null;
	public $template_description = null;
	public $template_category = null;
	
	public function __construct() {
		parent::__construct ( 'agreements_templates', 'template_id', 'agreements' );
	}
	public function getTemplatesByCategory($notUsed = null, $category_id = 0, $search = '') {
		
		// SETUP FOR AGREEMENTS LIST
		$q = $this->_getQuery ();
		$q->addQuery ( 'agreements_templates.*' );
		$q->addTable ( 'agreements_templates' );
		
		if ($search != '') {
			$q->addWhere ( '(template_name LIKE \'%' . $search . '%\' OR template_description LIKE \'%' . $search . '%\')' );
		}
		
		// Category
		if ($category_id >= 0) {
			$q->addWhere ( 'template_category = ' . $category_id );
		}
		
		// Permissions
		$q->addOrder ( 'template_name' );
		
		return $q->loadList ();
	}
	
	public function getTemplatesById($Id) {
	
		// SETUP FOR AGREEMENTS LIST
		$q = $this->_getQuery ();
		$q->addTable ( 'agreements_templates' );
		$q->addQuery ( '*' );
		$q->addWhere ( 'template_id = '.$Id);
		$hash=$q->loadHash ();
		if($hash) $q->bindHashToObject ( $hash, $this, null, $strip );
	}
	
	public function canEdit() {
		return $this->_perms->checkModuleItem ( 'agreements', 'edit' );
	}
	public function canDelete() {
		return $this->_perms->checkModuleItem ( 'agreements', 'delete' );
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
		$search ['table'] = 'agreements_templates';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'agreements_templates';
		$search ['table_key'] = 'template_id'; // primary key in searched table
		$search ['table_agreement'] = 'index.php?m=agreements&submod=template&a=addedit_tpl&template_id='; // first part of agreement
		$search ['table_title'] = 'Agreements templates';
		$search ['table_orderby'] = 'template_name';
		$search ['search_fields'] = array(
				'l.template_name',
				'l.template_description');
		$search ['display_fields'] = $search ['search_fields'];
		return $search;
	}
}
