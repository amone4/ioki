<?php

class Users extends Controller {
	private $user;

	public function __construct() {
		// initialize the model
		$this->user = $this->model('User');
	}

	// function for login
	public function index() {
		// checking if the user is logged in
		if (!isAppRequest() && validateLogin()) {
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
								if (!isAppRequest()) {
									$_SESSION['user'] = encryptAlpha($row->id, 6);
									$_SESSION['pass'] = encryptBlowfish($p['password'], $row->password);
								}
								enqueueSuccessMessage('You have been successfully logged in');
								redirect();

							// error messages
							} else enqueueErrorMessage('Invalid credentials');
						} else enqueueErrorMessage('Invalid credentials');
					} else enqueueErrorMessage('Invalid password');
				} else enqueueErrorMessage('Invalid username');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users/login');
	}

	// function for register
	public function register() {
		// checking if the user is logged in
		if (!isAppRequest() && validateLogin()) {
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
										$p['name'] = strtolower($p['name']);
										// if the email has already been registered
										$result = $this->user->selectWhere(['email' => $p['email']]);
										if ($this->user->rowCount() === 0) {
											// if the username has already been registered
											$result = $this->user->selectWhere(['username' => $p['username']]);
											if ($this->user->rowCount() === 0) {

												// generating confirmation code for email
												$p['code'] = encryptAlpha($this->user->getNewID(), 6);
												// mailing the confirmation code
												$message = '<p>Confirm your email by clicking on the link below<br><a href="' . URLROOT . '/users/confirm/email/' . $p['code'] . '">Confirm email</a></p>';
												if (writeMessage($message, 'code.txt') || mail($p['email'], 'Confirm your email', $message, 'From: noreply@example.com' . "\r\n")) {

													// inserting record in database
													$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);
													unset($p['confirmPassword']);
													$p['code_sent_on'] = time();
													if ($this->user->insert($p)) {

														enqueueSuccessMessage('You have been successfully registered. Confirm your email, and login again');
														redirect('users');

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

		$this->view('users/register');
	}

	// function for logout
	public function logout() {
		if (!isAppRequest()) {
			if (validateLogin()) enqueueSuccessMessage('You have been successfully logged out');
			if (isset($_SESSION['user'])) unset($_SESSION['user']);
			if (isset($_SESSION['pass'])) unset($_SESSION['pass']);
			redirect('users');
		}
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
		if (!isAppRequest() && validateLogin()) {
			enqueueInformation('You can\'t confirm your email, until you\'re logged in');
			$this->logout();
		}

		// validating code and fetching ID
		$code = filter_var($code, FILTER_SANITIZE_STRING);
		$id = decryptAlpha($code, 6);
		$row = $this->user->select($id);

		if ($this->user->rowCount() === 1) {
			if (($row->code === $code) && (time() - $row->code_sent_on < 864000)) {
				// confirming email
				if ($this->user->update($id, ['confirm_email' => 1, 'code' => '0'])) {

					enqueueSuccessMessage('Your email has been confirmed successfully. Login to proceed');
					redirect('users');

				// error messages
				} else generateErrorPage('Some error occurred while confirming your email. Try again');
			} else generateErrorPage('Your code has expired');
		} else generateErrorPage();
	}

	// function to confirm phone number
	private function confirmPhone() {
		// checking if the user is logged in
		if (!isAppRequest() && !validateLogin()) $this->logout();

		// checking if phone verification is needed
		$user = $this->user->select(decryptAlpha($_SESSION['user'], 6));
		if ($user->confirm_phone == 1) {
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
					if (($p['otp'] == $user->otp) && (time() - $user->otp_sent_on < 864000)) {
						// confirming phone number
						if ($this->user->update($user->id, ['confirm_phone' => 1, 'otp' => '0'])) {

							enqueueSuccessMessage('Your phone number was successfully confirmed');
							redirect();

						// error messages
						} else enqueueErrorMessage('Some error occurred while confirming your phone number. Try again');
					} else enqueueErrorMessage('Invalid OTP');
				} else enqueueErrorMessage('Invalid OTP');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users/confirm_phone');
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
		if (!isAppRequest() && !validateLogin()) redirect();

		// checking if phone verification is needed
		$user = $this->user->select(decryptAlpha($_SESSION['user'], 6));
		if ($user->confirm_phone == 1) {
			enqueueInformation('Your phone number has already been verified');
			redirect();
		}

		// generating otp
		$otp = rand(100, 999) * substr(time(), -3);
		$otp = substr($otp, 0, 2) . substr($otp, -2);
		$otp = substr($otp + $user->id, 0, 4);

		// storing OTP
		if ($this->user->update($user->id, ['otp' => $otp, 'otp_sent_on' => time()])) {
			// sending OTP
			if (writeMessage($user->phone . ': ' . $otp, 'otp.txt') || sendOTP(['phone' => $user->phone, 'otp' => $otp])) {

				enqueueSuccessMessage('OTP was successfully sent to your registered phone number');
				redirect('users/confirm/phone');

			// error messages
			} else generateErrorPage('Some error occurred while sending the OTP. Try again');
		} else generateErrorPage('Some error occurred while storing your OTP. Try again');
	}

	// function to send confirmation code for confirming the email
	private function sendCode() {
		// checking if the user is logged in
		if (!isAppRequest() && !validateLogin()) redirect();

		$user = $this->user->select(decryptAlpha($_SESSION['user'], 6));

		// checking if phone verification is needed
		if ($user->confirm_email == 1) {
			enqueueErrorMessage('Your email has already been verified');
			redirect();
		}

		// generating code
		$code = encryptAlpha($user->id, 6);
		// mailing the confirmation code
		$message = '<p>Confirm your email by clicking on the link below<br><a href="' . URLROOT . '/users/confirm/email/' . $code . '">Confirm email</a></p>';

		// storing code
		if ($this->user->update($user->id, ['code' => $code, 'code_sent_on' => time()])) {
			// sending code
			if (writeMessage($message, 'code.txt') || mail($user->email, 'Confirm your email', $message, 'From: noreply@example.com' . "\r\n")) {

				enqueueSuccessMessage('Confirmation code was successfully sent to your registered email');
				redirect();

			// error messages
			} else generateErrorPage('Some error occurred while sending the confirmation code. Try again');
		} else generateErrorPage('Some error occurred while storing your confirmation code. Try again');
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
		if (!isAppRequest() && validateLogin()) redirect();

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
						$message = '<p>Reset your password by clicking on the link below<br><a href="' . URLROOT . '/users/password/reset/' . $code . '">Reset password</a></p>';
						if (writeMessage($message, 'code.txt') || mail($p['email'], 'Reset your password', $message, 'From: noreply@example.com' . "\r\n")) {
							// updating the confirmation code
							if ($this->user->update($row->id, ['code' => $code, 'code_sent_on' => time()])) {

								enqueueSuccessMessage('Link to reset the password was successfully sent.');
								redirect('users');

							// error messages
							} else enqueueErrorMessage('Some error occurred. Try again');
						} else enqueueErrorMessage('Some error occurred while sending the mail. Try again');
					} else enqueueErrorMessage('This email is not registered');
				} else enqueueErrorMessage('Invalid email');
			} else enqueueErrorMessage('Please enter valid details in all form fields');
		}

		$this->view('users/password_forgot');
	}

	// function to reset password
	private function passwordReset($code) {
		// checking if the user is logged in
		if (!isAppRequest() && validateLogin()) {
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

								// deleting all unapproved credentials which were shared to the user
								$share = $this->model('Shared');
								if ($share->deleteWhere(['shared_to' => $id, 'approved' => 0])) {

									// confirmation code reset and password reset. The flag is also set to `reset` stage
									$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);
									if ($this->user->update($id, ['code' => '0', 'old_hash' => $row->password, 'flag' => 10, 'password' => $p['password']])) {

										enqueueSuccessMessage('Your password has been successfully reset. Login to proceed');
										redirect('users');

									// error messages
									} else enqueueErrorMessage('Some error occurred while resetting your password. Try again');
								} else enqueueErrorMessage('Some error occurred while resetting your password. Try again');
							} else enqueueErrorMessage('Passwords don\'t match');
						} else enqueueErrorMessage('Invalid password');
					} else enqueueErrorMessage('Please enter valid details in all form fields');
				}

				$this->view('users/password_reset', $code);

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to change password
	private function passwordChange() {
		// checking if the user is logged in
		if (!isAppRequest() && !validateLogin()) $this->logout();

		// checking if the form has been submitted
		if (postSubmit()) {
			// checking if the form fields have been filled
			if (postVars($p, ['oldPassword', 'newPassword', 'confirmPassword'])) {
				// validating passwords
				if (validatePassword($p['oldPassword']) && validatePassword($p['newPassword']) && validatePassword($p['confirmPassword'])) {
					// checking if the passwords match
					if ($p['newPassword'] === $p['confirmPassword']) {

						$user = $this->user->select(decryptAlpha($_SESSION['user'], 6));
						// checking if the old password is correct
						if (password_verify($p['oldPassword'], $user->password)) {

							// setting the flag
							$this->user->update($user->id, ['flag' => 1]);
							// changing encryption of all credentials
							if ($this->changeCredentialEncryption($user->id, $p['oldPassword'], $p['newPassword'])) {

								// changing the password
								$p['newPassword'] = password_hash($p['newPassword'], PASSWORD_DEFAULT);
								if ($this->user->update($user->id, ['password' => $p['newPassword'], 'old_hash' => $user->old_password, 'flag' => 0])) {

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

		$this->view('users/password_change');
	}

	public function sync() {
		if ($p = $this->validateAppRequest()) {
			if (isset($_POST['old']) && !empty($_POST['old'])) {
				$p['old'] = $_POST['old'];
				// getting the user details
				$user = $this->user->select($p['user']);
				// making the decryption key
				$decrypt = decryptBlowfish($p['old'], $user->old_hash);
				// making the encryption key
				$encrypt = decryptBlowfish($p['key'], $user->password);
				// setting the syncing flag
				$this->user->update($user->id, ['flag' => 1]);
				// changing encryption
				if ($this->changeCredentialEncryption($user->id, $decrypt, $encrypt)) {
					// changing flag to normal
					$this->user->update($user->id, ['flag' => 0]);
					generateErrorPage('Success');
				} else generateErrorPage('Error');

			} else generateErrorPage();
		} else generateErrorPage();
	}

	// function to change encryption of all credentials
	private function changeCredentialEncryption($user, $oldKey, $newKey) {
		// getting the models
		$credential = $this->model('Credential');
		$share = $this->model('Shared');

		// getting the credentials
		$credentials = $credential->selectWhere(['user' => $user]);
		$credentials = toArray($credentials);

		// getting the shared credentials
		$shared = $share->selectWhere(['shared_to' => $user]);
		$shared = toArray($shared);

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

		return true;
	}

	private function validateAppRequest() {
		if (postSubmit()) {
			if (postVars($p, ['user', 'key'])) {
				if (ctype_digit($p['user'])) {

					$row = $this->user->select($p['user']);
					if (password_verify(decryptBlowfish($p['key'], $row->password), $row->password)) {
						return $p;

					} else return false;
				} else return false;
			} else return false;
		} else return false;
	}
}