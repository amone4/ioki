<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Forms {
	/**
	 * returns an associative array with keys being all values in given array and each value being null
	 * @param  array $arr array containing the keys of the required associative array
	 * @return array
	 */
	private static function nullArray($arr) {
		$temp = [];
		foreach ($arr as $value) {
			$temp[$value]=null;
		}
		return $temp;
	}

	/**
	 * checks if the GET variables with index in the array are set and not empty, and then escapes them by escapeData function
	 * @param  array &$get  used to store the escaped version of the data
	 * @param  array $arr contains indices of GET variables
	 * @param  string &$err [optional] stores the index which caused the error to generate
	 * @return boolean       returns true if data was present, false otherwise
	 */
	public static function get(&$get,$arr,&$err=null) {
		$get = self::nullArray($arr);
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
	 * checks if the POST variables with index in the array are set and not empty, and then escapes them by escapeData function
	 * @param  array &$post  used to store the escaped version of the data
	 * @param  array $arr contains indices of POST variables
	 * @param  string &$err [optional] stores the index which caused the error to generate
	 * @return boolean       returns true if data was present, false otherwise
	 */
	public static function post(&$post,$arr,&$err=null) {
		$post = self::nullArray($arr);
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
	 * checks if a post form was submitted
	 * @return boolean
	 */
	public static function isSubmitted() {
		return $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']);
	}

	/**
	 * gets the form details from forms.json
	 * @param $form string name of the form
	 * @param $component string name of the component
	 * @return object JSON else null
	 */
	private static function getForm($form, $component = null) {
		if ($component === null) $component = App::get('component');
		$filePath = APPROOT . '/components/' . $component . '/forms.json';
		if (file_exists($filePath)) {
			$file = trim(file_get_contents($filePath));
			if ($file) {
				$forms = json_decode($file);
				foreach ($forms as $temp)
					if ($temp->name === $form) {
						$form = $temp;
						break;
					}
				if (get_class($form) !== 'string')
					return $form;
			}
		}
		return null;
	}

	/**
	 * @param $type string type given in forms.json
	 * @return string type to be filled in input field
	 */
	private static function getInputType($type) {
		switch ($type) {
			case 'password': return 'password';
			case 'phone':
			case 'number': return 'number';
			case 'email': return 'email';
			default: return 'text';
		}
	}

	/**
	 * returns the filter to be used for sanitizing data
	 * @param $type string type given in forms.json
	 * @return int filter type
	 */
	private static function getFilterType($type) {
		switch ($type) {
			case 'phone':
			case 'number': return FILTER_SANITIZE_NUMBER_INT;
			case 'email': return FILTER_SANITIZE_EMAIL;
			default: return FILTER_SANITIZE_STRING;
		}
	}

	/**
	 * validates data submitted in a form
	 * @param $form string name of the form
	 * @param $component string name of the component
	 * @return array|bool form data after validation
	 */
	public static function validate($form, $component = null) {
		if ($component === null) $component = App::get('component');
		$form = self::getForm($form, $component);
		$data = [];
		if ($form) {
			if (isset($form->method) && strtolower($form->method) === 'get')
				$array = $_GET;
			else
				$array = $_POST;
			foreach ($form->fields as $field)
				if ($field->type !== 'submit')
					if (isset($array[$field->name]) && !empty($array[$field->name])) {
						$data[$field->name] = filter_var($array[$field->name], self::getFilterType($field->type));
						if (method_exists('Validations', $field->type))
							if (!call_user_func('Validations::' . $field->type, $data[$field->name])) {
								Messages::error('Invalid ' . $field->name);
								return false;
							}
					} else {
						Messages::error('Missing ' . $field->label);
						return false;
					}
		} else return false;
		return $data;
	}

	/**
	 * renders a form
	 * @param $form string name of the form
	 * @param $component string name of the component
	 */
	public static function render($form, $component = null) {
		if ($component === null) $component = App::get('component');
		$form = self::getForm($form, $component);
		if ($form) {
			$action = URLROOT . $form->action;
			$method = (isset($form->method) && strtolower($form->method) === 'get') ? 'GET' : 'POST';
			$class = (isset($form->class) && !empty($form->class) ? $form->class : '');
			$id = (isset($form->id) && !empty($form->id) ? $form->id : '');
			$labels = (isset($form->labels) && !empty($form->labels) ? $form->labels : false);
			echo '<form action="' . $action . '" method="' . $method . '" class="' . $class . '" id="' . $id . '">';
			echo '<fieldSet>';
			$submitButtonAdded = false;
			foreach ($form->fields as $field) {
				if ($field->type !== 'submit') {
					echo '<div class="field">';
					$name = $field->name;
					$class = (isset($field->class) && !empty($field->class) ? $field->class : '');
					$id = (isset($field->id) && !empty($field->id) ? $field->id : $form->name.'-'.$field->name);
					$label = (isset($field->label) && !empty($field->label) ? $field->label : ucwords($field->name));
					$type = self::getInputType($field->type);
					$placeholder = (isset($field->placeholder) && !empty($field->placeholder) ? $field->placeholder : 'Enter ' . $label);
					if($labels) echo '<label for="' . $id . '">' . $label . '</label>';
					echo '<input' . (isset($field->required) ? ' required' : '') . ' type="' . $type . '" name="' . $name . '" id="' . $id . '" class="' . $class . '" placeholder="' . $placeholder . '">';
					echo '</div><br>';
				} else {
					$submitButtonAdded = true;
					$class = (isset($field->class) && !empty($field->class) ? $field->class : '');
					$value = (isset($field->value) && !empty($field->value) ? ucwords($field->value) : 'Submit');
					$id = (isset($field->id) && !empty($field->id) ? $field->id : 'submit');
					echo '<input type="submit" name="submit" id="'.$id.'" value="'.$value.'" class="'.$class.'"><br><br>';
				}
			}
			if (!$submitButtonAdded)
				echo '<input type="submit" name="submit" id="submit" value="'.ucwords($form->name).'"><br>';
			echo '</fieldSet>';
			echo '</form>';
		}
	}
}