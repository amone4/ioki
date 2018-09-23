<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Add extends Credentials {

	// adds a new credential
	public function __construct() {
		parent::__construct();

		// checking if the form was submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields were filled
			if (Forms::post($p, ['link', 'type', 'login', 'password'], $err)) {
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
					$p['login'] = Crypt::encryptBlowfish($p['login'], $this->key);
					$p['password'] = Crypt::encryptBlowfish($p['password'], $this->key);
					// confirming that the credential doesn't already exist
					$this->credential->selectWhere($p);
					if ($this->credential->rowCount() === 0) {
						// inserting the new credential
						if ($this->credential->insert($p)) {

							Output::success('Credential successfully added');
							Output::redirect('credentials');

						// error messages
						} else Output::error('Some error occurred while inserting the new credential');
					} else Output::error('Credentials already exist');
				} else Output::error('Invalid login type');
			} else Output::error('Enter valid details in all form fields ' . $err);
		}

		Output::view('add');
	}
}