<?php
/**
 * function returns an associative array with keys being all values in given array and each value being null
 * @param  array $arr array containing the keys of the required associative array
 * @return array
 */
function nullArray($arr) {
	$temp = [];
	foreach ($arr as $value) {
		$temp[$value]=null;
	}
	return $temp;
}

/**
 * function checks if the GET variables with index in the array are set and not empty, and then escapes them by escapeData function
 * @param  array &$get  used to store the escaped version of the data
 * @param  array $arr contains indices of GET variables
 * @param  string &$err [optional] stores the index which caused the error to generate
 * @return boolean       returns true if data was present, false otherwise
 */
function getVars(&$get,$arr,&$err=null) {
	$get = nullArray($arr);
	foreach ($get as $key => &$value) {
		if (isset($_GET[$key])&&!empty($_GET[$key])) {
			$value=$_GET[$key];
		} else {
			$err=$key;
			return false;
		}
	}
	return true;
}

/**
 * function checks if the POST variables with index in the array are set and not empty, and then escapes them by escapeData function
 * @param  array &$post  used to store the escaped version of the data
 * @param  array $arr contains indices of POST variables
 * @param  string &$err [optional] stores the index which caused the error to generate
 * @return boolean       returns true if data was present, false otherwise
 */
function postVars(&$post,$arr,&$err=null) {
	$post = nullArray($arr);
	foreach ($post as $key => &$value) {
		if (isset($_POST[$key])&&!empty($_POST[$key])) {
			$value=$_POST[$key];
		} else {
			$err=$key;
			return false;
		}
	}
	return true;
}

/**
 * function to check if a phone number is valid
 * @param   $phone  int phone number to be checked
 * @return  boolean returns if the phone number is valid
 */
function validatePhone($phone) {
	return preg_match('%^[1-9]{1}[0-9]{9}$%', $phone);
}

/**
 * function to check if an email ID is valid
 * @param   $email  string  email ID to be checked
 * @return  boolean returns if the email ID is valid
 */
function validateEmail($email) {
	return preg_match('%^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$%', $email);
}

/**
 * function to check if a username is valid
 * @param   $username  string  username to be checked
 * @return  boolean returns if the username is valid
 */
function validateUsername($username) {
	return preg_match('%[A-z0-9\_\-\.\@]+%', $username);
}