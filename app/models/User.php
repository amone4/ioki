<?php

class User {
	private $database;

	public function __construct() {
		$this->database = new Database();
	}

	public function validateCredentials($data) {
		$this->database->query('SELECT id, password FROM users WHERE username = :username');
		$this->database->bind('username', $data['username'], PDO::PARAM_STR);
		$single = $this->database->single();
		if ($this->database->rowCount() === 1) {
			if (password_verify($data['password'], $single->password)) {
				return $single->id;
			}
		}
		return false;
	}

	public function fieldExists($field, $value) {
		$this->database->query('SELECT id FROM users WHERE ' . $field . ' = :' . $field);
		$this->database->bind($field, $value, PDO::PARAM_STR);
		$this->database->execute();
		if ($this->database->rowCount() === 1) {
			return $this->database->single()->id;
		} else {
			return false;
		}
	}

	public function register($data) {
		$this->database->query('INSERT INTO users (username, name, email, password, phone) VALUES (:username, :name, :email, :password, :phone)');
		$this->database->bind('username', $data['username'], PDO::PARAM_STR);
		$this->database->bind('name', $data['name'], PDO::PARAM_STR);
		$this->database->bind('email', $data['email'], PDO::PARAM_STR);
		$this->database->bind('phone', $data['phone'], PDO::PARAM_STR);
		$this->database->bind('password', $data['password'], PDO::PARAM_STR);
		if ($this->database->execute()) {
			$this->database->query('SELECT last_insert_id() AS id FROM users');
			return $this->database->single()->id;
		} else {
			return 0;
		}
	}

	public function serialNumberExists($id) {
		$this->database->query('SELECT id FROM users WHERE id = :id');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		$this->database->execute();
		if ($this->database->rowCount() === 1) {
			return $this->database->single()->id;
		} else {
			return false;
		}
	}

	public function confirmEmail($id) {
		$this->database->query('UPDATE users SET confirm_email = 1 WHERE id = :id');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		return $this->database->execute();
	}

	public function confirmPassword($data) {
		$this->database->query('SELECT password FROM users WHERE id = :id');
		$this->database->bind('id', $data['id'], PDO::PARAM_INT);
		$password = $this->database->single()->password;
		return password_verify($data['password'], $password);
	}

	public function changePassword($data) {
		$this->database->query('UPDATE users SET password = :password WHERE id = :id');
		$this->database->bind('id', $data['id'], PDO::PARAM_INT);
		$this->database->bind('password', $data['password'], PDO::PARAM_STR);
		return $this->database->execute();
	}

	public function storeResetCode($id, $code) {
		$this->database->query('UPDATE users SET reset_password = :code WHERE id = :id');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		$this->database->bind('code', $code, PDO::PARAM_STR);
		return $this->database->execute();
	}

	public function verifyResetCode($id, $code) {
		$this->database->query('SELECT id FROM users WHERE id = :id AND reset_password = :code');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		$this->database->bind('code', $code, PDO::PARAM_STR);
		$this->database->execute();
		return $this->database->rowCount() === 1;
	}

	public function resetPassword($data) {
		$this->database->query('UPDATE users SET password = :password, reset_password = 0 WHERE id = :id');
		$this->database->bind('id', $data['id'], PDO::PARAM_INT);
		$this->database->bind('password', $data['password'], PDO::PARAM_STR);
		return $this->database->execute();
	}

	public function setOTP($data) {
		$this->database->query('UPDATE users SET otp = :otp WHERE id = :id');
		$this->database->bind('otp', $data['otp'], PDO::PARAM_STR);
		$this->database->bind('id', $data['id'], PDO::PARAM_INT);
		return $this->database->execute();
	}

	public function getOTP($id) {
		$this->database->query('SELECT otp FROM users WHERE id = :id');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		return $this->database->single()->otp;
	}

	public function verifyPhone($id) {
		$this->database->query('UPDATE users SET otp = 1 WHERE id = :id');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		return $this->database->execute();
	}

	public function isPhoneVerfied($id) {
		$this->database->query('SELECT otp FROM users WHERE id = :id');
		$this->database->bind('id', $id, PDO::PARAM_INT);
		return $this->database->single()->otp === 1;
	}
}