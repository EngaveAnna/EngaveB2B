<?php

/**
 * @package     apmProject\theme
 */
abstract class apm_Theme_Base {
	protected $_AppUI = null;
	protected $_m = null;
	protected $_uistyle = 'modern';
	protected $footerJavascriptFiles = array ();
	public function __construct($AppUI, $m = '') {
		$this->_AppUI = $AppUI;
		$this->_m = $m;
	}
	public function __toString() {
		return $this->_uistyle;
	}
	public function resolveTemplate($template) {
		$filepath = apm_BASE_DIR . '/style/' . $this->_uistyle . '/' . $template . '.php';
		if (! file_exists ( $filepath )) {
			$filepath = apm_BASE_DIR . '/style/_common/' . $template . '.php';
		}
		return $filepath;
	}
	public function buildHeaderNavigation($rootTag = '', $innerTag = '', $dividingToken = '', $rootStyle = NULL, $innerStyle = NULL) {
		$s = '';
		$nav = $this->_AppUI->getMenuModules ();
		
		$links = array ();
		
		// FIXME: APMX temporary hack - menu grupping for most responsive design
		$iGroupsNames = array (
				'cr' => array (
						'name' => 'New',
						'type' => 'right',
						'icon' => 'file'
				),
				'mg' => array (
						'name' => 'Management',
						'type' => 'left' 
				),
				'tl' => array (
						'name' => 'Utils',
						'type' => 'left' 
				),
				'doc' => array (
						'name' => 'Documents',
						'type' => 'left'
				),

		);
		$navbarType = array (
				'left',
				'right' 
		);
		
		foreach ( $nav as $module ) {
			if (canAccess ( $module ['mod_directory'] )) {
				
				switch ($module ['mod_directory']) {
					case 'files':
						$iGroups ['doc'] [] = $module;
						break;
					case 'agreements':
						$iGroups ['doc'] [] = $module;
						break;
					case 'links':
						$iGroups ['doc'] [] = $module;
						break;
					case 'invoices':
						$iGroups ['doc'] [] = $module;
						break;					
					case 'users' :
						$iGroups ['mg'] [] = $module;
						break;
					case 'system' :
						$iGroups ['mg'] [] = $module;
						break;
					case 'events' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'contacts' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'forums' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'dataexchange' :
						$iGroups ['tl'] [] = $module;
						break;						
					case 'history' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'departments' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'resources' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'reports' :
						$iGroups ['tl'] [] = $module;
						break;
					case 'smartsearch' :
						$iGroups ['tl'] [] = $module;
						break;
					
					default :
						$css = null;
						$tooltip = null;
						switch ($module ['mod_directory']) {
							case 'projectdesigner' :
								$d = 'right';
								$mTitle = '<span class="nav-icon glyphicon glyphicon-' . $module ['mod_ui_text_icon'] . '"></span>';
								$tooltip = 'data-toggle="tooltip" title="' . $this->_AppUI->_ ( $module ['mod_ui_name'] ) . '" ';
								break;
							default :
								$d = 'left';
								$mTitle = $this->_AppUI->_ ( $module ['mod_ui_name'] ) . $this->_m;
								break;
						}
						// FIXME: APMX temporary hack - menu element needs id
						($_GET ['m'] == $module ['mod_directory'] && $_GET ['a'] != 'addedit' && $_GET ['a'] != 'todo') ? $active = 'class="active"' : $active = '';
						$link = ($innerTag != '') ? '<' . $innerTag . ' ' . $active . '>' : '';
						$class = ($this->_m == $module ['mod_ui_name']) ? ' class="module"' : '';
						$link .= '<a href="?m=' . $module ['mod_directory'] . '" ' . $tooltip . '' . $class . '>' . $mTitle . '</a>';
						$link .= ($innerTag != '') ? "</$innerTag>" : '';
						$links [$d] [] = $link;
						break;
				}
			}
		}
		
		// Features not available in modules directly
		$nav1 = array (
				array (
						'mod_directory' => 'tasks',
						'mod_ui_name' => 'My Tasks',
						'mod_ui_text_icon' => 'flag',
						'restActive' => 'todo',
						'restURL' => '' 
				) 
		);
		
		if (canAccess ( 'calendar' )) {
			$now = new apm_Utilities_Date ();
			
			$nav1 [] = array (
					'mod_directory' => 'calendar',
					'mod_ui_name' => 'Today',
					'mod_ui_text_icon' => 'bell',
					'restActive' => 'day_view',
					'restURL' => '&date=' . $now->format ( FMT_TIMESTAMP_DATE ) 
			);
		}
		
		foreach ( $nav1 as $module ) {
			$d = 'right';
			$mTitle = (! empty ( $module ['mod_ui_text_icon'] )) ? '<span class="nav-icon glyphicon glyphicon-' . $module ['mod_ui_text_icon'] . '"></span>' : $this->_AppUI->_ ( $module ['mod_ui_name'] );
			($_GET ['m'] == $module ['mod_directory'] && $_GET ['a'] == $module ['restActive']) ? $active = "class=\"active\"" : $active = '';
			$link = ($innerTag != '') ? '<' . $innerTag . ' ' . $active . '>' : '';
			$class = ($this->_m == $module ['mod_ui_name']) ? ' class="module"' : '';
			$link .= '<a href="?m=' . $module ['mod_directory'] . '&a=' . $module ['restActive'] . $module ['restURL'] . '"' . $class . ' data-toggle="tooltip" title="' . $this->_AppUI->_ ( $module ['mod_ui_name'] ) . '">' . $mTitle . '</a>';
			$link .= ($innerTag != '') ? "</$innerTag>" : '';
			$links [$d] [] = $link;
		}
		
		// Menu right fast add content
		if ($this->_AppUI->user_id > 0) {
			foreach ( $nav as $module ) {
				if (canAccess ( $module ['mod_directory'] )) {
					
					switch ($module ['mod_directory']) {
						case 'companies' :
							$iGroups ['cr'] [] = $module;
							break;
						case 'projects' :
							$iGroups ['cr'] [] = $module;
							break;
						case 'contacts' :
							$iGroups ['cr'] [] = $module;
							break;
						case 'events' :
							$iGroups ['cr'] [] = $module;
							break;
						case 'files' :
							$iGroups ['cr'] [] = $module;
							break;
						case 'users' :
							$iGroups ['cr'] [] = $module;
							break;
					}
				}
			}
		}
		
		foreach ( $iGroups as $key => $group ) {
			$link = null;
			$mactive = null;
			foreach ( $group as $module ) {
				
				if ($key == 'cr') {
					$module ['mod_directory'].'_id';
					if ($_GET ['m'] == $module ['mod_directory'] && $_GET['a'] == 'addedit' && (count($_GET)<=2)) {
						$active = "active";
						$mactive = 'active';
					} else {
						$active = '';
					}
					$ad = '&a=addedit';
				} else {
					if ($_GET ['m'] == $module ['mod_directory']) {
						$active = "active";
						$mactive = 'active';
					} else {
						$active = '';
					}
				}
				
				$link .= ($innerTag != '') ? "<$innerTag class=\"" . $active . "\">" : '';
				$class = ($this->_m == $module ['mod_directory']) ? ' class="module"' : '';
				$link .= '<a href="?m=' . $module ['mod_directory'] . $ad . '"' . $class . '>' . $this->_AppUI->_ ( $module ['mod_ui_name'] ) . '</a>';
				$link .= ($innerTag != '') ? "</$innerTag>" : '';
			}
			
			$mTitle = ($key == 'cr') ? '<span class="nav-icon glyphicon glyphicon-' . $iGroupsNames [$key] ['icon'] . '"></span>' : $this->_AppUI->_ ( $iGroupsNames [$key] ['name'] );
			
			switch ($iGroupsNames [$key] ['type']) {
				case 'left' :
					$g = '<li class="dropdown ' . $mactive . '"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $mTitle . '<b class="caret"></b></a><ul class="dropdown-menu">';
					$g .= $link;
					$g .= '</ul></li>';
					$links ['left'] [] = $g;
					break;
				default :
					$g = '<li class="dropdown ' . $mactive . '"><a href="#" class="dropdown-toggle" data-toggle="dropdown" title="' . $this->_AppUI->_ ( $iGroupsNames [$key] ['name'] ) . '">' . $mTitle . '<b class="caret"></b></a><ul class="dropdown-menu">';
					$g .= $link;
					$g .= '</ul></li>';
					$links ['right'] [] = $g;
					break;
			}
		}
		
		$s = null;
		foreach ( $navbarType as $nt ) {
			$rStyle = ($rootStyle) ? "class=\"" . $rootStyle . " navbar-" . $nt . "\"" : "";
			$s .= ($rootTag != '') ? "<$rootTag " . $rStyle . " id=\"headerNav\">" : '';
			$s .= implode ( $dividingToken, $links [$nt] );
			$s .= ($rootTag != '') ? "</$rootTag>" : '';
		}
		return $s;
	}
	public function messageHandler($reset = true) {
		return $this->_AppUI->getMsg ( $reset );
	}
	public function styleRenderBoxTop() {
		global $currentInfoTabId;
		if ($currentInfoTabId) {
			return '';
		}
		
		$ret = '';
		return $ret;
	}
	public function styleRenderBoxBottom($tab = 0) {
		if (- 1 == $tab) {
			return '';
		}
		
		$ret = '';
		return $ret;
	}
	
	/**
	 * Find and add to output the file tags required to load module-specific
	 * javascript.
	 */
	public function loadHeaderJS() {
		global $m, $a;
		
		// load the js base.php
		include apmgetConfig ( 'root_dir' ) . '/js/base.php';
		
		// Search for the javascript files to load.
		if (! isset ( $m )) {
			return;
		}
		$root = apm_BASE_DIR;
		if (substr ( $root, - 1 ) != '/') {
			$root .= '/';
		}
		
		$base = apm_BASE_URL;
		if (substr ( $base, - 1 ) != '/') {
			$base .= '/';
		}
		// Load the basic javascript used by all modules.
		echo '<script type="text/javascript" src="' . $base . 'js/base.js"></script>';
		
		$this->getModuleJS ( $m, $a, true );
	}
	public function getModuleJS($module, $file = null, $load_all = false) {
		$root = apm_BASE_DIR;
		if (substr ( $root, - 1 ) != '/') {
			$root .= '/';
		}
		$base = apm_BASE_URL;
		if (substr ( $base, - 1 ) != '/') {
			$base .= '/';
		}
		if ($load_all || ! $file) {
			if (file_exists ( $root . 'modules/' . $module . '/' . $module . '.module.js' )) {
				echo '<script type="text/javascript" src="' . $base . 'modules/' . $module . '/' . $module . '.module.js"></script>';
			}
		}
		if (isset ( $file ) && file_exists ( $root . 'modules/' . $module . '/' . $file . '.js' )) {
			echo '<script type="text/javascript" src="' . $base . 'modules/' . $module . '/' . $file . '.js"></script>';
		}
	}
	public function addFooterJavascriptFile($pathTo) {
		if (! in_array ( $pathTo, $this->footerJavascriptFiles )) {
			$base = apm_BASE_URL;
			if (substr ( $base, - 1 ) != '/') {
				$base .= '/';
			}
			if (strpos ( $pathTo, $base ) === false) {
				$pathTo = $base . $pathTo;
			}
			$this->footerJavascriptFiles [] = $pathTo;
		}
	}
	public function loadFooterJS() {
		$s = '<script type="text/javascript">';
		$s .= '$(document).ready(function() {';
		// Move the focus to the first textbox available, while avoiding the "Global Search..." textbox
		if (canAccess ( 'smartsearch' )) {
			$s .= '    $("input[type=\'text\']:eq(1)").focus();';
		} else {
			$s .= '    $("input[type=\'text\']:eq(0)").focus();';
		}
		$s .= '});';
		$s .= '</script>';
		
		if (is_array ( $this->footerJavascriptFiles ) and ! empty ( $this->footerJavascriptFiles )) {
			while ( $jsFile = array_pop ( $this->footerJavascriptFiles ) ) {
				$s .= "<script type='text/javascript' src='" . $jsFile . "'></script>";
			}
		}
		
		return $s;
	}
	public function loadCalendarJS() {
		global $AppUI;
		
		$s = '<style type="text/css">@import url(' . apm_BASE_URL . '/lib/jscalendar/skins/aqua/theme.css);</style>';
		$s .= '<script type="text/javascript" src="' . apm_BASE_URL . '/js/calendar.js"></script>';
		$s .= '<script type="text/javascript" src="' . apm_BASE_URL . '/lib/jscalendar/calendar.js"></script>';
		if (file_exists ( apmgetConfig ( 'root_dir' ) . '/lib/jscalendar/lang/calendar-' . $AppUI->user_locale . '.js' )) {
			$s .= '<script type="text/javascript" src="' . apm_BASE_URL . '/lib/jscalendar/lang/calendar-' . $AppUI->user_locale . '.js"></script>';
		} else {
			$s .= '<script type="text/javascript" src="' . apm_BASE_URL . '/lib/jscalendar/lang/calendar-en.js"></script>';
		}
		$s .= '<script type="text/javascript" src="' . apm_BASE_URL . '/lib/jscalendar/calendar-setup.js"></script>';
		echo $s;
		include apmgetConfig ( 'root_dir' ) . '/js/calendar.php';
	}
}