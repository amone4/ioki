<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Credentials extends Controller {
	protected $credential;
	protected $shared;
	protected $user;
	protected $key;

	// constructor function
	public function __construct() {
		// validating that the user is logged in
		if (!Misc::validateLogin()) Output::redirect();
		// using the models
		$this->credential = $this->getModel();
		$this->shared = $this->getModel('Shared');
		// decrypting the user ID
		$this->user = Crypt::decryptBlowfish($_SESSION['user']);
		// decrypting the user password to get the encryption-decryption key
		$user = $this->getModel('users/User');
		$this->key = Crypt::decryptBlowfish($_SESSION['pass'], $user->select($this->user)->password);
	}

	// function to view all added credentials
	public function index() {
		$encrypted = $this->credential->selectWhere(['user' => $this->user]);
		if ($this->credential->rowCount() === 0)
			Output::info('No credentials found');
		$encrypted = Misc::toArray($encrypted);

		$decrypted = [];
		foreach ($encrypted as $key => $value) {
			$decrypted[$key]['login'] = Crypt::decryptBlowfish($value->login, $this->key);
			$decrypted[$key]['password'] = Crypt::decryptBlowfish($value->password, $this->key);
		}

		Output::view('view', ['encrypted' => $encrypted, 'decrypted' => $decrypted]);
	}

	// controller function to handle requests related to sharing of a credential
	public function share($request, $id = null) {
		$method = 'share' . ucwords($request);
		if ($id !== null) App::dispatchMethod($method, $id);
		else App::dispatchMethod($method);
	}
}