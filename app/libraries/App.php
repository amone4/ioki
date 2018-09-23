<?php

defined('_INDEX_EXEC') or die('Restricted access');

class App {
	private static $data;

	// function to initiate the app
	public static function start() {
		// process request
		$data = App::processRequest();

		// setting values for app
		App::$data = [
			'component' => $data['component'],
			'isAPIRequest' => $data['isAPIRequest']
		];

		// getting any set session variables from API request
		Session::create();

		// dispatching method called in request
		App::dispatchMethod($data['method'], $data['params']);
	}

	// function to get an app value
	public static function get($key) {
		if (isset(App::$data[$key]))
			return App::$data[$key];
		else return null;
	}

	// function to set an app value
	public static function set($key, $value) {
		App::$data[$key] = $value;
	}

	// function to process request
	private static function processRequest() {
		$request = [
			'url' => '',
			'isAPIRequest' => false,
			'component' => 'Pages',
			'method' => 'index',
			'params' => []
		];

		// getting request
		if (isset($_GET['url'])) {
			// sanitizing request
			$request['url'] = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);

			// checking if its an API request
			if (substr($request['url'], 0, 3) === 'api') {
				if (!isset($request['url'][3])) {
					$request['isAPIRequest'] = true;
					$request['url'] = '';
				} elseif ($request['url'][3] === '/') {
					$request['isAPIRequest'] = true;
					$request['url'] = substr($request['url'], 4);
				}
			}

		// empty request
		} else $request['url'] = '';

		// converting URL into array
		$request['url'] = explode('/', $request['url']);

		// determining the component
		if (isset($request['url'][0]) && !empty($request['url'][0])) {
			$componentPath = APPROOT . '/components/' . $request['url'][0];
			$request['component'] = ucwords($request['url'][0]);
			unset($request['url'][0]);
		} else $componentPath = APPROOT . '/components/pages';

		if (!file_exists($componentPath . '/' . $request['component'] . '.php'))
			Output::fatal();

		// require the controller
		require_once $componentPath . '/' . $request['component'] . '.php';

		// check for second part of url
		if (isset($request['url'][1])) {
			$request['method'] = $request['url'][1];
			unset($request['url'][1]);
		}

		// getting all params
		if ($request['url']) $request['params'] = array_values($request['url']);

		return $request;
	}

	// function to dispatch method of a component
	public static function dispatchMethod($func, $params = []) {
		try {
			// method within controller class of that component
			$controller = App::$data['component'];
			$controller = new $controller;
			if (method_exists($controller, $func)) {
				if (!is_callable([$controller, $func])) Output::fatal();
				else call_user_func_array([$controller, $func], $params);

			// method within methods folder of that component
			} else {
				$func = ucwords($func);
				$filePath = APPROOT . '/components/' . App::$data['component'] . '/methods/' . $func . '.php';
				if (file_exists($filePath)) {
					require_once $filePath;
					$reflect = new ReflectionClass($func);
					$reflect->newInstanceArgs($params);

				// method not found
				} else Output::fatal('Method does not exist');
			}

			// rendering output after processing
			Output::render();

		// invalid parameters
		} catch (ArgumentCountError $e) {
			Output::fatal();
		}
	}

	// function to deny API request for specific components
	public static function denyAPIAccess() {
		App::$data['isAPIRequest'] = false;
		Session::destroy();
	}
}