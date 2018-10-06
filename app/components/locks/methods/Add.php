<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Add extends Locks {

	// adds a new lock
	public function __construct() {
		parent::__construct();

		// checking if the form was submitted
		if (Forms::isSubmitted()) {
			// checking if the form fields were filled
			if (Forms::post($p, ['id', 'name'], $err)) {
				// sanitizing data
				$p['id'] = filter_var($p['id'], FILTER_SANITIZE_NUMBER_INT);
				$p['name'] = filter_var($p['name'], FILTER_SANITIZE_STRING);
				// confirming that the lock exists
				$result = $this->lock->select($p['id']);
				if ($this->lock->rowCount() === 1) {
					// confirming that the lock hasn't already been added
					if (empty($result->user)) {
						// assigning lock to the user
						if ($this->lock->update($p['id'], ['user' => $this->user, 'name' => $p['name']])) {

							Output::success('Lock successfully added');
							Output::redirect('locks');

						// error messages
						} else Output::error('Some error occurred while inserting the new credential');
					} else Output::error('You or Another user already owns the lock');
				} else Output::error('Lock does not exist');
			} else Output::error('Enter valid details in all form fields ' . $err);
		}

		Output::view('add');
	}
}