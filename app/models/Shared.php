<?php

class Shared extends Model {
	public function __construct() {
		$this->tableName = 'shared';
		parent::__construct();
	}
}