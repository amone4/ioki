<?php

class Credential extends Model {
	public function __construct() {
		$this->tableName = 'credentials';
		parent::__construct();
	}
}