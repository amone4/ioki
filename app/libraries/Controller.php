<?php
/**
 * base controller
 * loads the models and views
 */
abstract class Controller {
	// abstract index function which runs by default
	abstract public function index();

	// load model
	public function model($model){
		// require model file
		require_once '../app/models/' . $model . '.php';

		// instantiate model
		return new $model();
	}

	// load view
	public function view($view, $data = []){
		// check for view file
		if(file_exists('../app/views/' . $view . '.php')){
			require_once '../app/views/' . $view . '.php';
		// view does not exist
		} else {
			die('View does not exist');
		}
	}
}