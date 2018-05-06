<?php

class Pages extends Controller {

	public function index() {
		$this->view('pages/index');
	}

	public function error($message) {
		echo '<p>' . $message . '</p>';
		die();
	}
}