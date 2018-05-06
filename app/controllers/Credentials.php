<?php

class Credentials extends Controller {
	private $credential;
	private $key;

	public function __construct() {
		// validating that the user is logged in before processing of credentials begins
		if (!validateLogin()) redirect('');
		if (!$this->key = $this->getKey()) die('Some error occurred');
		$this->credential = $this->model('Credential');
	}
/*
	// function to get the decrypting key
	private function getKey() {
		return $key;
	}

	// function for encryption
	private function encrypt($input, $key = null) {
		if ($key === null) $key = $this->key;
		return $output;
	}

	// function for decryption
	private function decrypt($input, $key = null) {
		if ($key === null) $key = $this->key;
		return $output;
	}
*/
	// default function to show stored the credentials
	public function index() {
		// fetching all credentials
		$credentials = $this->credential->selectWhere(['user' => $_SESSION['user']]);

		// decrypt all credentials
		foreach ($credentials as &$credential) {
			$credential->username = $this->decrypt($credential->username);
			$credential->password = $this->decrypt($credential->password);
		}

		$this->view('credentials/view', $credentials);
	}

	// function to add a credential
	public function add() {
		// if the form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// getting and sanitizing all data
			if (postVars($p, ['link', 'type', 'password'])) {
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);
				$proceed = true;
				if ($p['type'] === 'email') {
					$proceed = postVars($p, ['email']);
					$p['type'] = true;
				} elseif ($p['type'] === 'username') {
					$proceed = postVars($p, ['username']);
					$p['type'] = false;

				} else $proceed = false;
				if ($proceed) {

					// sanitizing remaining data
					if ($p['type']) $p['username'] = filter_var($p['email'], FILTER_SANITIZE_EMAIL);
					else $p['username'] = filter_var($p['username'], FILTER_SANITIZE_STRING);

					// encrypting credentials
					$p['username'] = $this->encrypt($p['username']);
					$p['password'] = $this->encrypt($p['password']);

					// verifying that there is no duplication
					if (count($this->credential->selectWhere($p)) === 0) {

						// storing credentials
						if ($p['type']) unset($p['email']);
						$p['user'] = $_SESSION['user'];
						if ($this->credential->insert($p)) {

							enqueueSuccessMessage('Credentials successfully added');
							redirect('credentials');

						// error messages
						} else enqueueErrorMessage('Some error occurred while storing your credentials. Try again');
					} else enqueueErrorMessage('You have already registered these credentials');
				} else enqueueErrorMessage('Fill all form fields');
			} else enqueueErrorMessage('Fill all form fields');
		}

		$this->view('credentials/add');
	}

	// function to update a credential
	public function update($id) {
		// if the form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// validating the ID
			if (ctype_digit($id)) {
				if (count($this->credential->select($id)) === 1) {

					// getting and sanitizing all data
					if (postVars($p, ['link', 'type', 'password'])) {
						$p = filter_var_array($p, FILTER_SANITIZE_STRING);
						$proceed = true;
						if ($p['type'] === 'email') {
							$proceed = postVars($p, ['email']);
							$p['type'] = true;
						} elseif ($p['type'] === 'username') {
							$proceed = postVars($p, ['username']);
							$p['type'] = false;

						} else $proceed = false;
						if ($proceed) {

							// sanitizing remaining data
							if ($p['type']) $p['username'] = filter_var($p['email'], FILTER_SANITIZE_EMAIL);
							else $p['username'] = filter_var($p['username'], FILTER_SANITIZE_STRING);

							// encrypting credentials
							$p['username'] = $this->encrypt($p['username']);
							$p['password'] = $this->encrypt($p['password']);

							// verifying that there is no duplication
							if (count($this->credential->selectWhere($p)) === 0) {

								// storing credentials
								if ($p['type']) unset($p['email']);
								$p['user'] = $_SESSION['user'];
								if ($this->credential->udpate($id, $p)) {

									enqueueSuccessMessage('Credentials successfully added');
									redirect('credentials');

								// error messages
								} else enqueueErrorMessage('Some error occurred while storing your credentials. Try again');
							} else enqueueErrorMessage('You have already registered these credentials');
						} else enqueueErrorMessage('Fill all form fields');
					} else enqueueErrorMessage('Fill all form fields');
				} else enqueueErrorMessage('Invalid ID');
			} else enqueueErrorMessage('Invalid ID');
		}

		$this->index();
	}

	// function to delete a credential
	public function delete($id) {
		// validating the ID
		if (ctype_digit($id)) {
			if (count($this->credential->select($id)) === 1) {

				// deleting credentials
				if ($this->credential->delete($id)) {

					enqueueSuccessMessage('Credentials successfully deleted');
					redirect('credentials');

				// error messages
				} else enqueueErrorMessage('Some error occurred while deleting your credentials. Try again');
			} else enqueueErrorMessage('Invalid ID');
		} else enqueueErrorMessage('Invalid ID');

		$this->index();
	}
}