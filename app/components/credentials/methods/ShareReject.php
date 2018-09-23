<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareReject extends Credentials {

	// rejects a request to share a credential with us
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_to == $this->user) {

				if ($row->shared_till > time()) {

					// making the decrypting key
					$user = $this->getModel('users/User');
					$key = $user->select($this->user)->password;
					// changing encryption after approval
					$row->login = Crypt::encryptBlowfish(Crypt::decryptBlowfish($row->login, $key), $this->key);
					$row->password = Crypt::encryptBlowfish(Crypt::decryptBlowfish($row->password, $key), $this->key);

					if ($this->shared->update($id, ['approved' => 1, 'password' => $row->password, 'login' => $row->login])) {
						Output::success('Credential successfully accepted for sharing');
					} else Output::error('Some error occurred while accepting this request');
				} else {
					Output::error('Time has expired to accept this request of sharing a credential. The request is being deleted');
					$this->shared->delete($id);
				}

				Output::redirect('credentials/share/to');

			} else Output::fatal();
		} else Output::fatal();
	}
}