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
		return preg_match('%^[A-z0-9\_\-\.\@]+$%', $input);
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

	public static function time($input) {
		return preg_match('%^(?:2[0-3]|[01][0-9]):[0-5][0-9]$%', $input);
	}

	public static function date($date) {
		$date = explode('-', $date);
		if (isset($date[0]) && !empty($date[0]) && ctype_digit($date[0]))
			if (isset($date[1]) && !empty($date[1]) && ctype_digit($date[1]))
				if (isset($date[2]) && !empty($date[2]) && ctype_digit($date[2]))
					return checkdate($date[1], $date[2], $date[0]);
		return false;
	}
}