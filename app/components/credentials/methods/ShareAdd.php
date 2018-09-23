<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareAdd extends Credentials {

	// shares a credential
	public function __construct($id) {
		parent::__construct();

		// validating id the credential selected is correct
		if (ctype_digit($id)) {
			$credential = $this->credential->select($id);
			if ($this->credential->rowCount() === 1) {

				// checking if the form was submitted
				if (Forms::isSubmitted()) {
					// checking of the form fields are filled
					if (Forms::post($p, ['username', 'shared_till_date', 'shared_till_time'])) {
						// sanitizing data
						$p['username'] = filter_var($p['username'], FILTER_SANITIZE_STRING);
						$p['shared_till_date'] = filter_var($p['shared_till_date'], FILTER_SANITIZE_STRING);
						$p['shared_till_time'] = filter_var($p['shared_till_time'], FILTER_SANITIZE_STRING);
						// validating data
						if (Validations::date($p['shared_till_date'])) {
							if (Validations::time($p['shared_till_time'])) {
								if (Validations::username($p['username'])) {
									// checking if the user exits
									$user = $this->getModel('users/User');
									$result = $user->selectWhere(['username' => $p['username']]);
									if ($user->rowCount() === 1) {
										$user = $result;
										// checking if the credential is already shared with this user
										$this->shared->selectWhere(['credential' => $id, 'shared_to' => $user->id]);
										if ($this->shared->rowCount() === 0) {

											// setting the necessary variables
											$p['credential'] = $id;
											$p['link'] = $credential->link;
											$p['type'] = $credential->type;
											$p['login'] = Crypt::encryptBlowfish(Crypt::decryptBlowfish($credential->login, $this->key), $user->password);
											$p['password'] = Crypt::encryptBlowfish(Crypt::decryptBlowfish($credential->password, $this->key), $user->password);
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

													Output::success('Credential was successfully shared');
													Output::redirect('credentials/share/by');

												// error messages
												} else Output::error('Some error occurred while sharing the credential');
											} else Output::error('Select a sharing time, after current time');
										} else Output::error('Credential has already been shared by the user');
									} else Output::error('No user with such a username exists');
								} else Output::error('Invalid username');
							} else Output::error('Invalid time');
						} else Output::error('Invalid date');
					} else Output::error('Enter valid details in all form fields');
				}

				Output::view('share_add', $credential);

			} else Output::fatal();
		} else Output::fatal();
	}
}