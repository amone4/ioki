<?php

defined('_INDEX_EXEC') or die('Restricted access');

abstract class Model {
	protected $database;
	protected $tableName;
	protected $primaryKey;

	public function __construct() {
		$this->database = new Database();
	}

	// function to get the primary key name of the table
	private function getPrimaryKey() {
		$this->database->query('SHOW KEYS FROM ' . $this->tableName . ' WHERE Key_name = \'PRIMARY\'');
		$row = $this->database->single();
		return $row->Column_name;
	}

	// function to insert a record
	public function insert($data) {
		if (!isset($this->tableName) || empty($this->tableName))
			$this->tableName = strtolower(get_called_class() . 's');

		$query1 = 'INSERT INTO ' . $this->tableName . '(';
		$query2 = ') VALUES (';
		$query3 = ')';

		foreach ($data as $key => $value) {
			$query1 .= $key . ', ';
			$query2 .= ':' . $key . ', ';
		}
		$query1 = chop($query1, ', ');
		$query2 = chop($query2, ', ');

		$this->database->query($query1 . $query2 . $query3);

		foreach ($data as $key => $value) {
			$this->database->bind($key, $value);
		}

		return $this->database->execute();
	}

	// function to find a record using the primary key
	public function select($id = null) {
		if ($id == null) return $this->selectWhere();

		if (!isset($this->tableName) || empty($this->tableName))
			$this->tableName = strtolower(get_called_class() . 's');
		if (!isset($this->primaryKey) || empty($this->primaryKey))
			$this->primaryKey = $this->getPrimaryKey();

		$query = 'SELECT * FROM ' . $this->tableName;
		$query .= ' WHERE ' . $this->primaryKey . ' = :id';
		$this->database->query($query);
		$this->database->bind('id', $id);
		return $this->database->single();
	}

	// function to find records using (key => value) pairs
	public function selectWhere($clause = null, $convertToArray = false) {
		if (!isset($this->tableName) || empty($this->tableName)) $this->tableName = strtolower(get_called_class() . 's');

		$query = 'SELECT * FROM ' . $this->tableName;

		if ($clause != null) {
			$query .= ' WHERE ';
			foreach ($clause as $key => $value) {
				$query .= $key . ' = :' . $key . ' AND ';
			}
			$query = chop($query, ' AND ');
		}

		$this->database->query($query);

		if ($clause != null) {
			foreach ($clause as $key => $value) {
				$this->database->bind($key, $value);
			}
		}

		$set = $this->database->resultSet();
		if ($this->database->rowCount() > 1 || $convertToArray) return $set;
		else if ($this->database->rowCount() === 1) return $set[0];
		else return null;
	}

	// function to update a record using the primary key
	public function update($id, $data) {
		if (!isset($this->tableName) || empty($this->tableName))
			$this->tableName = strtolower(get_called_class() . 's');
		if (!isset($this->primaryKey) || empty($this->primaryKey))
			$this->primaryKey = $this->getPrimaryKey();

		$query = 'UPDATE ' . $this->tableName . ' SET ';

		foreach ($data as $key => $value) {
			$query .= $key . ' = :' . $key . ', ';
		}
		$query = chop($query, ', ');

		$query .= ' WHERE ' . $this->primaryKey . ' = :' . $this->primaryKey;

		$this->database->query($query);

		foreach ($data as $key => $value) {
			$this->database->bind($key, $value);
		}
		$this->database->bind($this->primaryKey, $id);

		return $this->database->execute();
	}

	// function to update records using (key => value) pairs
	public function updateWhere($data, $clause = null) {
		if (!isset($this->tableName) || empty($this->tableName))
			$this->tableName = strtolower(get_called_class() . 's');

		$query = 'UPDATE ' . $this->tableName . ' SET ';

		foreach ($data as $key => $value) {
			$query .= $key . ' = :' . $key . '1, ';
		}
		$query = chop($query, ', ');

		if ($clause != null) {
			$query .= ' WHERE ';
			foreach ($clause as $key => $value) {
				$query .= $key . ' = :' . $key . '2 AND ';
			}
			$query = chop($query, ' AND ');
		}

		$this->database->query($query);

		foreach ($data as $key => $value) {
			$this->database->bind($key . '1', $value);
		}

		if ($clause != null) {
			foreach ($clause as $key => $value) {
				$this->database->bind($key . '2', $value);
			}
		}

		return $this->database->execute();
	}

	// function to delete a record using the primary key
	public function delete($id) {
		if (!isset($this->tableName) || empty($this->tableName))
			$this->tableName = strtolower(get_called_class() . 's');
		if (!isset($this->primaryKey) || empty($this->primaryKey))
			$this->primaryKey = $this->getPrimaryKey();

		$query = 'DELETE FROM ' . $this->tableName;
		if ($id != null) $query .= ' WHERE ' . $this->primaryKey . ' = :id';
		$this->database->query($query);
		if ($id != null) $this->database->bind('id', $id);
		return $this->database->execute();
	}

	// function to delete records using (key => value) pairs
	public function deleteWhere($clause = null) {
		if (!isset($this->tableName) || empty($this->tableName))
			$this->tableName = strtolower(get_called_class() . 's');

		$query = 'DELETE FROM ' . $this->tableName;

		if ($clause != null) {
			$query .= ' WHERE ';
			foreach ($clause as $key => $value)
				$query .= $key . ' = :' . $key . ' AND ';
			$query = chop($query, ' AND ');
		}

		$this->database->query($query);

		if ($clause != null)
			foreach ($clause as $key => $value)
				$this->database->bind($key, $value);

		return $this->database->execute();
	}

	// function to return the row count for previous query
	public function rowCount() {
		return $this->database->rowCount();
	}
}