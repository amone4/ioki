<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Register extends Users {

	// function for register
	public function __construct() {
		parent::__construct();

		// checking if the user is logged in
		if (Misc::validateLogin()) {
			Output::info('You can\'t register, because you\'re logged in');
			App::dispatchMethod('logout');
		}

		// checking if the form is submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields are filled
			if (Forms::post($p, ['name', 'username', 'email', 'phone', 'password', 'confirmPassword'])) {
				// sanitizing data
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);
				// validating email
				if (Validations::validateEmail($p['email'])) {
					// validating username
					if (Validations::validateUsername($p['username'])) {
						// validating password
						if (Validations::validatePassword($p['password']) && Validations::validatePassword($p['confirmPassword'])) {
							// validating phone number
							if (Validations::validatePhone($p['phone'])) {
								// validating name
								if (Validations::validateName($p['name'])) {
									// checking if the passwords match
									if ($p['password'] === $p['confirmPassword']) {
										$p['name'] = strtolower($p['name']);
										// if the email has already been registered
										$this->user->selectWhere(['email' => $p['email']]);
										if ($this->user->rowCount() === 0) {
											// if the username has already been registered
											$this->user->selectWhere(['username' => $p['username']]);
											if ($this->user->rowCount() === 0) {

												// generating confirmation code for email
												$p['code'] = Crypt::encryptBlowfish($this->user->getNewID());
												// mailing the confirmation code
												$message = '<p>Confirm your email by clicking on the link below<br><a href="' . URLROOT . '/users/confirm/email/' . $p['code'] . '">Confirm email</a></p>';
												if (Misc::writeMessage($message, 'code.txt') || mail($p['email'], 'Confirm your email', $message, 'From: noreply@example.com' . "\r\n")) {

													// inserting record in database
													$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);
													unset($p['confirmPassword']);
													$p['code_sent_on'] = time();
													if ($this->user->insert($p)) {

														Output::success('You have been successfully registered. Confirm your email, and login again');
														Output::redirect('users');

													// error messages
													} else Output::error('Some error occurred while registering you. Try again to register');
												} else Output::error('Some error occurred while registering you. Try again to register');
											} else Output::error('Username has already been registered');
										} else Output::error('Email has already been registered');
									} else Output::error('Password don\'t match');
								} else Output::error('Invalid name');
							} else Output::error('Invalid phone number');
						} else Output::error('Invalid password');
					} else Output::error('Invalid username');
				} else Output::error('Invalid email');
			} else Output::error('Please enter valid details in all form fields');
		}

		Output::view('register');
	}
}