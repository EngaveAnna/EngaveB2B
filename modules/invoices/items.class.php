<?php
/**
 * @package     apmProject\modules\misc
 */
class CItem extends apm_Core_BaseObject {
	
	public $item_id = null;
	public $item_name = null;
	public $item_invoice = null;
	public $item_quantity = null;
	public $item_unit = null;
	public $item_unit_price = null;
	public $item_tax_rare = null;
	public $item_date = null;
	public $item_owner = null;
	public $item_properties = null;
	
	public function __construct() {
		parent::__construct ( 'items', 'item_id' );
	}
	public function getItemsByInvoice($invoice_id, $user_id) {
		$q = $this->_getQuery ();
		$q->addTable ( 'invoices_items' );
		$q->addQuery ( '*' );
		$q->addWhere ( 'item_owner = ' . $user_id );
		if ($invoice_id >= 0) { // Category
			$q->addWhere ( 'item_invoice = ' . $invoice_id );
		}
		$q->addOrder ( 'item_id' );
		return $q->loadList ();
	}
	
	public function delItemsByInvoice($invoice_id, $user_id)
	{
		// delete linked items invoice
		$q = $this->_getQuery ();
		$q->setDelete ( 'invoices_items' );
		$q->addWhere ( 'item_invoice = ' . $invoice_id );
		$q->addWhere ( 'item_owner = ' . $user_id );
		$q->exec ();
	}
	
	public function setItemsByInvoice($invoice_id, $user_id, $itemSchema, $itemData, $postData)
	{
		$directSruct=array('item_name','item_quantity','item_unit', 'item_unit_price','item_tax_rare' );
		$this->delItemsByInvoice($invoice_id, $user_id);
		$err=false;
		foreach($itemData as $dkey=>$dval)
		{
			$item_properties=null;
			$q = $this->_getQuery ();
			$q->addTable ('invoices_items');
			$q->addInsert('item_invoice', $invoice_id);
			$q->addInsert('item_date', $q->dbfnNowWithTZ ());
			$q->addInsert('item_owner', $user_id);		
			
			foreach($itemSchema as $key=>$val)
			{				
				if(!in_array($val['name'], $directSruct))
				$item_properties[$val['name']]=$dval[$val['name']];
				else		
				$q->addInsert($val['name'], $dval[$val['name']]);
				
				if(!preg_match($val['regex'], $dval[$val['name']]))
				{	
					echo "</br>preg_match: regex->".$val['regex']." value->".$dval[$val['name']];
					$err=true;
				}
			}

			if($err!==true)
			{	
				if(!empty($item_properties))
				$q->addInsert('item_properties', serialize($item_properties));
				$q->exec();
			}
			else
			{
				$err=false;
				$q->clear();
			}
		}
	}

	public function getItemById($item_id = 0) {

		if(is_numeric($item_id)) 
		$where='item_id = ' . $item_id;
		else
		$where='item_id = IN('.$item_id.')';

		$q = $this->_getQuery ();
		$q->addQuery ( '*' );
		$q->addTable ( 'items' );
		$q->addWhere($where);
		return $q->loadList ();
	}	
	public function isValid() {
		$baseErrorMsg = get_class ( $this ) . '::store-check failed - ';
		
		if ('' == trim ( $this->item_name )) {
			$this->_error ['item_name'] = $baseErrorMsg . 'item name is not set';
		}
		
		return (count ( $this->_error )) ? false : true;
	}
	protected function hook_preStore() {
		$q = $this->_getQuery ();
		$this->item_create_date = $q->dbfnNowWithTZ ();
		$this->item_owner = ( int ) $this->item_owner ? $this->item_owner : $this->_AppUI->user_id;
	}
	public function hook_search() {
		$search ['table'] = 'items';
		$search ['table_alias'] = 'l';
		$search ['table_module'] = 'items';
		$search ['table_key'] = 'item_id'; // primary key in searched table
		$search ['table_item'] = 'index.php?m=items&a=addedit&item_id='; // first part of item
		$search ['table_title'] = 'Items';
		$search ['table_orderby'] = 'item_name';
		$search ['search_fields'] = array(
				'l.item_name',
				'l.item_description' 
		);
		$search ['display_fields'] = $search ['search_fields'];
		return $search;
	}
	
	
	function getTableHead($itemSchema)
	{
		$r='<tr class="success">';
		//APM row numeration
		$r.='<th class="text-center">'.$this->_AppUI->_('N').'</th>';

		foreach($itemSchema as $key=>$val)
		$r.='<th class="text-center">'.$this->_AppUI->_($val['name']).'</th>';
		
		$r.='<th class="text-center" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;"></th>';
		$r.='</tr>';
		return $r;
	}
	
	function getTableBody($itemSchema, $invoice_items)
	{
		$r=null;
		$sysVals['item_tax_rare']=explode(',',apmgetConfig('item_tax_rare'));
		foreach($invoice_items as $dataKey=>$dataVal)
		{
			//APM item_properties join to invoice_items
			if($dataVal['item_properties'])
			{
				$dataVal=array_merge($dataVal, unserialize($dataVal['item_properties']));
			}
	
			$r.='<tr id="addr'.$dataKey.'" data-id="'.$dataKey.'" >';
			//APM row numeration
			$n=$dataKey+1;
			$r.='<td id="N['.$dataKey.']">'.$n.'</td>';
				
			foreach($itemSchema as $key=>$val)
			{
				switch($val['type'])
				{
					case 'select':
						$r.='<td data-name="'.$val['name'].'"><select name="invoice_items['.$dataKey.']['.$val['name'].']" class="form-control">';
						foreach($sysVals['item_tax_rare'] as $sysKey=>$sysVal)
						{
							if($sysKey==$dataVal[$val['name']]) $selected ='selected="selected"'; else $selected='';
							$r.='<option value="'.$sysKey.'" '.$selected.'>'.$sysVal.'</option>';
						}
						$r.='</select></td>';
						break;
					default:
						$r.='<td data-name="'.$val['name'].'"><input type="text" name="invoice_items['.$dataKey.']['.$val['name'].']" value="'.$dataVal[$val['name']].'" placeholder="'.$this->_AppUI->_($val['name']).'" class="form-control"/></td>';
				}
			}
	
			$r.='<td data-name="del"> <button name="del'.$dataKey.'" class="btn btn-danger glyphicon glyphicon-remove row-remove"></button></td>';
			$r.='</tr>';
		}
		return $r;
	}	
	
	function getDocTableHead($itemSchema)
	{
		$r='<tr>';
		//APM row numeration
		$r.='<td class="border_bottom left vertical_bottom" width="5%">'.$this->_AppUI->_('N').'</td>';
		foreach($itemSchema as $key=>$val)
		$r.='<td class="border_bottom '.$val['align'].' vertical_bottom" width="'.$val['width'].'"><span class="naglowek">'.$this->_AppUI->_($val['name']).'</span></td>';
		$r.='</tr>';
		return $r;
	}

	function getDocTableBody($itemSchema, $invoice_items)
	{
		$r=null;
		$sysVals['item_tax_rare']=explode(',',apmgetConfig('item_tax_rare'));
		foreach($invoice_items as $dataKey=>$dataVal)
		{
			//APM item_properties join to invoice_items
			if($dataVal['item_properties'])
			{
				$dataVal=array_merge($dataVal, unserialize($dataVal['item_properties']));
			}
	
			$r.='<tr>';
			//APM row numeration
			$n=$dataKey+1;
			$r.='<td class="border_bottom2 left vertical_bottom" width="5%">'.$n.'</td>';
				
			foreach($itemSchema as $key=>$val)
			{
				switch($val['type'])
				{
					case 'select':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.$sysVals['item_tax_rare'][$dataVal[$val['name']]].'</td>';
					break;
					case 'text':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.$dataVal[$val['name']].'</td>';
					break;
					case 'price':
							$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.number_format($dataVal[$val['name']], 2, ',', ' ').'</td>';
					break;						
					case 'item_net_val':
						$item_net_val=$dataVal['item_quantity']*$dataVal['item_unit_price'];
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.number_format($item_net_val, 2, ',', ' ').'</td>';
					break;						
				}
			}
			$r.='</tr>';
		}
		return $r;
	}
	



	function getResumTableHead($itemSchema)
	{
		$r='<tr class="text-right">';
		//APM row numeration
		foreach($itemSchema as $key=>$val)
		$r.='<td class="border_bottom '.$val['align'].' vertical_bottom" width="'.$val['width'].'"><span class="naglowek">'.$this->_AppUI->_($val['name']).'</span></td>';
		
		$r.='</tr>';
		return $r;
	}
	
	
/* 	<tr class="text-right">
	<td class="border_bottom" width="25%"> 23 %</td>
	<td class="border_bottom" width="25%"> 6&nbsp;480,00</td>
	<td class="border_bottom" width="25%"> 1&nbsp;490,40</td>
	<td class="border_bottom" width="25%"> 7&nbsp;970,40</td>
	</tr>
	
	<tr class="text-right">
	<td class="border_bottom" width="25%"> 8 %</td>
	<td class="border_bottom" width="25%"> 2&nbsp;500,00</td>
	<td class="border_bottom" width="25%"> 200,00</td>
	<td class="border_bottom" width="25%"> 2&nbsp;700,00</td>
	</tr>
	
	<tr class="text-right">
	<td class="border_bottom" width="25%"><span class="naglowek">Razem</span></td>
	<td class="border_bottom" width="25%"><span class="naglowek"> 8&nbsp;980,00 </span></td>
	<td class="border_bottom" width="25%"><span class="naglowek"> 1&nbsp;690,40 </span></td>
	<td class="border_bottom" width="25%"><span class="naglowek"> 10&nbsp;670,40 </span></td>
	</tr> */
	
	function getResumTableBody($itemSchema, $invoice_items)
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
			$r.='<tr>';
			foreach($itemSchema as $key=>$val)
			{
				switch($val['name'])
				{
					case 'resume_tax_rare':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.$sysVals['item_tax_rare'][$dataKey].'</td>';
					break;
					case 'resume_net':
						$resume['resume_net']+=$dataVal['resume_net'];
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.number_format($dataVal['resume_net'], 2, ',', ' ').'</td>';
					break;
					case 'resume_tax_val':
						$t=str_replace('%', '', $dataVal['resume_tax_rare']) / 100;
						$d= (is_numeric($t))? $t:0;
						$taxval=$dataVal['resume_net']*$d;
						$resume['resume_tax_val']+=$taxval;
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.number_format($taxval, 2, ',', ' ').'</td>';
						break;
					case 'resume_gross':
						$grossval=$taxval+$dataVal['resume_net'];
						$resume['resume_gross']+=$grossval;
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'">'.number_format($grossval, 2, ',', ' ').'</td>';
					break;
				}
			}
			$r.='</tr>';
		} 
		//APM Resume row
		if(isset($resume))
		{
			$r.='<tr>';
			foreach($itemSchema as $key=>$val)
			{
				switch($val['name'])
				{
					case 'resume_tax_rare':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'"><span class="naglowek">'.$this->_AppUI->_('resume').'</span></td>';
					break;
					case 'resume_net':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'"><span class="naglowek">'.number_format($resume['resume_net'], 2, ',', ' ').'</span></td>';
					break;
					case 'resume_tax_val':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'"><span class="naglowek">'.number_format($resume['resume_tax_val'], 2, ',', ' ').'</span></td>';
					break;
					case 'resume_gross':
						$r.='<td class="border_bottom2 '.$val['align'].' vertical_bottom" width="'.$val['width'].'"><span class="naglowek">'.number_format($resume['resume_gross'], 2, ',', ' ').'</span></td>';
					break;
				}
			}
			$r.='</tr>';
		}

		return $r;
	}	
}
