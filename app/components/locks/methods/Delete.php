<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Delete extends Locks {

	// deletes a lock
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$lock = $this->lock->select($id);
			if ($this->lock->rowCount() === 1 && $lock->user == $this->user) {

				if ($this->shared->deleteWhere(['lock_id' => $id]) && $this->lock->update($id, ['user' => null, 'name' => null]))
					Output::success('Lock successfully deleted');
				else
					Output::error('Some error occurred while deleting the Lock');
				Output::redirect('locks');

			} else Output::fatal();
		} else Output::fatal();
	}
}