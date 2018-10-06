<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Update extends Locks {

	// updates a lock
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$lock = $this->lock->select($id);
			if ($this->lock->rowCount() === 1 && $lock->user == $this->user) {

				// checking if the form was submitted
				if (Forms::isSubmitted()) {
					// checking if the form fields were filled
					if (Forms::post($p, ['name'])) {
						// sanitizing data
						$p['name'] = filter_var($p['name'], FILTER_SANITIZE_STRING);
						if ($this->lock->update($id,['name' => $p['name']])) {

							Output::success('Lock successfully updated');
							Output::redirect('locks/update/' . $id);

						// error messages
						} else Output::error('Some error occurred while updating the lock');
					} else Output::error('Enter valid details in all form fields');
				}

				$lock->secret = null;
				Output::view('update', $lock);

			} else Output::fatal();
		} else Output::fatal();
	}
}