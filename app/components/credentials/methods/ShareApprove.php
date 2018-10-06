<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareApprove extends Credentials {

	// approves a request to share a credential with us
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_to == $this->user) {

				// getting the decrypting hash
				$hash = $this->getModel('users/User')->select($this->user)->password;

				// changing encryption
				$row->login = Crypt::encryptBlowfish(Crypt::decryptBlowfish($row->login, $hash), $this->key);
				$row->password = Crypt::encryptBlowfish(Crypt::decryptBlowfish($row->password, $hash), $this->key);

				// updating in database
				if ($this->shared->update($id, ['approved' => 1, 'login' => $row->login, 'password' => $row->password]))
					Output::success('Credential successfully accepted for sharing');

				else Output::error('Some error occurred while accepting this request');

				Output::redirect('credentials/share/to');

			} else Output::fatal();
		} else Output::fatal();
	}
}