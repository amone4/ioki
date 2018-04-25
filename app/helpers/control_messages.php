<?php

class Node {
	public $message;
	public $messageType;

	/**
	 * constructor method
	 *
	 * @param $message
	 * @param $messageType
	 */
	public function __construct($message, $messageType) {
		$this->message = $message;
		$this->messageType = $messageType;
	}
}

class Messages {
	private $top = 0;
	private $nodes = [];

	/**
	 * method to push a single message
	 *
	 * @param $message      string  message to be enqueued
	 * @param $messageType  boolean type of message; 1 => success; 0 => error
	 */
	public function push($message, $messageType) {
		$this->nodes[$this->top++] = new Node($message, $messageType);
	}

	/**
	 * method to pop out all messages
	 */
	public function pop() {
		for ($key = 0; $key < $this->top; $key++) {

			if ($this->nodes[$key]->messageType === 0) $type = 'success';
			else if ($this->nodes[$key]->messageType === 1) $type = 'primary';
			else $type = 'danger';


			echo '
				<div class="alert alert-' . $type . ' in" role="alert" id="alert_' . $type . $key . '">
					<button type="button" class="close" onclick="document.getElementById(\'alert_' . $type . $key . '\').hidden = true;">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">Close</span>
					</button>
					<strong>';

			if ($this->nodes[$key]->messageType === 0) echo 'Success!';
			else if ($this->nodes[$key]->messageType === 1) echo 'Information:';
			else echo 'Warning!';

			echo '</strong> ' . $this->nodes[$key]->message . '.
				</div>
			';
		}
		$this->top = 0;
	}
}

$_SESSION['control_message_handler'] = new Messages();

function enqueueErrorMessage($message) {
	$_SESSION['control_message_handler']->push($message, 2);
}

function enqueueInformation($message) {
	$_SESSION['control_message_handler']->push($message, 1);
}

function enqueueSuccessMessage($message) {
	$_SESSION['control_message_handler']->push($message, 0);
}

function dequeMessages() {
	$_SESSION['control_message_handler']->pop();
}