<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Session {
	private static $vars;

	public static function create() {
		Session::$vars = [];
		if (App::get('isAPIRequest') && isset($_POST['session'])) {
			$session = json_decode($_POST['session']);
			foreach ($session as $key => $value) {
				$key = trim(filter_var($key, FILTER_SANITIZE_STRING));
				array_push(Session::$vars, $key);
				$_SESSION[$key] = trim(filter_var($value, FILTER_SANITIZE_STRING));
			}
			unset($_POST['session']);
		}
	}

	public static function destroy() {
		if (App::get('isAPIRequest')) {
			Output::session($_SESSION);
			session_destroy();
		} else if (Session::$vars)
			foreach (Session::$vars as $key => $value)
				if (isset($_SESSION[$key]))
					unset($_SESSION[$key]);
	}
}