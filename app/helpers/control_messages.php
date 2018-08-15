<?php

if (!isset($_SESSION['control_messages_top']) || empty($_SESSION['control_messages_top'])) $_SESSION['control_messages_top'] = 0;

function enqueueErrorMessage($message) {
	$_SESSION['control_messages_text' . $_SESSION['control_messages_top']] = $message;
	$_SESSION['control_messages_type' . $_SESSION['control_messages_top']] = 2;
	$_SESSION['control_messages_top']++;
}

function enqueueInformation($message) {
	$_SESSION['control_messages_text' . $_SESSION['control_messages_top']] = $message;
	$_SESSION['control_messages_type' . $_SESSION['control_messages_top']] = 1;
	$_SESSION['control_messages_top']++;
}

function enqueueSuccessMessage($message) {
	$_SESSION['control_messages_text' . $_SESSION['control_messages_top']] = $message;
	$_SESSION['control_messages_type' . $_SESSION['control_messages_top']] = 0;
	$_SESSION['control_messages_top']++;
}

function dequeMessages() {
	for ($key = 0; $key < $_SESSION['control_messages_top']; $key++) {

		if ($_SESSION['control_messages_type' . $key] === 0) $type = 'success';
		else if ($_SESSION['control_messages_type' . $key] === 1) $type = 'primary';
		else $type = 'danger';

		echo '
			<div class="alert alert-' . $type . ' in" role="alert" id="alert_' . $type . $key . '">
				<button type="button" class="close" onclick="document.getElementById(\'alert_' . $type . $key . '\').hidden = true;">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<strong>';

		if ($_SESSION['control_messages_type' . $key] === 0) echo 'Success!';
		else if ($_SESSION['control_messages_type' . $key] === 1) echo 'Information:';
		else echo 'Warning!';

		echo '</strong> ' . $_SESSION['control_messages_text' . $key] . '.
			</div>
		';
	}
	$_SESSION['control_messages_top'] = 0;
}

function messagesToJSON() {
	$messages = [];
	for ($key = 0; $key < $_SESSION['control_messages_top']; $key++) {
		if ($_SESSION['control_messages_type' . $key] === 0) $messages[$key]['type'] = 'success';
		else if ($_SESSION['control_messages_type' . $key] === 1) $messages[$key]['type'] = 'primary';
		else $messages[$key]['type'] = 'danger';
		$messages[$key]['message'] = $_SESSION['control_messages_text' . $key];
	}
	$_SESSION['control_messages_top'] = 0;
	return $messages;
}