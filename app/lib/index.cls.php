<?php
class IndexAction extends initAction{
	function index() {
	    $this->assign("page_title", $this->app_name);
	    $this->display('public:header');
	    $this->display();
	    $this->display('public:footer');
	}
	function _init(){
	}
	function _default(){
    	echo "index::_default<br/>";
	}
}