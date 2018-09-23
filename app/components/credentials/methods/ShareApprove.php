<?php

defined('_INDEX_EXEC') or die('Restricted access');

class ShareApprove extends Credentials {

	// approves a request to share a credential with us
	public function __construct($id) {
		parent::__construct();

		if (ctype_digit($id)) {
			$row = $this->shared->select($id);
			if ($this->shared->rowCount() === 1 && $row->shared_to == $this->user) {

				if ($this->shared->delete($id)) Output::success('Credential successfully rejected for sharing');
				else Output::error('Some error occurred while rejecting this request');
				Output::redirect('credentials/share/to');

			} else Output::fatal();
		} else Output::fatal();
	}
}