<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareApprove extends Locks {

	// approves a request to share a lock with us
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_to == $this->user) {

				if ($this->shared->update($id, ['approved' => 1])) Output::success('Lock successfully accepted for sharing');
				else Output::error('Some error occurred while accepting this request');
				Output::redirect('locks/share/to');

			} else Output::fatal();
		} else Output::fatal();
	}
}