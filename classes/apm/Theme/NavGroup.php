<?php
class NavGroup {
	public $title;
	public $textIcon;
	public $cssCl;
	public $cssActive;
	public $showTitle = true;
	public $showIcon = false;
	public function __construct($title = '', $textIcon = '', $cssCl = '', $cssActive = '') {
		$this->title = $title;
		$this->textIcon = $textIcon;
		$this->cssCl = $cssCl;
		$this->cssActive = $cssActive;
	}
	public function isActive() {
		($cssActive) ? true : false;
	}
}