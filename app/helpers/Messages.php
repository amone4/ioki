<?php

defined('_INDEX_EXEC') or die('Restricted access');

/**
 * class to handle control messages
 */
class Messages {

	public static function init() {
		if (!isset($_SESSION['control_messages_top']))
			$_SESSION['control_messages_top'] = 1;
	}

	public static function error($message) {
		$_SESSION['control_messages_' . $_SESSION['control_messages_top'] . '_text'] = $message;
		$_SESSION['control_messages_' . $_SESSION['control_messages_top'] . '_type'] = 2;
		$_SESSION['control_messages_top']++;
	}

	public static function info($message) {
		$_SESSION['control_messages_' . $_SESSION['control_messages_top'] . '_text'] = $message;
		$_SESSION['control_messages_' . $_SESSION['control_messages_top'] . '_type'] = 1;
		$_SESSION['control_messages_top']++;
	}

	public static function success($message) {
		$_SESSION['control_messages_' . $_SESSION['control_messages_top'] . '_text'] = $message;
		$_SESSION['control_messages_' . $_SESSION['control_messages_top'] . '_type'] = 0;
		$_SESSION['control_messages_top']++;
	}

	public static function pop() {
		for ($key = 1; $key < $_SESSION['control_messages_top']; $key++) {

			if ($_SESSION['control_messages_' . $key . '_type'] === 0) $type = 'success';
			else if ($_SESSION['control_messages_' . $key . '_type'] === 1) $type = 'primary';
			else $type = 'danger';

			echo '
				<div class="alert alert-' . $type . ' in" role="alert" id="control_messages_alert_' . $key . '_' . $type . '">
					<button type="button" class="close" onclick="document.getElementById(\'control_messages_alert_' . $key . '_' . $type . '\').hidden = true;">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">Close</span>
					</button>
					<strong>';

			if ($_SESSION['control_messages_' . $key . '_type'] === 0) echo 'Success!';
			else if ($_SESSION['control_messages_' . $key . '_type'] === 1) echo 'Information:';
			else echo 'Warning!';

			echo '</strong> ' . $_SESSION['control_messages_' . $key . '_text'] . '.
				</div>
			';
		}

		$_SESSION['control_messages_top'] = 1;
	}
}
Messages::init();