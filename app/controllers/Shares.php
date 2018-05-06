<?php

class Shares extends Controller {
	private $share;
	private $key;

	public function __construct() {
		// validating that the user is logged in before processing of credentials begins
		if (!validateLogin()) redirect('');
		if (!$this->key = $this->getKey()) die('Some error occurred');
		$this->share = $this->model('Share');
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
	// function to display the credentials viewing page
	private function showCredentialsPage() {
		require_once APPROOT . '/controllers/Credentials.php';
		$controller = new Credentials();
		$controller->index();
	}

	// default function to view entire shared credential - user mapping
	public function index() {
		// fetching all shared credentials
		$credentials = $this->share->select();

		// decrypt all credentials
		$user = $this->model('User');
		foreach ($credentials as $key => $credential) {
			$data[$key]['link'] = $credential->link;
			$data[$key]['type'] = $credential->type;
			$data[$key]['user'] = $user->select($credential->user);
			$data[$key]['username'] = decrypt($credential->username);
		}

		$this->view('shares/view');
	}

	// function to share a credential
	public function add($id) {
		// validating ID
		if (ctype_digit($id)) {
			$credential = $this->model('Credential');
			if ($credentials = $credential->select($id) && count($credentials) === 1) {

				// if the form was submitted
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

					// validating the username
					if (postVars($p, ['username'])) {
						$p = filter_var($p['username'], FILTER_SANITIZE_STRING);
						$user = $this->model('User');
						if ($p['to'] = $user->existsByField('username', $p['username'])) {

							// decrypting credentials
							$p['username'] = $this->decrypt($credentials->username);
							$p['password'] = $this->decrypt($credentials->password);

							// calculating encrypting key

							// encrypting credentials
							$p['username'] = $this->encrypt($credentials->username, $key);
							$p['password'] = $this->encrypt($credentials->password, $key);

							// sharing the credentials
							$p['link'] = $credentials->link;
							$p['type'] = $credentials->type;
							$p['user'] = $credentials->user;
							if ($this->share->addForApproval($p)) {

								enqueueSuccessMessage('Credentials successfully shared');
								redirect('shares/view');

							// error messages
							} else enqueueErrorMessage('Some error occurred while sharing the credentials');
						} else enqueueErrorMessage('This user doesn\'t exists');
					} else enqueueErrorMessage('Enter the username of the user, you want to share the credentials with');
				}

				$this->view('shares/add');

			// error messages
			} else enqueueErrorMessage('Invalid ID');
		} else enqueueErrorMessage('Invalid ID');

		$this->showCredentialsPage();
	}

	// function to revoke a shared credential
	public function revoke($id) {
		// validating ID
		if (ctype_digit($id)) {
			if (count($this->share->select($id)) === 1) {

				// revoking the shared credentials
				if ($this->share->delete($id)) {

					enqueueSuccessMessage('Credentials successfully shared');
					redirect('shares/view');

				// error messages
				} else enqueueErrorMessage('Some error occurred while deleting the credentials');
			} else enqueueErrorMessage('Invalid ID');
		} else enqueueErrorMessage('Invalid ID');

		$this->index();
	}
}