<?php
#[AllowDynamicProperties]
class Controller {

	function __construct() {
	    $view_object = new View();
		$this->view = $view_object;

		// set defualt timezone some up;; affects the whole project
		date_default_timezone_set("Africa/Nairobi");
		Session::initialize();
	}
	
	public function loadModel($name) {
		
		$path = 'models/'.$name.'_model.php';
		
		if (file_exists($path)) {
			require 'models/'.$name.'_model.php';
			
			$modelName = $name . '_Model';
			$this->model = new $modelName();
		}
		$this->view->_company = $this->model->_company();  
		$this->view->_me = $this->model->me(); 
		$this->view->version = CODE_VERSION;
		$this->view->_content = $this->model->_content(); 
		$this->view->currentpage = 0;
		$this->view->pid = '';  
		$this->view->page_id = '';
		  
	} 

	public function me() {
		return $this->model->me();
	}
	public function _company() {
		return $this->model->_company();
	}
	public function _content() {
		return $this->model->_content();
	}
   
  





	
}
