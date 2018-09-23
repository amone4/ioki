<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Update extends Credentials {

	// updates a credential
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$credential = $this->credential->select($id);
			if ($this->credential->rowCount() === 1 && $credential->user == $this->user) {

				// checking if the form was submitted
				if (Forms::isSubmitted()) {
					// checking if the form fields were filled
					if (Forms::post($p, ['type', 'login', 'password'])) {
						// sanitizing data
						$p = filter_var_array($p, FILTER_SANITIZE_STRING);
						// validating type of credential
						if ($p['type'] === 'email' || $p['type'] === 'username') {
							$p['type'] = (int) ($p['type'] === 'email');
							$p['link'] = $credential->link;
							$p['user'] = $this->user;
							// encrypting the credential
							$p['login'] = Crypt::encryptBlowfish($p['login'], $this->key);
							$p['password'] = Crypt::encryptBlowfish($p['password'], $this->key);
							// confirming that the credential doesn't already exist
							$result = $this->credential->selectWhere($p);
							if ($this->credential->rowCount() === 0 || $result->id == $id) {
								// updating credential
								unset($p['link']);
								unset($p['user']);
								if ($this->shared->deleteWhere(['credential' => $id,'shared_by' => $this->user]) && $this->credential->update($id, $p)) {

									Output::success('Credential successfully updated');
									Output::redirect('credentials/update/' . $id);

								// error messages
								} else Output::error('Some error occurred while inserting the new credential');
							} else Output::error('Credentials already exist');
						} else Output::error('Invalid login type');
					} else Output::error('Enter valid details in all form fields');
				}

				$credential->login = Crypt::decryptBlowfish($credential->login, $this->key);
				$credential->password = Crypt::decryptBlowfish($credential->password, $this->key);
				Output::view('update', $credential);

			} else Output::fatal();
		} else Output::fatal();
	}
}