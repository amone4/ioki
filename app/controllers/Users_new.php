<?php

class Users_new extends Controller {
	private $user;

	public function __construct() {
		// initialize the model
		$this->user = $this->model('User_new');
	}

	// function for login
	public function index() {
		// checking if the user is logged in
		if (validateLogin()) {
			enqueueInformation('You can\'t login, because you\'re logged in');
			$this->logout();
		}

		// checking if the form is submitted
		if (postSubmit()) {
			// checking if the form fields are filled
			if (postVars($p, ['username', 'password'])) {
				// sanitizing data
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);
				// validating username
				if (validateUsername($p['username'])) {
					// validating password
					if (validatePassword($p['password'])) {
						// checking if username exists
						if (($row = $this->user->selectWhere(['username' => $p['username']])) && $this->user->rowCount() === 1) {
							// verifying if the password is correct
							if (password_verify($p['password'], $row->password)) {

								// encrypting and storing the session
								$_SESSION['user'] = encryptAlpha($row->id, 6);
								$_SESSION['pass'] = encryptBlowfish($p['password'], $row->password);

								enqueueSuccessMessage('You have been successfully logged in');
								redirect();

							// error messages
							} else enqueueErrorMessage('Invalid credentials');
						} else enqueueErrorMessage('Invalid credentials');
					} else enqueueErrorMessage('Invalid password');
				} else enqueueErrorMessage('Invalid username');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users_new/login');
	}

	// function for register
	public function register() {
		// checking if the user is logged in
		if (validateLogin()) {
			enqueueInformation('You can\'t register, because you\'re logged in');
			$this->logout();
		}

		// checking if the form is submitted
		if (postSubmit()) {
			// checking if the form fields are filled
			if (postVars($p, ['name', 'username', 'email', 'phone', 'password', 'confirmPassword'])) {
				// sanitizing data
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);
				// validating email
				if (validateEmail($p['email'])) {
					// validating username
					if (validateUsername($p['username'])) {
						// validating password
						if (validatePassword($p['password']) && validatePassword($p['confirmPassword'])) {
							// validating phone number
							if (validatePhone($p['phone'])) {
								// validating name
								if (validateName($p['name'])) {
									// checking if the passwords match
									if ($p['password'] === $p['confirmPassword']) {
										// if the email has already been registered
										if (count($this->user->selectWhere(['email' => $p['email']])) === 0) {
											// if the username has already been registered
											if (count($this->user->selectWhere(['username' => $p['username']])) === 0) {

												// generating confirmation code for email
												$p['code'] = encryptAlpha($this->user->getNewID(), 6);
												// mailing the confirmation code
												$message = '<p>Confirm your email by clicking on the link below<br><a href="' . URLROOT . '/users_new/confirm/email/' . $p['code'] . '">Confirm email</a></p>';
												if (mail($p['email'], 'Confirm your email', $message, 'From: noreply@example.com' . "\r\n")) {
													// registering the user
													$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);
													unset($p['confirmPassword']);
													if ($this->user->insert($p)) {

														enqueueSuccessMessage('You have been successfully registered. Confirm your email, and login again');
														redirect();

													// error messages
													} else enqueueErrorMessage('Some error occurred while registering you. Try again to register');
												} else enqueueErrorMessage('Some error occurred while registering you. Try again to register');
											} else enqueueErrorMessage('Username has already been registered');
										} else enqueueErrorMessage('Email has already been registered');
									} else enqueueErrorMessage('Password don\'t match');
								} else enqueueErrorMessage('Invalid name');
							} else enqueueErrorMessage('Invalid phone number');
						} else enqueueErrorMessage('Invalid password');
					} else enqueueErrorMessage('Invalid username');
				} else enqueueErrorMessage('Invalid email');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users_new/register');
	}

	// function for logout
	public function logout() {
		if (validateLogin()) enqueueSuccessMessage('You have been successfully logged out');
		if (isset($_SESSION['user'])) unset($_SESSION['user']);
		if (isset($_SESSION['pass'])) unset($_SESSION['pass']);
		redirect('users_new');
	}

	/** functions to handle confirmations **/
	public function confirm($request, $code = null) {
		if ($request === 'email' && $code !== null) $this->confirmEmail($code);
		else if ($request === 'phone') $this->confirmPhone();
		else generateErrorPage();
	}

	// function to confirm email
	private function confirmEmail($code) {
		// checking if the user is logged in
		if (validateLogin()) {
			enqueueInformation('You can\'t confirm your email, until you\'re logged in');
			$this->logout();
		}

		// validating code and fetching ID
		$code = filter_var($code, FILTER_SANITIZE_STRING);
		$id = decryptAlpha($code, 6);
		if (($row = $this->user->select($id)) && $this->user->rowCount() === 1) {
			if (($row->code === $code) && (time() - $row->code_sent_on < 864000)) {
				// confirming email
				if ($this->user->update($id, ['confirm_email' => 1, 'code' => '0'])) {

					enqueueSuccessMessage('Your email has been confirmed successfully. Login to proceed');
					redirect('users_new');

				// error messages
				} else generateErrorPage('Some error occurred while confirming your email. Try again');
			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to confirm phone number
	private function confirmPhone() {
		// checking if the user is logged in
		if (!validateLogin()) $this->logout();

		// checking if phone verification is needed
		if ($this->isPhoneVerified(decryptAlpha($_SESSION['user'], 6))) {
			enqueueInformation('Your phone number has already been verified');
			redirect();
		}

		// checking if the form has been submitted
		if (postSubmit()) {
			// checking if the form fields have been filled
			if (postVars($p, ['otp'])) {
				// sanitizing data
				$p['otp'] = filter_var($p['otp'], FILTER_SANITIZE_NUMBER_INT);

				// checking if the OTP is valid
				if (ctype_digit($p['otp'])) {
					if ($row = $this->user->select(decryptAlpha($_SESSION['user'], 6))) {
						if (($p['otp'] == $row->otp) && (time() - $row->otp_sent_on < 864000)) {

							// confirming phone number
							if ($this->user->update(decryptAlpha($_SESSION['user'], 6), ['confirm_phone' => 1, 'otp' => '0'])) {

								enqueueSuccessMessage('Your phone number was successfully confirmed');
								redirect();

							// error messages
							} else enqueueErrorMessage('Some error occurred while confirming your phone number. Try again');
						} else enqueueErrorMessage('Invalid OTP');
					} else enqueueErrorMessage('Some error occurred. Please try again');
				} else enqueueErrorMessage('Invalid OTP');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users_new/confirm_phone');
	}

	/** functions to send OTP and confirmation codes **/
	public function send($request) {
		if ($request === 'otp') $this->sendOTP();
		elseif ($request === 'code') $this->sendCode();
		else generateErrorPage();
	}

	// function to send OTP for confirming the phone number
	private function sendOTP() {
		// checking if the user is logged in
		if (!validateLogin()) redirect();

		// checking if phone verification is needed
		if ($this->isPhoneVerified(decryptAlpha($_SESSION['user'], 6))) {
			enqueueInformation('Your phone number has already been verified');
			redirect();
		}

		// generating otp
		$otp = rand(100, 999) * substr(time(), -3);
		$otp = substr($otp, 0, 2) . substr($otp, -2);
		$otp = substr($otp + decryptAlpha($_SESSION['user'], 6), 0, 4);

		// storing OTP
		if ($this->user->update(decryptAlpha($_SESSION['user'], 6), ['otp' => $otp, 'otp_sent_on' => time()])) {
			// sending OTP
			if ($phone = $this->user->select(decryptAlpha($_SESSION['user'], 6))->phone && sendOTP(['phone' => $phone, 'otp' => $otp])) {

				enqueueSuccessMessage('OTP was successfully sent to your registered phone number');
				redirect('users_new/confirm/phone');

			// error messages
			} else generateErrorPage('Some error occurred while sending the OTP. Try again');
		} else generateErrorPage('Some error occurred while storing your OTP. Try again');
	}

	// function to send confirmation code for confirming the email
	private function sendCode() {
		// checking if the user is logged in
		if (!validateLogin()) redirect();

		// checking if phone verification is needed
		if ($this->isEmailVerified(decryptAlpha($_SESSION['user'], 6))) {
			enqueueInformation('Your email has already been verified');
			redirect();
		}

		// generating code
		$code = $_SESSION['user'];
		// mailing the confirmation code
		$message = '<p>Confirm your email by clicking on the link below<br><a href="' . URLROOT . '/users_new/confirm/email/' . $code . '">Confirm email</a></p>';

		// storing code
		if ($this->user->update(decryptAlpha($_SESSION['user'], 6), ['code' => $code, 'code_sent_on' => time()])) {
			// sending code
			if ($email = $this->user->select(decryptAlpha($_SESSION['user'], 6))->email && mail($p['email'], 'Confirm your email', $message, 'From: noreply@example.com' . "\r\n")) {

				enqueueSuccessMessage('Confirmation code was successfully sent to your registered email');
				redirect();

			// error messages
			} else generateErrorPage('Some error occurred while sending the confirmation code. Try again');
		} else generateErrorPage('Some error occurred while storing your confirmation code. Try again');
	}

	// function to check if the phone number of user with `id` is verified or not
	private function isPhoneVerified($id) {
		return $this->user->select($id)->confirm_phone == 1;
	}

	// function to check if the email of user with `id` is verified or not
	private function isEmailVerified($id) {
		return $this->user->select($id)->confirm_email == 1;
	}

	/** functions to manage password **/
	public function password($request, $code = null) {
		if ($request === 'forgot') $this->passwordForgot();
		else if ($request === 'reset' && $code !== null) $this->passwordReset($code);
		else if ($request === 'change') $this->passwordChange();
		else generateErrorPage();
	}

	// function for forgot password
	private function passwordForgot() {
		// checking if the user is logged in
		if (validateLogin()) redirect();

		// checking if the form was submitted
		if (postSubmit()) {
			// checking if the form fields were filled
			if (postVars($p, ['email'])) {
				// sanitizing data
				$p['email'] = filter_var($p['email'], FILTER_SANITIZE_EMAIL);
				// checking if the email entered is valid
				if (validateEmail($p['email'])) {
					if (($row = $this->user->selectWhere(['email' => $p['email']])) && $this->user->rowCount() === 1) {

						// generating confirmation code for email
						$code = encryptAlpha($row->id, 6);
						// mailing the confirmation code
						$message = '<p>Reset your password by clicking on the link below<br><a href="' . URLROOT . '/users_new/password/reset/' . $code . '">Reset password</a></p>';
						if (mail($p['email'], 'Reset your password', $message, 'From: noreply@example.com' . "\r\n")) {
							// updating the confirmation code
							if ($this->user->update($row->id, ['code' => $code, 'code_sent_on' => time()])) {

								enqueueSuccessMessage('Link to reset the password was successfully sent.');
								redirect('users_new');

							// error messages
							} else enqueueErrorMessage('Some error occurred. Try again');
						} else enqueueErrorMessage('Some error occurred while sending the mail. Try again');
					} else {
						// for security reasons, no error message will be given when the email is not registered
						enqueueSuccessMessage('Link to reset the password was successfully sent.');
						redirect('users_new');
					}
				} else enqueueErrorMessage('Invalid email');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users_new/password_forgot');
	}

	// function to reset password
	private function passwordReset($code) {
		// checking if the user is logged in
		if (validateLogin()) {
			enqueueInformation('You can\'t reset your password, because you\'re logged in');
			$this->logout();
		}

		// validating code and fetching ID
		$code = filter_var($code, FILTER_SANITIZE_STRING);
		$id = decryptAlpha($code, 6);
		if (($row = $this->user->select($id)) && $this->user->rowCount() === 1) {
			if ($row->code === $code) {

				// checking if the form has been submitted
				if (postSubmit()) {
					// checking if the form fields have been filled
					if (postVars($p, ['password', 'confirmPassword'])) {
						// validating passwords
						if (validatePassword($p['password']) && validatePassword($p['confirmPassword'])) {
							// checking if the passwords match
							if ($p['password'] === $p['confirmPassword']) {

								// confirmation code reset and password reset
								$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);
								if ($this->user->update($id, ['code' => '0', 'old_hash' => $row->password, 'flag' => 2, 'password' => $p['password']])) {

									enqueueSuccessMessage('Your password has been successfully reset. Login to proceed');
									redirect('users_new');

								// error messages
								} else enqueueErrorMessage('Some error occurred while resetting your password. Try again');
							} else enqueueErrorMessage('Passwords don\'t match');
						} else enqueueErrorMessage('Invalid password');
					} else enqueueErrorMessage('Please enter valid details in all form fields');
				}

				$this->view('users_new/password_reset', $code);

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to change password
	private function passwordChange() {
		// checking if the user is logged in
		if (!validateLogin()) $this->logout();

		// checking if the form has been submitted
		if (postSubmit()) {
			// checking if the form fields have been filled
			if (postVars($p, ['oldPassword', 'newPassword', 'confirmPassword'])) {
				// validating passwords
				if (validatePassword($p['oldPassword']) && validatePassword($p['newPassword']) && validatePassword($p['confirmPassword'])) {
					// checking if the passwords match
					if ($p['newPassword'] === $p['confirmPassword']) {
						// checking if the old password is correct
						if (($old = $this->user->select(decryptAlpha($_SESSION['user'], 6))->password) && password_verify($p['oldPassword'], $old)) {

							// changing encryption of all credentials
							require_once APPROOT . '/controllers/Credentials_new.php';
							$credential = new Crendentials_new();
							if ($credential->changeEncryption(decryptAlpha($_SESSION['user'], 6), $p['oldPassword'], $p['newPassword'])) {

								// changing the password
								$p['newPassword'] = password_hash($p['newPassword'], PASSWORD_DEFAULT);
								if ($this->user->update(decryptAlpha($_SESSION['user'], 6), ['password' => $p['newPassword'], 'old_hash' => $old])) {

									enqueueSuccessMessage('Your password has been successfully changed. Login again to continue');
									$this->logout();

								// error messages
								} else enqueueErrorMessage('Some error occurred while changing your password');
							} else enqueueErrorMessage('Some error occurred while changing encryption of your credentials');
						} else enqueueErrorMessage('Old password is incorrect');
					} else enqueueErrorMessage('Passwords don\'t match');
				} else enqueueErrorMessage('Invalid password');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users_new/password_change');
	}

	private function changeCredentialEncryption($user, $oldKey, $newKey) {
		// getting the models
		$credential = $this->model('Credential_new');
		$share = $this->model('Shared');

		// getting the credentials
		$credentials = $credential->selectWhere(['user' => $user]);
		if ($credential->rowCount() === 1) {
			$temp[0] = $credentials;
			unset($credentials);
			$credentials = $temp;
		} elseif ($credentials->rowCount() === 0) $credentials = [];

		// getting the shared credentials
		$shared = $share->selectWhere(['user' => $user]);
		if ($share->rowCount() === 1) {
			$temp[0] = $shared;
			unset($shared);
			$shared = $temp;
		} elseif ($shared->rowCount() === 0) $shared = [];

		// encryption-decryption of credentials
		foreach ($credentials as $value) {
			$value->login = encryptBlowfish(decryptBlowfish($value->login, $oldKey), $newKey);
			$value->password = encryptBlowfish(decryptBlowfish($value->password, $oldKey), $newKey);
			$credential->update($value->id, ['login' => $value->login, 'password' => $value->password]);
		}

		// encryption-decryption of shared credentials
		foreach ($shared as $value) {
			$value->login = encryptBlowfish(decryptBlowfish($value->login, $oldKey), $newKey);
			$value->password = encryptBlowfish(decryptBlowfish($value->password, $oldKey), $newKey);
			$share->update($value->id, ['login' => $value->login, 'password' => $value->password]);
		}
	}
}