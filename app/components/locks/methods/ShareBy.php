<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareBy extends Locks {

	// displays all locks that have been shared by us
	public function __construct() {
		parent::__construct();

		// getting the locks from shared table
		$locks =  $this->shared->sharedBy($this->user);
		if ($this->shared->rowCount() === 0)
			Output::info('No locks have been shared by you');

		Output::view('share_by', $locks);
	}
}