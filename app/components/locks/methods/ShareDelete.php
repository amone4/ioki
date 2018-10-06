<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareDelete extends Locks {

	// takes back a lock
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_by == $this->user) {

				if ($this->shared->delete($id)) Output::success('Lock is no longer shared with the user');
				else Output::error('Some error occurred while taking back the lock');
				Output::redirect('locks/share/by');

			} else Output::fatal();
		} else Output::fatal();
	}
}