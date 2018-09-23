<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Output {
	private static $output;

	// function initialize value of output
	public static function init() {
		Output::$output = [
			'messages' => [],
			'redirect' => [
				'valid' => false,
				'link' => ''
			],
			'view' => '',
			'data' => []
		];
	}

	public static function session($data) {
		if (App::get('isAPIRequest'))
			Output::$output['session'] = $data;
	}

	// function to set view
	public static function view($view, $data = null) {
		Output::$output['view'] = $view;
		if ($data !== null)
			Output::data($data);
	}

	// function to set data
	public static function data($data) {
		Output::$output['data'] = $data;
	}

	// function to push a success message
	public static function success($message) {
		array_push(Output::$output['messages'], [
			'type' => 'success',
			'message' => $message
		]);
	}

	// function to push information
	public static function info($message) {
		array_push(Output::$output['messages'], [
			'type' => 'info',
			'message' => $message
		]);
	}

	// function to push an error message
	public static function error($message) {
		array_push(Output::$output['messages'], [
			'type' => 'error',
			'message' => $message
		]);
	}

	// function to terminate processing, and display a fatal error
	public static function fatal($message = 'Invalid URL') {
		if (App::get('isAPIRequest'))
			Output::renderJSON(['fatal' => $message]);
		else {
			require_once APPROOT . '/views/message.php';
			Session::destroy();
			die();
		}
	}

	// function to redirect or instruct to redirect
	public static function redirect($link = '') {
		Output::$output['view'] = '';
		Output::$output['data'] = [];
		Output::$output['redirect'] = [
			'valid' => true,
			'link' => $link
		];
		Output::render();
	}

	// function to start final rendering of output
	public static function render() {
		if (App::get('isAPIRequest'))
			Output::renderJSON(Output::$output);
		else
			Output::renderHTML(Output::$output);
	}

	// function to render HTML output
	private static function renderHTML($output) {
		foreach ($output['messages'] as $message)
			Messages::{$message['type']}($message['message']);

		if ($output['redirect']['valid']) {
			header('Location: ' . URLROOT . '/' . $output['redirect']['link']);
			Session::destroy();
			die();
		}

		if (!empty($output['view'])) {
			$output['view'] = explode('/', $output['view']);
			$componentViewsPath = APPROOT . '/components/';
			if (isset($output['view'][1])) {
				$componentViewsPath .= $output['view'][0] . '/views/';
				unset($output['view'][0]);
			} else $componentViewsPath .= App::get('component') . '/views/';
			$data = $output['data'];
			if (file_exists($componentViewsPath . $output['view'][0] .  '.php')) {
				if (file_exists($componentViewsPath . 'header.php'))
					require_once $componentViewsPath . 'header.php';
				else require_once APPROOT . '/views/header.php';
				if (file_exists($componentViewsPath . 'navbar.php'))
					require_once $componentViewsPath . 'navbar.php';
				echo '<div id="container">';
				require_once $componentViewsPath . $output['view'][0] . '.php';
				echo '</div>';
				if (file_exists($componentViewsPath . 'footer.php'))
					require_once $componentViewsPath . 'footer.php';
				else require_once APPROOT . '/views/footer.php';
			} else Output::fatal('View does not exists');
		}
		Session::destroy();
		die();
	}

	// function to render JSON output
	private static function renderJSON($output) {
		header('Content-type: application/json');
		if (!empty($output['view'])) {
			$view = explode('/', $output['view']);
			if (isset($view[1]))
				$output['view'] = $view[0] . '/' . $view[1];
			else
				$output['view'] = App::get('component') . '/' . $view[0];
		}
		echo json_encode($output);
		Session::destroy();
		die();
	}
}
Output::init();