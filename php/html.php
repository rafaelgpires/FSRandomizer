<?php
const htmldir = 'html';

class HTML {
	public $navbar, $styles, $links;
	public function __construct() {
		$this->navbar = file_get_contents(htmldir . '/navbar.html');
		$this->styles = file_get_contents(htmldir . '/styles.html');
		$this->links  = file_get_contents(htmldir . '/links.html');
	}
}
?>