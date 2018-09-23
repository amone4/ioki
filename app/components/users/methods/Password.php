<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Password extends Users {

	// controller for handling password related requests
	public function __construct($request, $code = null) {
		parent::__construct();

		if ($request === 'forgot') $this->passwordForgot();
		else if ($request === 'reset' && $code !== null) $this->passwordReset($code);
		else if ($request === 'change') $this->passwordChange();
		else Output::fatal();
	}

	// function for forgot password
	private function passwordForgot() {
		// checking if the user is logged in
		if (Misc::validateLogin()) Output::redirect();

		// checking if the form was submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields were filled
			if (Forms::post($p, ['email'])) {
				// sanitizing data
				$p['email'] = filter_var($p['email'], FILTER_SANITIZE_EMAIL);
				// checking if the email entered is valid
				if (Validations::email($p['email'])) {
					if (($row = $this->user->selectWhere(['email' => $p['email']])) && $this->user->rowCount() === 1) {

						// generating confirmation code for email
						$code = Crypt::encryptBlowfish($row->id);
						// mailing the confirmation code
						$message = '<p>Reset your password by clicking on the link below<br><a href="' . URLROOT . '/users/password/reset/' . $code . '">Reset password</a></p>';
						if (Misc::writeMessage($message, 'code.txt') || mail($p['email'], 'Reset your password', $message, 'From: noreply@example.com' . "\r\n")) {
							// updating the confirmation code
							if ($this->user->update($row->id, ['code' => $code, 'code_sent_on' => time()])) {

								Output::success('Link to reset the password was successfully sent.');
								Output::redirect('users');

							// error messages
							} else Output::error('Some error occurred. Try again');
						} else Output::error('Some error occurred while sending the mail. Try again');
					} else Output::error('This email is not registered');
				} else Output::error('Invalid email');
			} else Output::error('Please enter valid details in all form fields');
		}

		Output::view('password_forgot');
	}

	// function to reset password
	private function passwordReset($code) {
		// checking if the user is logged in
		if (Misc::validateLogin()) {
			Output::info('You can\'t reset your password, because you\'re logged in');
			App::dispatchMethod('logout');
		}

		// validating code and fetching ID
		$code = filter_var($code, FILTER_SANITIZE_STRING);
		$id = Crypt::decryptBlowfish($code);
		if (($row = $this->user->select($id)) && $this->user->rowCount() === 1) {
			if ($row->code === $code) {

				// checking if the form has been submitted
				if (Forms::isSubmitted()) {
					// checking if the form fields have been filled
					if (Forms::post($p, ['password', 'confirmPassword'])) {
						// validating passwords
						if (Validations::password($p['password']) && Validations::password($p['confirmPassword'])) {
							// checking if the passwords match
							if ($p['password'] === $p['confirmPassword']) {

								// deleting all unapproved credentials which were shared to the user
								$share = $this->getModel('credentials/Shared');
								if ($share->deleteWhere(['shared_to' => $id, 'approved' => 0])) {

									// confirmation code reset and password reset. The flag is also set to `reset` stage
									$p['password'] = password_hash($p['password'], PASSWORD_DEFAULT);
									if ($this->user->update($id, ['code' => '0', 'old_hash' => $row->password, 'flag' => 10, 'password' => $p['password']])) {

										Output::success('Your password has been successfully reset. Login to proceed');
										Output::redirect('users');

									// error messages
									} else Output::error('Some error occurred while resetting your password. Try again');
								} else Output::error('Some error occurred while resetting your password. Try again');
							} else Output::error('Passwords don\'t match');
						} else Output::error('Invalid password');
					} else Output::error('Please enter valid details in all form fields');
				}

				Output::view('password_reset', $code);

			} else Output::fatal();
		} else Output::fatal();
	}

	// function to change password
	private function passwordChange() {
		// checking if the user is logged in
		if (!Misc::validateLogin()) App::dispatchMethod('logout');

		// checking if the form has been submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields have been filled
			if (Forms::post($p, ['oldPassword', 'newPassword', 'confirmPassword'])) {
				// validating passwords
				if (Validations::password($p['oldPassword']) && Validations::password($p['newPassword']) && Validations::password($p['confirmPassword'])) {
					// checking if the passwords match
					if ($p['newPassword'] === $p['confirmPassword']) {

						$user = $this->user->select(Crypt::decryptBlowfish($_SESSION['user']));
						// checking if the old password is correct
						if (password_verify($p['oldPassword'], $user->password)) {

							// setting the flag
							$this->user->update($user->id, ['flag' => 1]);
							// changing encryption of all credentials
							if ($this->changeCredentialEncryption($user->id, $p['oldPassword'], $p['newPassword'])) {

								// changing the password
								$p['newPassword'] = password_hash($p['newPassword'], PASSWORD_DEFAULT);
								if ($this->user->update($user->id, ['password' => $p['newPassword'], 'old_hash' => $user->old_password, 'flag' => 0])) {

									Output::success('Your password has been successfully changed. Login again to continue');
									App::dispatchMethod('logout');

								// error messages
								} else Output::error('Some error occurred while changing your password');
							} else Output::error('Some error occurred while changing encryption of your credentials');
						} else Output::error('Old password is incorrect');
					} else Output::error('Passwords don\'t match');
				} else Output::error('Invalid password');
			} else Output::error('Please enter valid details in all form fields');
		}

		Output::view('password_change');
	}
}