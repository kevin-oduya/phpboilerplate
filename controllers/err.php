<?php

class Err extends Controller {

	function __construct() {
		parent::__construct();
		Session::initialize();
	}
	
	function index() { 
        $this->notFound();
	}

}
