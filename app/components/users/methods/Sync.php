<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Sync extends Users {

	// function to decrypt credentials from old key, and use new key to encrypt them
	public function __construct() {
		parent::__construct();

		if (Misc::validateLogin() && App::get('isAPIRequest')) {
			if (isset($_POST['old']) && !empty($_POST['old'])) {
				// getting the user details
				$user = $this->user->select(Crypt::decryptBlowfish($_SESSION['user']));
				// checking if syncing is required
				if ($user->flag == 10) {
					// making the decryption key
					$decrypt = Crypt::decryptBlowfish($_POST['old'], $user->old_hash);
					// making the encryption key
					$encrypt = Crypt::decryptBlowfish($_SESSION['pass'], $user->password);
					// setting the syncing flag
					$this->user->update($user->id, ['flag' => 1]);
					// changing encryption
					if ($this->changeCredentialEncryption($user->id, $decrypt, $encrypt)) {
						// changing flag to normal
						$this->user->update($user->id, ['flag' => 0]);

						// success message
						Output::success('Success');

					// error messages
					} else Output::error('Error');
				} else Output::fatal();
			} else Output::fatal();
		} else Output::fatal();
	}
}