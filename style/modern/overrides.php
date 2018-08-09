<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
class style_modern extends apm_Theme_Base {
}

/**
 * This overrides the show function of the CTabBox_core function
 */
class CTabBox extends apm_Theme_TabBox {
	public function show($extra = '', $js_tabs = false, $alignment = 'left', $opt_flat = true) {
		$this->loadExtras ( $this->m, $this->a );
		
		if (($this->a == 'addedit' || $this->a == 'view' || $this->a == 'viewuser')) {

		}
		
		reset ( $this->tabs );
		$s = '';
		// tabbed / flat view options
		if ($this->_AppUI->getPref ( 'TABVIEW' ) == 0) {
			if ($opt_flat) {
				// $s .= '<table border="0" cellpadding="2" cellspacing="0" width="100%">';
				// $s .= '<tr>';
				// $s .= '<td width="54" nowrap="nowrap">';
				// $s .= '<a class="btn btn-default" href="' . $this->baseHRef . 'tab=0"><div>' . $this->_AppUI->_('tabbed') . '</div></a>';
				// $s .= '</td>';
				// $s .= '<td nowrap="nowrap">';
				// $s .= '<a class="btn btn-default" href="' . $this->baseHRef . 'tab=-1"><div>' . $this->_AppUI->_('flat') . '</div></a>';
				// $s .= '</td>' . $extra . '</tr></table>';
				// echo $s;
			}
		} else {
			if ($extra) {
				echo '<table border="0" cellpadding="2" cellspacing="0" width="100%"><tr>' . $extra . '</tr>' . '</table>';
			}
		}
		
		if ($this->active < 0 || $this->_AppUI->getPref ( 'TABVIEW' ) == 2) {
			// flat view, active = -1
			echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';
			foreach ( $this->tabs as $k => $v ) {
				echo '<tr><td><strong>' . ($v [2] ? $v [1] : $this->_AppUI->_ ( $v [1] )) . '</strong></td></tr>';
				echo '<tr><td>';
				$this->currentTabId = $k;
				$this->currentTabName = $v [1];
				include $this->baseInc . $v [0] . '.php';
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
			// tabbed view
			
			$s = '<div class="panel with-nav-tabs"><ul class="nav nav-tabs responsive">';
			
			if (count ( $this->tabs ) - 1 < $this->active) {
				// Last selected tab is not available in this view. eg. Child tasks
				$this->active = 0;
			}
			foreach ( $this->tabs as $k => $v ) {
				$class = ($k == $this->active) ? 'class="active"' : '';
				$s .= '<li  ' . $class . '><a href="';
				$s .= $this->baseHRef . 'tab=' . $k;
				$s .= '"  >' . ($v [2] ? $v [1] : $this->_AppUI->_ ( $v [1] )) . '
                </a></li>';
			}
			$s .= '</ul>';
			echo $s;
			
			// Will be null if the previous selection tab is not available in the new window eg. Children tasks
			echo '<div class="tab-content responsive"> ';
			
			$k = $this->active;
			
			($this->active == $k) ? $css = 'active' : $css = '';
			$v = $this->tabs [$k];
			echo '<div class="tab-pane ' . $css . '" id="tab_' . $k . '">';
			$this->currentTabId = $k;
			$this->currentTabName = $v [1];
			require $this->baseInc . $v [0] . '.php';
			echo '</div>';
			/*
			 * echo '<script language="javascript" type="text/javascript">
			 * <!--
			 * show_tab(' . $this->active . ');
			 * //-->
			 * </script>';
			 */
			
			echo '</div></div></div>';
		}
	}
}