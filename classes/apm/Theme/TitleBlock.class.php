<?php

/**
 * @package     apmProject\theme
 */
class apm_Theme_TitleBlock {
	/**
	 *
	 * @var string The main title of the page
	 */
	public $title = '';
	/**
	 *
	 * @var string The name of the icon used to the left of the title
	 */
	public $icon = '';
	/**
	 *
	 * @var string The name of the module that this title block is displaying in
	 */
	public $module = '';
	/**
	 *
	 * @var array An array of the table 'cells' to the right of the title block and for bread-crumbs
	 */
	public $cells = null;
	protected $_AppUI = null;
	protected $_apmconfig = null;
	/**
	 * The constructor
	 *
	 * Assigns the title, icon, module and help reference. If the user does not
	 * have permission to view the help module, then the context help icon is
	 * not displayed.
	 */
	public function __construct($title, $icon = '', $module = '') {
		global $AppUI;
		$this->_AppUI = $AppUI;
		global $apmconfig;
		$this->_apmconfig = $apmconfig;
		
		$this->title = $title;
		$this->icon = $icon;
		$this->module = $module;
		$this->cells1 = array ();
		$this->cells2 = array ();
		$this->crumbs = array ();
		$this->showhelp = canView ( 'help' );
		$this->count = 0;
	}
	/**
	 * Adds a table 'cell' beside the Title string
	 *
	 * Cells are added from left to right.
	 */
	public function addCell($data = '', $attribs = '', $prefix = '', $suffix = '') {
		$this->cells1 [] = array (
				$attribs,
				$data,
				$prefix,
				$suffix 
		);
	}
	public function getModuleIcon() {
	}
	public function addSearchCell($search) {
		$this->addCell ( '<form name="searchform" action="?m=' . $this->module . '" method="post" accept-charset="utf-8" role="form">
				<div class="form-group">
                <label for="search">' . $this->_AppUI->_ ( 'Search' ) . '</label>
				<input type="text" class="form-control" id="search" name="search_string" value="' . $search . '" />
				<input class="btn btn-info" type="submit" value="szukaj">
				</div>
				</form>
				' );
	}
	public function addFilterCell($label, $field, $values, $value) {
		$form = 'filter' . $this->count;
		
		$this->addCell ( '<form action="?m=' . $this->module . '" method="post" name="' . $form . '" accept-charset="utf-8" role="form">
		<div class="form-group">
		<label for="select' . $form . '">' . $this->_AppUI->_ ( $label ) . '</label>		
		' . arraySelect ( $values, $field, 'size="1" class="form-control" id="select' . $form . '" onChange="document.' . $form . '.submit();"', $value, false ) . '
		</div>
		</form>' );
		
		$this->count ++;
	}
	public function addButton($label, $url) {
		$button = '<input type="submit" class="btn btn-success" value="' . $this->_AppUI->_ ( $label ) . '">';
		$form = '<form action="' . $url . '" method="post" accept-charset="utf-8" role="form">' . $button . '</form>';
		
		$this->addCell ( $form );
	}
	public function addCustomCell($content) {
		$this->addCell ( $content );
	}
	
	/**
	 * Adds a table 'cell' to left-aligned bread-crumbs
	 *
	 * Cells are added from left to right.
	 */
	public function addCrumb($link, $label, $icon = '', $button = TRUE) {
		$this->crumbs [] = array (
				$link,
				$label,
				$icon,
				$button 
		);
	}
	
	/**
	 *
	 * @param
	 *        	$m
	 * @param
	 *        	$id
	 * @param string $a        	
	 */
	public function addViewLink($module, $key, $a = 'view') {
		if ($key) {
			$this->addCrumb ( '?m=' . apm_pluralize ( $module ) . '&a=' . $a . '&' . $module . '_id=' . $key, 'view this ' . $module );
		}
	}
	
	/**
	 * Adds a table 'cell' to the right-aligned bread-crumbs
	 *
	 * Cells are added from left to right.
	 */
	public function addCrumbRight($data = '', $attribs = '', $prefix = '', $suffix = '') {
		$this->cells2 [] = array (
				$attribs,
				$data,
				$prefix,
				$suffix 
		);
	}
	/**
	 * Creates a standarised, right-aligned delete bread-crumb and icon.
	 */
	public function addCrumbDelete($title, $canDelete = '', $msg = '') {
		$this->addCrumb ( 'javascript:delIt()', $this->_AppUI->_ ( $title ) );
	}
	/**
	 * The drawing function
	 */
	public function show($navbar = true) {
		global $a;
		$m = $this->module;
		$this->loadExtraCrumbs ( $m, $a );
		$s = '';
		
		$s .= '<div class="mainbox">
        <div class="panel panel-info">       
        <div class="panel-heading">
        <div class="panel-title">' . $this->_AppUI->_ ( $this->title ) . '</div></div>
        <div class="panel-body">';
		
		/*
		 * if ($this->icon) {
		 * $s .= '<div class="icon">';
		 * $s.=apmshowIcon($this->icon);
		 *
		 * $s .= apmshowImage($this->icon, '', '', '', '', $this->module);
		 * $s .= '</div>';
		 * }
		 */
		// $s .= '<h1>' . $this->_AppUI->_($this->title) . '</h1>';
		
		if ($this->cells1) {
			
			$s .= '<div class="modal fade" id="tabToolsModal" tabindex="-1" role="dialog" aria-labelledby="tabToolsModalLabel" aria-hidden="true">
		<div class="modal-dialog"><div class="modal-content"><div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="' . $this->_AppUI->_ ( "Close" ) . '"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">' . $this->_AppUI->_ ( "Utilities for tab" ) . ' "' . $this->_AppUI->_ ( $this->title ) . '"</h4>
		</div><div class="modal-body">';
			
			foreach ( $this->cells1 as $c ) {
				if ('' == $c [1]) {
					continue;
				}
				$s .= $c [2] ? $c [2] : '';
				$s .= '<div class="row" ' . ($c [0] ? (' ' . $c [0]) : '') . '>';
				$s .= $c [1] ? $c [1] : '&nbsp;';
				$s .= '</div>';
				// $s .= $c [3] ? $c [3] : '';
			}
			
			$s .= '</div><div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">' . $this->_AppUI->_ ( "Close" ) . '</button>
			</div></div></div></div>';
			
			$tool_btn = '<a type="button" class="btn-info btn-module-nav" data-original-title="' . $this->_AppUI->_ ('Filters') . '" data-container="body" data-toggle="tooltip" data-placement="right" >
			<span class="glyphicon glyphicon-cog" data-toggle="modal" data-target="#tabToolsModal"></span>
			</a>';
		}
		
		if ($navbar) {
			$s .= "<div class='module-nav' id='module-tools'>" . $tool_btn;
			
			if (count ( $this->crumbs ) || count ( $this->cells2 )) {
				$crumbs = array ();
				
				foreach ( $this->crumbs as $k => $v ) {
					if ($v [3]) {
						$img = 

						(! empty ( $v [2] )) ? ($v [2] [0] == 'glyphicon') ? '<span class="glyphicon-th-list glyphicon glyphicon-' . $v [2] [1] . '"></span>' : '<img src="' . apmfindImage ( $v [2], $this->module ) . '" />&nbsp;' : '';
						$t = $img . ucfirst($this->_AppUI->_ ( $v [1] ));
						$attr = ($v [0] [0] == 'custom') ? $v [0] [1] : ' href="' . $v [0] . '"';
						$crumbs [] = '<a class="btn btn-default" role="button" type="button" ' . $attr . '>' . $t . '</a>';
					} else {
						$crumbs [] = $v [1];
					}
				}
				
				$s .= '<div class="crumb" style="float:left;"><span>' . implode ( '', $crumbs ) . '</span></div>';
				if (count ( $this->cells2 )) {
					$s .= '<div class="right crumb-right"><ul>';
					foreach ( $this->cells2 as $c ) {
						$s .= $c [2] ? $c [2] : '';
						$s .= '<li ' . ($c [0] ? " $c[0]" : '') . '>';
						$s .= $c [1] ? $c [1] : '&nbsp;';
						$s .= '</li>';
						$s .= $c [3] ? $c [3] : '';
					}
					$s .= '</ul></div>';
				}
			}
			
			$s .= '</div>';
		}
		echo $s;
	}
	
	
	

	public function loadExtraCrumbs($module, $file = null) {
		if (! isset ( $_SESSION ['all_crumbs'] ) || ! isset ( $_SESSION ['all_crumbs'] [$module] )) {
			return false;
		}
		
		if ($file) {
			if (isset ( $_SESSION ['all_crumbs'] [$module] [$file] ) && is_array ( $_SESSION ['all_crumbs'] [$module] [$file] )) {
				$crumb_array = &$_SESSION ['all_crumbs'] [$module] [$file];
			} else {
				return false;
			}
		} else {
			$crumb_array = &$_SESSION ['all_crumbs'] [$module];
		}
		$crumb_count = 0;
		foreach ( $crumb_array as $crumb_elem ) {
			if (isset ( $crumb_elem ['module'] ) && $this->_AppUI->isActiveModule ( $crumb_elem ['module'] )) {
				$crumb_count ++;
				include $crumb_elem ['file'] . '.php';
			}
		}
		return $crumb_count;
	}
	public function findCrumbModule($crumb) {
		global $m, $a;
		
		if (! isset ( $_SESSION ['all_crumbs'] ) || ! isset ( $_SESSION ['all_crumbs'] [$m] )) {
			return false;
		}
		
		if (isset ( $a )) {
			if (isset ( $_SESSION ['all_crumbs'] [$m] [$a] ) && is_array ( $_SESSION ['all_crumbs'] [$m] [$a] )) {
				$crumb_array = &$_SESSION ['all_crumbs'] [$m] [$a];
			} else {
				$crumb_array = &$_SESSION ['all_crumbs'] [$m];
			}
		} else {
			$crumb_array = &$_SESSION ['all_crumbs'] [$m];
		}
		
		list ( $file, $name ) = $this->crumbs [$crumb];
		foreach ( $crumb_array as $crumb_elem ) {
			if (isset ( $crumb_elem ['name'] ) && $crumb_elem ['name'] == $name && $crumb_elem ['file'] == $file) {
				return $crumb_elem ['module'];
			}
		}
		return false;
	}
}