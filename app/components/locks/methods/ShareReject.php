<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareReject extends Locks {

	// rejects a request to share a lock with us
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_to == $this->user) {
				if ($this->shared->delete($id)) Output::success('Lock successfully rejected for sharing');
				else Output::error('Some error occurred while rejecting this request');
				Output::redirect('locks/share/to');
			} else Output::fatal();
		} else Output::fatal();
	}
}