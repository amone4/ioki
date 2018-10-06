<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Shared extends Model {
	public function __construct() {
		parent::__construct();
		$this->tableName = 'shared_locks';
	}

	private function getSharedCredentials($user, $field) {
		$this->database->query('SELECT s.*, l.name FROM locks l, shared_locks s WHERE l.id = s.lock_id AND s.' . $field . ' = :user');
		$this->database->bind('user', $user, PDO::PARAM_INT);
		return $this->database->resultSet();
	}

	public function sharedBy($user) {
		return $this->getSharedCredentials($user, 'shared_by');
	}

	public function sharedTo($user) {
		return $this->getSharedCredentials($user, 'shared_to');
	}
}