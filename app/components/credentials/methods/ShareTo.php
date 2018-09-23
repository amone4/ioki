<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareTo extends Credentials {

	// displays all credentials that have been shared to us
	public function __construct() {
		parent::__construct();

		$encrypted =  $this->shared->selectWhere(['shared_to' => $this->user]);
		if ($this->shared->rowCount() === 0) {
			Output::info('No credentials have been shared to you');
		}
		$encrypted = Misc::toArray($encrypted);

		$decrypted = [];
		$user = $this->getModel('users/User');
		$password = $user->select($this->user)->password;
		foreach ($encrypted as $key => $value) {
			if ($value->approved == 0) $decrypted[$key]['login'] = Crypt::decryptBlowfish($value->login, $password);
			else $decrypted[$key]['login'] = Crypt::decryptBlowfish($value->login, $this->key);
		}

		Output::view('share_to', ['encrypted' => $encrypted, 'decrypted' => $decrypted]);
	}
}