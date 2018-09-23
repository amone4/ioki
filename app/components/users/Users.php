<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Users extends Controller {
	protected $user;

	public function __construct() {
		// initialize the model
		$this->user = $this->getModel();
	}

	// function for login
	public function index() {
		// checking if the user is logged in
		if (Misc::validateLogin()) {
			Output::info('You can\'t login, because you\'re logged in');
			App::dispatchMethod('logout');
		}

		// checking if the form is submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields are filled
			if (Forms::post($p, ['username', 'password'])) {
				// sanitizing data
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);
				// validating username
				if (Validations::validateUsername($p['username'])) {
					// validating password
					if (Validations::validatePassword($p['password'])) {
						// checking if username exists
						if (($row = $this->user->selectWhere(['username' => $p['username']])) && $this->user->rowCount() === 1) {
							// verifying if the password is correct
							if (password_verify($p['password'], $row->password)) {

								// encrypting and storing the session
								$_SESSION['user'] = Crypt::encryptBlowfish($row->id);
								$_SESSION['pass'] = Crypt::encryptBlowfish($p['password'], $row->password);
								Output::success('You have been successfully logged in');
								Output::redirect();

							// error messages
							} else Output::error('Invalid credentials');
						} else Output::error('Invalid credentials');
					} else Output::error('Invalid password');
				} else Output::error('Invalid username');
			} else Output::error('Please enter valid details in all form fields');
		}

		Output::view('login');
	}

	// function to change encryption of all credentials
	protected function changeCredentialEncryption($user, $oldKey, $newKey) {
		// getting the models
		$credential = $this->getModel('credentials/Credential');
		$share = $this->getModel('credentials/Shared');

		// getting the credentials
		$credentials = $credential->selectWhere(['user' => $user]);
		$credentials = Misc::toArray($credentials);

		// getting the shared credentials
		$shared = $share->selectWhere(['shared_to' => $user]);
		$shared = Misc::toArray($shared);

		// encryption-decryption of credentials
		foreach ($credentials as $value) {
			$value->login = Crypt::encryptBlowfish(Crypt::decryptBlowfish($value->login, $oldKey), $newKey);
			$value->password = Crypt::encryptBlowfish(Crypt::decryptBlowfish($value->password, $oldKey), $newKey);
			$credential->update($value->id, ['login' => $value->login, 'password' => $value->password]);
		}

		// encryption-decryption of shared credentials
		foreach ($shared as $value) {
			$value->login = Crypt::encryptBlowfish(Crypt::decryptBlowfish($value->login, $oldKey), $newKey);
			$value->password = Crypt::encryptBlowfish(Crypt::decryptBlowfish($value->password, $oldKey), $newKey);
			$share->update($value->id, ['login' => $value->login, 'password' => $value->password]);
		}

		return true;
	}
}