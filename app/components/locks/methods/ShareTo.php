<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareTo extends Locks {

	// displays all locks that have been shared to us
	public function __construct() {
		parent::__construct();

		// getting the locks from shared table
		$locks =  $this->shared->sharedTo($this->user);
		if ($this->shared->rowCount() === 0)
			Output::info('No locks have been shared to you');

		Output::view('share_to', $locks);
	}
}