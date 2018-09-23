<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Delete extends Credentials {

	// deletes a new credential
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$credential = $this->credential->select($id);
			if ($this->credential->rowCount() === 1 && $credential->user == $this->user) {

				if ($this->shared->deleteWhere(['credential' => $id]) && $this->credential->delete($id))
					Output::success('Credential successfully deleted');
				else
					Output::error('Some error occurred while deleting the credentials');
				Output::redirect('credentials');

			} else Output::fatal();
		} else Output::fatal();
	}
}