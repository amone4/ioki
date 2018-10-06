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
			'data' => [],
			'session' => [],
		];
	}

	public static function session($data) {
		if (App::get('isAPIRequest')) {
			Output::$output['session'] = $data;
		}
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
		if (App::get('isAPIRequest')) {
			Output::$output = ['fatal' => $message];
			Output::renderJSON();
		} else {
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
			Output::renderJSON();
		else
			Output::renderHTML();
	}

	// function to render HTML output
	private static function renderHTML() {
		foreach (Output::$output['messages'] as $message)
			Messages::{$message['type']}($message['message']);

		if (Output::$output['redirect']['valid']) {
			header('Location: ' . URLROOT . '/' . Output::$output['redirect']['link']);
			Session::destroy();
			die();
		}

		if (!empty(Output::$output['view'])) {
			Output::$output['view'] = explode('/', Output::$output['view']);
			$componentViewsPath = APPROOT . '/components/';
			if (isset(Output::$output['view'][1])) {
				$componentViewsPath .= Output::$output['view'][0] . '/views/';
				unset(Output::$output['view'][0]);
			} else $componentViewsPath .= App::get('component') . '/views/';
			$data = Output::$output['data'];
			if (file_exists($componentViewsPath . Output::$output['view'][0] .  '.php')) {
				if (file_exists($componentViewsPath . 'header.php'))
					require_once $componentViewsPath . 'header.php';
				else require_once APPROOT . '/views/header.php';
				echo '<div id="container">';
				require_once $componentViewsPath . Output::$output['view'][0] . '.php';
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
	private static function renderJSON() {
		header('Content-type: application/json');
		if (!empty($output['view'])) {
			$view = explode('/', $output['view']);
			if (isset($view[1]))
				$output['view'] = $view[0] . '/' . $view[1];
			else
				$output['view'] = App::get('component') . '/' . $view[0];
		}
		Session::destroy();
		if (isset(Output::$output['session']['control_messages_top']))
			unset(Output::$output['session']['control_messages_top']);
		echo json_encode(Output::$output);
		die();
	}
}
Output::init();