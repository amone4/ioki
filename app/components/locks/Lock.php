<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Lock extends Model {
	public function __construct() {
		parent::__construct();
		$this->tableName = 'locks';
	}
}