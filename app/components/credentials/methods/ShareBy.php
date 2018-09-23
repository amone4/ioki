<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareBy extends Credentials {

	// displays all credentials that have been shared by us
	public function __construct() {
		parent::__construct();

		// getting the credentials from shared table
		$encrypted =  $this->shared->selectWhere(['shared_by' => $this->user]);
		if ($this->shared->rowCount() === 0)
			Output::info('No credentials have been shared by you');
		$encrypted = Misc::toArray($encrypted);

		// getting all credentials from credentials table
		$credentials = $this->credential->selectWhere(['user' => $this->user]);
		$credentials = Misc::toArray($credentials);

		// decrypting the credentials
		$decrypted = [];
		foreach ($encrypted as $key => $value)
			foreach ($credentials as $credential)
				if ($value->credential == $credential->id) {
					$decrypted[$key]['login'] = Crypt::decryptBlowfish($credential->login, $this->key);
					break;
				}

		Output::view('share_by', ['encrypted' => $encrypted, 'decrypted' => $decrypted]);
	}
}