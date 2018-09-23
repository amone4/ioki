<?php

defined('_INDEX_EXEC') or die('Restricted access');

/**
 * class to encapsulate functions for data validations
 */
class Validations {

	public static function name($input) {
		return preg_match('%^[A-z\s]*$%', $input);
	}

	public static function string($input) {
		return preg_match('%^[A-z0-9]+$%', $input);
	}

	public static function username($input) {
		return preg_match('%^[A-z0-9\_\.]+$%', $input);
	}

	public static function email($input) {
		return preg_match('%^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$%', $input);
	}

	public static function phone($input) {
		return preg_match('%^[1-9]{1}[0-9]{9}$%', $input);
	}

	public static function number($input) {
		return ctype_digit($input);
	}

	public static function password($input) {
		return preg_match('%[A-z0-9\.\-\_\@\&]%', $input);
	}

	public static function address($input) {
		return preg_match('%[A-z0-9\s\,\-\;]%', $input);
	}
}