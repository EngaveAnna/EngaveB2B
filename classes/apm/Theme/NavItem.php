<?php
class NavItem {
	public $title;
	public $textIcon;
	public $cssCl;
	public $cssActive;
	
	/*
	 * public function __construct($title='', $textIcon='', $cssCl='', $cssActive='')
	 * {
	 * $this->title=$title;
	 * $this->textIcon=$textIcon;
	 * $this->cssCl=$cssCl;
	 * $this->cssActive=$cssActive;
	 * }
	 */
	public function isActive() {
		($cssActive) ? true : false;
	}
}