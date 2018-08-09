<?php
/**
 * Class apm_Output_HTML_Base
 *
 * @package     apmProject\output\html
 */
abstract class apm_Output_HTML_Base {
	protected $AppUI = null;
	public $df = null;
	protected $dtf = null;
	public function __construct($AppUI) {
		$this->AppUI = $AppUI;
		$this->df = $AppUI->getPref ( 'SHDATEFORMAT' );
		$this->dtf = $this->df . ' ' . $AppUI->getPref ( 'TIMEFORMAT' );
	}
	public function addLabel($label) {
		return $this->AppUI->_ ( $label );
	}
	public function showLabel($label) {
		echo $this->addLabel ( $label );
	}
}