<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Credential extends Model {
	public function __construct() {
		parent::__construct();
		$this->tableName = 'credentials';
	}
}