<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Locks extends Controller {
	protected $lock;
	protected $shared;
	protected $user;

	// constructor function
	public function __construct() {
		// validating that the user is logged in
		if (!Misc::validateLogin()) Output::redirect();
		// using the models
		$this->lock = $this->getModel();
		$this->shared = $this->getModel('Shared');
		// decrypting the user ID
		$this->user = Crypt::decryptBlowfish($_SESSION['user']);
	}

	// function to view all added credentials
	public function index() {
		$locks = $this->lock->selectWhere(['user' => $this->user]);
		if ($this->lock->rowCount() === 0)
			Output::info('No locks found');
		$locks = Misc::toArray($locks);

		foreach ($locks as &$lock)
			$lock->secret = null;

		Output::view('view', $locks);
	}

	// controller function to handle requests related to sharing of a credential
	public function share($request, $id = null) {
		$method = 'share' . ucwords($request);
		if ($id !== null) App::dispatchMethod($method, [$id]);
		else App::dispatchMethod($method);
	}
}