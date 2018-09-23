<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Shared extends Model {
	public function __construct() {
		parent::__construct();
		$this->tableName = 'shared';
	}
}