<?php
/**
 * @package     apmProject\modules\misc
 */
class CSignature extends apm_Core_BaseObject {
            public $signature_id = null;
			public $signature_mod = null;
			public $signature_name = null;
			public $signature_row = null;
			public $signature_source = null;
			public $signature_owner = null;
			public $signature_date = null;
			public $signature_category = null;
			public $signature_description = null;
	public function __construct() {
		parent::__construct ( 'signatures', 'signature_id' );
	}
	public function getSignaturesByMod($mod_id = '', $search = '') {

		// SETUP FOR LINK LIST
		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'signatures' );
		
		if ($mod_id != '') {
			$q->addWhere ( 'signature_mod = ' . $mod_id);
		}

		$q->addOrder ( 'signature_date' );
		return $q->loadList ();
	}
	
	public function getSignatureById($id = '') {
		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'signatures' );
	
		if ($id != '') {
			$q->addWhere ( 'signature_id = ' . $id);
		}
	
		$q->addOrder ( 'signature_date' );
		return $q->loadList ();
	}
	
	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->signature_mod )) {
			$this->_error ['signature_mod'] = $baseErrorMsg . 'signature mod id is not set';
		}
		if ('' == trim ( $this->signature_row )) {
			$this->_error ['signature_row'] = $baseErrorMsg . 'signature row id is not set';
		}
		return (count ( $this->_error )) ? false : true;
	}
	
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		$this->signature_date = $q->dbfnNowWithTZ ();
		$this->signature_name = md5(time());
		$this->signature_owner = ( int ) $this->signature_owner ? $this->signature_owner : $this->_AppUI->user_id;
	}
	
	public function hook_search() {
		$search ['table'] = 'signatures';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'signatures';
		$search ['table_key'] = 'signature_id'; // primary key in searched table
		$search ['table_signature'] = 'index.php?m=signatures&a=addedit&signature_id='; // first part of signature
		$search ['table_title'] = 'Signatures';
		$search ['search_fields'] = array (
				'l.signature_description',
				'l.signature_source'
		);
		$search ['display_fields'] = $search ['search_fields'];
		return $search;
	}
	
	public function getModuleName($mod_directory=null, $mod_id=null)
	{
		$q = $this->_getQuery ();
		$q->addQuery ( 'mod_id, mod_directory' );
		$q->addTable ( 'modules' );
		$q->addWhere ( 'mod_active = 1' );
		if($mod_directory)
		{
			$q->addWhere ( 'mod_directory = \''.strtolower($mod_directory).'\'' );
			$ret=$q->loadList();
			return $ret[0]['mod_id'];
			
		}
		if($mod_id)
		{
			$q->addWhere ( 'mod_id = '.$mod_id );
			$ret=$q->loadList();
			return $ret[0]['mod_directory'];
		}
		return $q->loadHashList();
	}
	
	public function getSignData($signature_mod_name, $signature_row)
	{
		$prefix=$this->_getColumnPrefixFromTableName($signature_mod_name);
		$q = $this->_getQuery ();
		$q->addQuery ( 'sign_src' );
		$q->addTable ( $signature_mod_name );
		$q->addWhere ( $prefix.'_id='.$signature_row);
		return $q->loadList();
	}
	
	public function getPreviewPrefix($signature_mod_name)
	{
		return $this->_getColumnPrefixFromTableName($signature_mod_name);
	}
	
	
	public function getRowById($mod_name, $id)
	{
		$prefix=strtolower($this->_getColumnPrefixFromTableName($mod_name));
		$q = $this->_getQuery ();
		$q->addTable ( $mod_name );
		$q -> addQuery('*');
		$q->addWhere ( $prefix.'_id = '.$id );
		return $q->loadList();
	}
	
	public function getUedField($mod_name, $id)
	{
		$prefix=strtolower($this->_getColumnPrefixFromTableName($mod_name));
		$q = $this->_getQuery ();
		$q->addTable ( $mod_name );
		$q -> addQuery('sign_ued');
		$q->addWhere ( $prefix.'_id = '.$id );
		$get= $q->loadHashList();
		return $get[0]['sign_ued'];
	}
	
	public function setUedField($mod_name, $id, $ued)
	{
		$prefix=strtolower($this->_getColumnPrefixFromTableName($mod_name));
		$q = $this->_getQuery ();
		$q->addTable ( $mod_name );
		$q -> addUpdate('sign_ued', $ued);
		$q->addWhere ( $prefix.'_id = '.$id );
		return $q->exec();
	}
	
	public function setRowStatus($mod_name, $id, $statsFieldName, $sign_u, $sign_ued)
	{
		
		if(empty($sign_ued)||empty($sign_u))
		{
			$status=0;
		}
		else 
		{
			$su=explode(',',$sign_u);
			asort($su);
			$sued=explode(',',$sign_ued);
			asort($sued);
			$sign_u=join(',', $su);
			$sign_ued=join(',', $sued);
			
			if($sign_ued==$sign_u)
			$status=3;
			else
			$status=2;	
		}
			
		$prefix=strtolower($this->_getColumnPrefixFromTableName($mod_name));
		$q = $this->_getQuery ();
		$q->addTable ( $mod_name );
		$q -> addUpdate($prefix.'_'.$statsFieldName, $status);
		$q->addWhere ( $prefix.'_id = '.$id );
		return $q->exec();
	}
	
	public function postSignData($signature_name, $mod_name, $mod_id, $row, $owner, $income)
	{
		$prefix=strtolower($this->_getColumnPrefixFromTableName($mod_name));
		$q = $this->_getQuery ();
		$q->addTable ( 'signatures' );
		$q->addInsert('signature_mod', $mod_id);
		$q->addInsert('signature_name', $signature_name);
		$q->addInsert('signature_row', $row);
		$q->addInsert('signature_source', $income);
		$q->addInsert('signature_owner', $owner);
		$q->addInsert('signature_date', $q->dbfnNowWithTZ ());
		
		if (! $q->exec())
		{
			$error_msg = $db->ErrorMsg ();
			$q->clear ();
			return false;
		} 
		else 
		{
			$q->clear();
			return ($this->signUpdateRelations($mod_name, $row, $owner, 'category', 'add'))? true : false;	
		}
	}
	
	public function signUpdateRelations($mod_name, $id, $owner, $statusFieldName, $utype)
	{
		if(!$row=$this->getRowById($mod_name, $id))
		return false;

		switch($utype)
		{
			case 'add':
				if($row[0]['sign_ued']!=null)
				{
					if(!is_numeric($row[0]['sign_ued']))
					{
						$ued=explode(',',$row[0]['sign_ued']);
						if(!in_array($owner, $ued)) {
							$ued[]=$owner;
						}
					}
					else
					{
						if($row[0]['sign_ued']!=$owner)
						$ued=array($row[0]['sign_ued'], $owner);
						else 
						$ued[]=$owner;
					}
					
					asort($ued);
					$ued=implode(",", $ued);
				}
				else
				$ued=$owner;
			break;
			case 'del':
				if(!is_numeric($row[0]['sign_ued']))
				{
					$ued=explode(',',$row[0]['sign_ued']);
					if(($key = array_search($owner, $ued)) !== false) {
				    	unset($ued[$key]);
					}
					asort($ued);
					$ued=implode(",", $ued);
				}
				else
				$ued=null;				
			break;					
		}
	
		return ($this->setUedField($mod_name, $id, $ued)&&$this->setRowStatus($mod_name, $id, $statusFieldName, $row[0]['sign_u'], $ued))? true : false;		
	}
	
	public function getSignatureBySpecData($mod_name,$row,$owner)
	{
		$mod_id=$this->getModuleName($mod_name, null);
		
		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'signatures' );
		
		if ($mod_name != '')
		$q->addWhere ( 'signature_mod = ' . $mod_id);

		if ($row != '')
		$q->addWhere ( 'signature_row = ' . $row);
		
		if ($owner != '')
		$q->addWhere ( 'signature_owner = ' . $owner);
		
		$q->addOrder ( 'signature_date' );
		return $q->loadList ();		
	}	

}
