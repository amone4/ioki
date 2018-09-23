<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareDelete extends Credentials {

	// takes back a credential
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_by == $this->user) {

				if ($this->shared->delete($id)) Output::success('Credential is no longer shared with the user');
				else Output::error('Some error occurred while taking back the credential');
				Output::redirect('credentials/share/by');

			} else Output::fatal();
		} else Output::fatal();
	}
}