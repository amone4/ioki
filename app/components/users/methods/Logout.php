<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Logout extends Users {

	// function for logout
	public function __construct() {
		parent::__construct();

		if (Misc::validateLogin()) Output::success('You have been successfully logged out');
		if (isset($_SESSION['user'])) unset($_SESSION['user']);
		if (isset($_SESSION['pass'])) unset($_SESSION['pass']);
		Output::redirect('users');
	}
}