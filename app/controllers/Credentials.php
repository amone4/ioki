<?php

class Credentials extends Controller {
	private $credential;
	private $shared;
	private $user;
	private $key;

	// constructor function
	public function __construct() {
		// validating that the user is logged in
		if (!validateLogin()) redirect();
		// using the models
		$this->credential = $this->model('Credential');
		$this->shared = $this->model('Shared');
		// decrypting the user ID
		$this->user = decryptAlpha($_SESSION['user'], 6);
		// decrypting the user password to get the encryption-decryption key
		$user = $this->model('User');
		$this->key = decryptBlowfish($_SESSION['pass'], $user->select($this->user)->password);
	}

	// function to view all added credentials
	public function index() {
		$encrypted = $this->credential->selectWhere(['user' => $this->user]);
		$decrypted = [];

		if ($this->credential->rowCount() === 1) {
			$temp[0] = $encrypted;
			unset($encrypted);
			$encrypted = $temp;
			$decrypted[0]['login'] = decryptBlowfish($encrypted[0]->login, $this->key);
			$decrypted[0]['password'] = decryptBlowfish($encrypted[0]->password, $this->key);

		} elseif ($this->credential->rowCount() > 1) {
			foreach ($encrypted as $key => $value) {
				$decrypted[$key]['login'] = decryptBlowfish($value->login, $this->key);
				$decrypted[$key]['password'] = decryptBlowfish($value->password, $this->key);
			}

		} else {
			$encrypted = [];
			enqueueInformation('No credentials found');
		}

		$this->view('credentials/view', ['encrypted' => $encrypted, 'decrypted' => $decrypted]);
	}

	// function to add a new credential
	public function add() {
		// checking if the form was submitted
		if (postSubmit()) {
			// checking if the form fields were filled
			if (postVars($p, ['link', 'type', 'login', 'password'], $err)) {
				// sanitizing data
				$p['link'] = filter_var($p['link'], FILTER_SANITIZE_URL);
				$p['password'] = filter_var($p['password'], FILTER_SANITIZE_STRING);
				$p['login'] = filter_var($p['login'], FILTER_SANITIZE_STRING);
				$p['type'] = filter_var($p['type'], FILTER_SANITIZE_STRING);
				// validating type of credential
				if ($p['type'] === 'email' || $p['type'] === 'username') {
					$p['type'] = (int) ($p['type'] === 'email');
					$p['user'] = $this->user;
					// encrypting the credential
					$p['login'] = encryptBlowfish($p['login'], $this->key);
					$p['password'] = encryptBlowfish($p['password'], $this->key);
					// confirming that the credential doesn't already exist
					$result = $this->credential->selectWhere($p);
					if ($this->credential->rowCount() === 0) {
						// inserting the new credential
						if ($this->credential->insert($p)) {

							enqueueSuccessMessage('Credential successfully added');
							redirect('credentials');

						// error messages
						} else enqueueErrorMessage('Some error occurred while inserting the new credential');
					} else enqueueErrorMessage('Credentials already exist');
				} else enqueueErrorMessage('Invalid login type');
			} else enqueueErrorMessage('Enter valid details in all form fields ' . $err);
		}

		$this->view('credentials/add');
	}

	// function to update a credential
	public function update($id) {
		if (ctype_digit($id)) {
			$row = $this->credential->select($id);
			if ($this->credential->rowCount() === 1) {

				// checking if the form was submitted
				if (postSubmit()) {
					// checking if the form fields were filled
					if (postVars($p, ['type', 'login', 'password'])) {
						// sanitizing data
						$p['password'] = filter_var($p['password'], FILTER_SANITIZE_STRING);
						$p['login'] = filter_var($p['login'], FILTER_SANITIZE_STRING);
						$p['type'] = filter_var($p['type'], FILTER_SANITIZE_STRING);
						// validating type of credential
						if ($p['type'] === 'email' || $p['type'] === 'username') {
							$p['type'] = (int) ($p['type'] === 'email');
							$p['link'] = $this->credential->select($id)->link;
							$p['user'] = $this->user;
							// encrypting the credential
							$p['login'] = encryptBlowfish($p['login'], $this->key);
							$p['password'] = encryptBlowfish($p['password'], $this->key);
							// confirming that the credential doesn't already exist
							$result = $this->credential->selectWhere($p);
							if ($this->credential->rowCount() === 0 || $result->id == $id) {
								// updating credential
								unset($p['link']);
								unset($p['user']);
								if ($this->shared->deleteWhere(['shared_by' => $this->user]) && $this->credential->update($id, $p)) {

									enqueueSuccessMessage('Credential successfully updated');
									redirect('credentials/update/' . $id);

								// error messages
								} else enqueueErrorMessage('Some error occurred while inserting the new credential');
							} else enqueueErrorMessage('Credentials already exist');
						} else enqueueErrorMessage('Invalid login type');
					} else enqueueErrorMessage('Enter valid details in all form fields');
				}

				$this->view('credentials/update', $this->credential->select($id));

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to delete a credential
	public function delete($id) {
		if (ctype_digit($id)) {
			$row = $this->credential->select($id);
			if ($this->credential->rowCount() === 1) {

				if ($this->shared->deleteWhere(['credential' => $id]) && $this->credential->delete($id)) enqueueSuccessMessage('Credential successfully deleted');
				else enqueueErrorMessage('Some error occurred while deleting the credentials');
				redirect('credentials');

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// controller function to handle requests related to sharing of a credential
	public function share($request, $id = null) {
		$method = 'share' . ucwords($request);
		if (method_exists($this, $method))
			if ($id !== null) $this->$method($id);
			else $this->$method();
		else generateErrorPage();
	}

	// function to view all credentials that have been shared by us
	private function shareBy() {
		$encrypted =  $this->shared->selectWhere(['shared_by' => $this->user]);
		$decrypted = [];

		if ($this->shared->rowCount() === 1) {
			$temp[0] = $encrypted;
			unset($encrypted);
			$encrypted[0] = $temp[0];
			$decrypted[0]['login'] = decryptBlowfish($encrypted[0]->login, $this->key);

		} elseif ($this->shared->rowCount() > 1) {
			foreach ($encrypted as $key => $value) {
				$decrypted[$key]['login'] = decryptBlowfish($value->login, $this->key);
			}

		} else {
			$encrypted = $decrypted = [];
			enqueueInformation('No credentials have been shared to you');
		}

		$this->view('credentials/share_by', ['encrypted' => $encrypted, 'decrypted' => $decrypted]);
	}

	// function to view all credentials that have been shared to us
	private function shareTo() {
		$encrypted =  $this->shared->selectWhere(['shared_to' => $this->user]);
		$decrypted = [];

		$user = $this->model('User');
		$user = $user->select($this->user);

		if ($this->shared->rowCount() === 1) {
			$temp[0] = $encrypted;
			unset($encrypted);
			$encrypted = $temp;
			$decrypted[0]['login'] = decryptBlowfish($encrypted[0]->login, $user->password);

		} elseif ($this->shared->rowCount() > 1) {
			foreach ($encrypted as $key => $value) {
				$decrypted[$key]['login'] = decryptBlowfish($value->login, $user->password);
			}

		} else {
			$encrypted = $decrypted = [];
			enqueueInformation('No credentials have been shared to you');
		}

		$this->view('credentials/share_to', ['encrypted' => $encrypted, 'decrypted' => $decrypted]);
	}

	// function to share a credential
	private function shareAdd($id) {
		// validating id the credential selected is correct
		if (ctype_digit($id)) {
			$credential = $this->credential->select($id);
			if ($this->credential->rowCount() === 1) {

				// checking if the form was submitted
				if (postSubmit()) {
					// checking of the form fields are filled
					if (postVars($p, ['username', 'shared_till_date', 'shared_till_time'])) {
						// sanitizing data
						$p['username'] = filter_var($p['username'], FILTER_SANITIZE_STRING);
						$p['shared_till_date'] = filter_var($p['shared_till_date'], FILTER_SANITIZE_STRING);
						$p['shared_till_time'] = filter_var($p['shared_till_time'], FILTER_SANITIZE_STRING);
						// validating data
						if (validateDate($p['shared_till_date'])) {
							if (validateTime($p['shared_till_time'])) {
								if (validateUsername($p['username'])) {
									// checking if the user exits
									$user = $this->model('User');
									$result = $user->selectWhere(['username' => $p['username']]);
									if ($user->rowCount() === 1) {
										$user = $result;
										// checking if the credential is already shared with this user
										$result = $this->shared->selectWhere(['credential' => $user->id, 'shared_to' => $p['username']]);
										if ($this->shared->rowCount() === 0) {

											// setting the necessary variables
											$p['credential'] = $id;
											$p['link'] = $credential->link;
											$p['type'] = $credential->type;
											$p['login'] = decryptBlowfish($credential->login, $this->key);
											$p['password'] = encryptBlowfish(decryptBlowfish($credential->password, $this->key), $user->password);
											$p['shared_to'] = $user->id;
											$p['shared_by'] = $this->user;
											$p['shared_on'] = time();

											// make the timestamp
											list($year, $month, $day) = explode('-', $p['shared_till_date']);
											list($hour, $minute) = explode(':', $p['shared_till_time']);
											$p['shared_till'] = mktime($hour,$minute,0,$month,$day,$year);
											unset($p['shared_till_date']);
											unset($p['shared_till_time']);
											unset($p['username']);

											// checking if the `sharing_till` time is valid
											if ($p['shared_till'] > $p['shared_on']) {
												// sharing the credential
												if ($this->shared->insert($p)) {

													enqueueSuccessMessage('Credential was successfully shared');
													redirect('credentials/share/by');

												} else enqueueErrorMessage('Some error occurred while sharing the credential');
											} else enqueueErrorMessage('Select a sharing time, after current time');
										} else enqueueErrorMessage('Credential has already been shared by the user');
									} else enqueueErrorMessage('No user with such a username exists');
								} else enqueueErrorMessage('Invalid username');
							} else enqueueErrorMessage('Invalid time');
						} else enqueueErrorMessage('Invalid date');
					} else enqueueErrorMessage('Enter valid details in all form fields');
				}

				$this->view('credentials/share_add', $credential);

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to take back a credential
	private function shareDelete($id) {
		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1) {

				if ($this->shared->delete($id)) enqueueSuccessMessage('Credential is no longer shared with the user');
				else enqueueErrorMessage('Some error occurred while taking back the credential');
				redirect('credentials/share/by');

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to accept a request for sharing
	private function shareApprove($id) {
		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1) {

				if ($result->shared_till > time()) {
					if ($this->shared->update($id, ['approved' => 1])) {
						enqueueSuccessMessage('Credential successfully accepted for sharing');
					} else enqueueErrorMessage('Some error occurred while accepting this request');
				} else {
					enqueueErrorMessage('Time has expired to accept this request of sharing a credential. The request is being deleted');
					$this->shared->delete($id);
				}

				redirect('credentials/share/to');

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to reject a request for sharing
	private function shareReject($id) {
		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1) {

				if ($this->shared->delete($id)) enqueueSuccessMessage('Credential successfully rejected for sharing');
				else enqueueErrorMessage('Some error occurred while rejecting this request');
				redirect('credentials/share/to');

			} else generateErrorPage();
		} else generateErrorPage();
	}
}