<?php
/**
 * app core class
 * creates URL & loads core controller
 * URL format - /controller/method/params
 */
class Core {
	protected $currentController = 'Pages';
	protected $currentMethod = 'index';
	protected $params = [];

	public function __construct() {
		if (isAppRequest()) header('Content-type: application/json');

		$url = $this->getUrl();

		// look in controllers for first value
		if (isset($url[0]) && !empty($url[0])) {

			// generate error if there is no file for handling request
			if (!file_exists('../app/controllers/' . ucwords($url[0]). '.php')) generateErrorPage();

			else {
				// if exists, set as controller
				$this->currentController = ucwords($url[0]);
				// unset 0 Index
				unset($url[0]);
			}
		}

		// require the controller
		require_once '../app/controllers/'. $this->currentController . '.php';

		// instantiate controller class
		$this->currentController = new $this->currentController;

		// check for second part of url
		if (isset($url[1])) {
			// check to see if method exists in controller
			if (method_exists($this->currentController, $url[1])) {
				$this->currentMethod = $url[1];
				// unset 1 index
				unset($url[1]);
			}
		}

		// get params
		$this->params = $url ? array_values($url) : [];

		try {
			// call a callback with array of params
			call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
		} catch (ArgumentCountError $e) {
			generateErrorPage();
		}
	}

	// function to convert URL into array
	private function getUrl() {
		if (isset($_GET['url'])) {
			$url = rtrim($_GET['url'], '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);
			return $url;
		}
	}
}