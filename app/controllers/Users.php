<?php

class Users extends Controller {
	private $data;
	private $user;

	public function __construct() {
		$this->user = $this->model('User');
	}

	public function index() {
		// no need to login, if already logged in
		if (validateLogin()) {
			redirect('');
		}

		// if the login form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// checking if the fields were filled in
			if (postVars($p, ['username', 'password'])) {

				// sanitizing the form data
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);

				// validating username
				if (validateUsername($p['username'])) {

					// validating credentials
					if ($id = $this->user->validateCredentials($p)) {

						// storing the login session
						$_SESSION['user'] = $id;
						redirect('');

					// error messages
					} else {
						enqueueErrorMessage('Invalid credentials');
					}
				} else {
					enqueueErrorMessage('Invalid username');
				}
			} else {
				enqueueErrorMessage('Please enter valid details in all form fields');
			}
		}

		// loading the login view
		$this->view('users/login', $this->data);
	}

	public function register() {
		// no need to register, if already logged in
		if (validateLogin()) {
			redirect('');
		}

		// if the login form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// checking if the fields were filled in
			if (postVars($p, ['username', 'email', 'name', 'phone', 'password', 'confirm'])) {

				// sanitizing the form data
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);

				// validating email
				if (validateEmail($p['email'])) {

					// validating phone number
					if (validatePhone($p['phone'])) {

						// validating username
						if (validateUsername($p['username'])) {

							// checking if the passwords match
							if ($p['confirm'] === $p['password']) {

								// checking if email is registered
								if (!$this->user->fieldExists('email', $p['email'])) {

									// checking if phone number is registered
									if (!$this->user->fieldExists('phone', $p['phone'])) {

										// checking if the username is registered
										if (!$this->user->fieldExists('username', $p['username'])) {

											// hashing the password
											$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);

											// registering the user
											if ($id = $this->user->register($p)) {

												// generating a confirmation code
												$code = rand(10000, 99999) . '' . $id . '' . rand(10000, 99999);
												$code = encryptAlpha($code);

												// preparing the email message
												$message = 'Click on the following link to confirm your email. <br>' . URLROOT . '/users/confirm_email/' . $code;

												// mailing the confirmation code
												enqueueSuccessMessage($message); if (true) {//if (mail($p['email'], 'Email Confirmation', $message, 'From: noreply@amone.apps19.com')) {

													// redirecting towards login page with a confirmation message
													enqueueSuccessMessage('Complete your registration by confirming your email');
													redirect('users');

												// error messages
												} else {
													enqueueErrorMessage('Some error occurred while sending you the confirmation email');
												}
											} else {
												enqueueErrorMessage('Some error occurred while registering you');
											}
										} else {
											enqueueErrorMessage('Username already registered');
										}
									} else {
										enqueueErrorMessage('Phone number already registered');
									}
								} else {
									enqueueErrorMessage('Email already registered');
								}
							} else {
								enqueueErrorMessage('Passwords don\'t match');
							}
						} else {
							enqueueErrorMessage('Invalid username');
						}
					} else {
						enqueueErrorMessage('Invalid phone number');
					}
				} else {
					enqueueErrorMessage('Invalid email ID');
				}
			} else {
				enqueueErrorMessage('Please enter valid details in all form fields');
			}
		}

		// loading the registration view
		$this->view('users/register', $this->data);
	}

	public function logout() {
		if (isset($_SESSION['user'])) {
			unset($_SESSION['user']);
			enqueueSuccessMessage('You were successfully logged out');
		}
		redirect('users');
	}

	public function confirm_email($param) {
		// sanitizing the code
		$param = filter_var($param, FILTER_SANITIZE_STRING);

		// checking if the code comprises of valid characters
		if (preg_match('%^[A-Za-z0-9]+$%', $param)) {

			// retrieving the serial number from the code
			$id = substr(substr(number_format(decryptAlpha($param), 0, '', ''), 5), 0, -5);

			// checking if serial number exists
			if ($this->user->serialNumberExists($id)) {

				// confirming email
				if ($this->user->confirmEmail($id)) {

					// storing a success message and redirecting
					if (isset($_SESSION['user'])) {
						unset($_SESSION['user']);
					}
					enqueueSuccessMessage('Email ID confirmed. Don\'t forget to confirm your phone number. Login to continue');
					redirect('users');

				// error messages
				} else {
					generateErrorPage('Some error occurred while confirming email. Try again');
				}
			} else {
				generateErrorPage('Invalid URL');
			}
		} else {
			generateErrorPage('Invalid URL');
		}
	}

	public function change() {
		// validating if the user is logged in
		if (!validateLogin()) {
			$this->logout();
		}

		// checking if the form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// checking if the form fields were filled
			if (postVars($p, ['old', 'new', 're'])) {

				// sanitizing strings
				$p = filter_var_array($p, FILTER_SANITIZE_STRING);

				// checking if the two new passwords match
				if ($p['new'] === $p['re']) {

					// checking if the old password was correct
					if ($this->user->confirmPassword(['id' => $_SESSION['user'], 'password' => $p['old']])) {

						// hashing the password
						$p['new'] = password_hash($p['new'], PASSWORD_DEFAULT);

						// changing password
						if ($this->user->changePassword(['id' => $_SESSION['user'], 'password' => $p['new']])) {

							// storing a success message and redirecting
							unset($_SESSION['user']);
							enqueueSuccessMessage('Password changed. Login to continue');
							redirect('users');

						// error messages
						} else {
							enqueueErrorMessage('Some error occurred while changing password. Try again');
						}
					} else {
						enqueueErrorMessage('The old password is incorrect');
					}
				} else {
					enqueueErrorMessage('The entered passwords don\'t match');
				}
			} else {
				enqueueErrorMessage('Please enter valid details in all form fields');
			}
		}

		// loading the view to change password
		$this->view('users/change_password', $this->data);
	}

	public function forgot() {
		if (validateLogin()) {
			redirect('');
		}

		$this->data['sent'] = false;

		// checking if the email form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// checking if the email was filled
			if (postVars($p, ['email'])) {

				// sanitizing email
				$p['email'] = filter_var($p['email'], FILTER_SANITIZE_EMAIL);

				// validating email
				if (validateEmail($p['email'])) {

					// checking if email exists
					if ($id = $this->user->emailExists($p['email'])) {

						// generating a confirmation code
						try {
							$code = random_int(10000, 99999) . '' . $id . '' . random_int(10000, 99999);
						} catch (Exception $e) {
							$code = rand(10000, 99999) . '' . $id . '' . rand(10000, 99999);
						}
						$code = encryptAlpha($code);

						// storing the code in the database
						if ($this->user->storeResetCode($id, $code)) {

							// preparing the email message
							$message = 'Click on the following link to change your password. <br>' . URLROOT . '/users/reset/' . $code;

							// mailing the confirmation code
							if (mail($p['email'], 'Change Password', $message)) {

								// setting a confirmation message
								enqueueSuccessMessage('Change your password using the link sent to your email');
								$this->data['sent'] = true;

							// error messages
							} else {
								enqueueErrorMessage('Some error occurred while sending you the code');
							}
						} else {
							enqueueErrorMessage('Some error occurred while processing your reset code');
						}
					} else {
						enqueueErrorMessage('The email is not registered');
					}
				} else {
					enqueueErrorMessage('Invalid email ID');
				}
			} else {
				enqueueErrorMessage('Please enter valid details in all form fields');
			}
		}

		// loading the view
		$this->view('users/forgot_password', $this->data);
	}

	public function reset($param) {
		if (validateLogin()) {
			redirect('');
		}

		// sanitizing the code
		$param = filter_var($param, FILTER_SANITIZE_STRING);

		// checking if the code comprises of valid characters
		if (preg_match('%^[A-Za-z0-9]+$%', $param)) {

			// retrieving the serial number from the code
			$id = substr(substr(number_format(decryptAlpha($param), 0, '', ''), 5), 0, -5);

			// checking if the code is valid
			if ($this->user->verifyResetCode($id, $param)) {

				$this->data['code'] = $param;

				// checking if the form was submitted
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

					// checking if the form fields were filled
					if (postVars($p, ['new', 're'])) {

						// sanitizing strings
						$p = filter_var_array($p, FILTER_SANITIZE_STRING);

						// checking if the two new passwords match
						if ($p['new'] === $p['re']) {

							// hashing the password
							$p['new'] = password_hash($p['new'], PASSWORD_DEFAULT);

							// changing password
							if ($this->user->resetPassword(['id' => $id, 'password' => $p['new']])) {

								// storing a success message and redirecting
								unset($_SESSION['user']);
								enqueueSuccessMessage('Password changed. Login to continue');
								redirect('users');

							// error messages
							} else {
								enqueueErrorMessage('Some error occurred while changing password. Try again');
							}
						} else {
							enqueueErrorMessage('The entered passwords don\'t match');
						}
					} else {
						enqueueErrorMessage('Please enter valid details in all form fields');
					}
				}

				// loading the view to reset password
				$this->view('users/reset_password', $this->data);

			} else {
				generateErrorPage('Invalid URL');
			}
		} else {
			generateErrorPage('Invalid URL');
		}
	}

	public function createOTP() {
		if (!validateLogin() || $this->user->isPhoneVerified($_SESSION['user'])) {
			redirect('');
		}

		$phone = $this->user->getPhone($_SESSION['user']);

		// preparing the OTP
		$otp = hash('sha256', (time() + $phone + $_SESSION['user']));
		$otp = substr($otp, 0, 6);
		$otp = strtoupper($otp);

		// storing the otp
		if ($this->user->setOTP(['id' => $_SESSION['user'], 'otp' => $otp])) {

			// sending the otp
			if (sendOTP(['phone' => $p['phone'], 'otp' => $otp])) {

				// redirecting to page for filling in the OTP
				enqueueSuccessMessage('OTP has been sent to you. Please enter the same to confirm your number');
				redirect('users/verifyOTP/' . encryptAlpha($p['phone']));

			// error messages
			} else {
				generateErrorPage('Some error occurred while sending you the OTP');
			}
		} else {
			generateErrorPage('Some error occurred while communicating with the database');
		}
	}

	public function verifyOTP() {
		if (!validateLogin() || $this->user->isPhoneVerified($_SESSION['user'])) {
			redirect('');
		}

		// checking if form was submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

			// checking if OTP was entered
			if (postVars($p, ['otp'])) {

				// sanitizing OTP
				$p['otp'] = strtoupper(filter_var($p['otp'], FILTER_SANITIZE_STRING));

				// fetching the OTP
				if ($otp = $this->user->getOTP($id)) {

					// comparing the OTP
					if ($otp === $p['otp']) {

						// storing this verfication
						$this->user->verifyPhone($id);
						redirect('');

					// error messages
					} else {
						enqueueErrorMessage('Wrong OTP');
					}
				} else {
					enqueueErrorMessage('OTP has expired');
				}
			} else {
				enqueueErrorMessage('Please enter valid details in all form fields');
			}
		}

		// loading the view
		$this->view('users/verify_otp', $this->data);
	}
}