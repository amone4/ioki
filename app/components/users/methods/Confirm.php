<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Confirm extends Users {

	// controller for confirmations
	public function __construct($request, $code = null) {
		parent::__construct();

		if ($request === 'email' && $code !== null) $this->confirmEmail($code);
		else if ($request === 'phone') $this->confirmPhone();
		else Output::fatal();
	}

	// function to confirm email
	private function confirmEmail($code) {
		// checking if the user is logged in
		if (Misc::validateLogin()) {
			Output::info('You can\'t confirm your email, until you\'re logged in');
			App::dispatchMethod('logout');
		}

		// validating code and fetching ID
		$code = filter_var($code, FILTER_SANITIZE_STRING);
		$id = Crypt::decryptBlowfish($code);
		$row = $this->user->select($id);

		if ($this->user->rowCount() === 1) {
			if (($row->code === $code) && (time() - $row->code_sent_on < 864000)) {
				// confirming email
				if ($this->user->update($id, ['confirm_email' => 1, 'code' => '0'])) {

					Output::success('Your email has been confirmed successfully. Login to proceed');
					Output::redirect('users');

				// error messages
				} else Output::fatal('Some error occurred while confirming your email. Try again');
			} else Output::fatal('Your code has expired');
		} else Output::fatal();
	}

	// function to confirm phone number
	private function confirmPhone() {
		// checking if the user is logged in
		if (!Misc::validateLogin()) App::dispatchMethod('logout');

		// checking if phone verification is needed
		$user = $this->user->select(Crypt::decryptBlowfish($_SESSION['user']));
		if ($user->confirm_phone == 1) {
			Output::info('Your phone number has already been verified');
			Output::redirect();
		}

		// checking if the form has been submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields have been filled
			if (Forms::post($p, ['otp'])) {
				// sanitizing data
				$p['otp'] = filter_var($p['otp'], FILTER_SANITIZE_NUMBER_INT);

				// checking if the OTP is valid
				if (ctype_digit($p['otp'])) {
					if (($p['otp'] == $user->otp) && (time() - $user->otp_sent_on < 864000)) {
						// confirming phone number
						if ($this->user->update($user->id, ['confirm_phone' => 1, 'otp' => '0'])) {

							Output::success('Your phone number was successfully confirmed');
							Output::redirect();

						// error messages
						} else Output::error('Some error occurred while confirming your phone number. Try again');
					} else Output::error('Invalid OTP');
				} else Output::error('Invalid OTP');
			} else Output::error('Please enter valid details in all form fields');
		}

		Output::view('confirm_phone');
	}
}