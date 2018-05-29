<?php

/**
 * method redirects user to a location within the website
 * @param $location string
 */
function redirect($location = null) {
	header('Location: ' . URLROOT . '/' . $location);
	die();
}

/**
 * checks if a form has been submitted via POST
 * @return boolean
 */
function postSubmit() {
	return ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']));
}

/**
 * checks if the login session is valid or not
 * @return bool
 */
function validateLogin() {
	if (isset($_SESSION['user']) && !empty($_SESSION['user'])) return true;
	else {
		unset($_SESSION['user']);
		return false;
	}
}

/**
 * generates an error page
 * @param $message string message to be displayed
 */
function generateErrorPage($message = 'Invalid URL') {
	require_once APPROOT . '/controllers/Pages.php';
	$controller = new Pages();
	$controller->error($message);
}

function toArray($input) {
	if (!is_array($input)) {
		if (empty($input)) return [];

		$temp[0] = $input;
		unset($input);
		$input = $temp;
	}
	return $input;
}

function writeMessage($message, $file = 'message.txt') {
	if ($con = fopen(APPROOT . '/' . $file, 'w')) {
		fwrite($con, $message);
		fclose($con);
		return true;
	}
	return false;
}