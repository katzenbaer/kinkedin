<?php
class EmptyFieldException extends Exception {}
class InvalidCredentialsException extends Exception {}
class PasswordLengthException extends Exception {}
class UserAlreadyExistsException extends Exception {}

class User {
	public $mysqli;
	public $email, $firstName, $lastName, $password;
	
	function User($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	function findByName($name) {
		$stmt = <<<SQL
SELECT `email`, `first_name`, `last_name`, `password` FROM `users`
		WHERE `name` = ?
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('s', $name);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($email, $firstName, $lastName, $password);
			while ($result = $stmt->fetch()) {
				$user = new self();
				$user->email = $email;
				$user->firstName = $firstName;
				$user->lastName = $lastName;
				$user->password = $password;
				return $user;
			}
			return null;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	function authenticate($email, $pass) {
		// Input Sanitization Checks
		if (empty($email) || empty($pass)) {
			throw new EmptyFieldException('Email or Password is empty.');
		}
		
		$stmt = <<<SQL
SELECT `email` FROM `users`
WHERE `email` = LOWER(?) AND `password` = SHA(LOWER(?))
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('ss', $email, $pass);
			
			//$stmt->bind_result($email, $firstName, $lastName, $password);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			if ($result = $stmt->fetch()) {
				return true;
			}
			
			throw new InvalidCredentialsException();
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	function registerEmptyFields($form) {
		$fields = array('firstName', 'lastName', 'email', 'password');
		
		$errors = array();
		foreach ($fields as $field) {
			if (empty($form[$field])) {
				$errors[] = '#signup' . ucfirst($field);
			}
		}
		return $errors;
	}
	
	function register($form) {
		if (count(self::registerEmptyFields($form)) > 0) {
			throw new EmptyFieldException();
		}
		
		if (strlen($form['password']) < 6) {
			throw new PasswordLengthException('Passwords must be 6 characters minimum.');
		}
		
		$stmt = <<<SQL
INSERT INTO `users` (`email`, `first_name`, `last_name`, `password`)
VALUES (LOWER(?), ?, ?, SHA(LOWER(?)))
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('ssss', $form['email'], $form['firstName'], $form['lastName'], $form['password']);
			
			if ($stmt->execute() === FALSE) {
				if ($this->mysqli->errno === 1062) {
					throw new UserAlreadyExistsException();
				} else {
					die('unable to execute (' . $this->mysqli->errno . ') - ' . htmlspecialchars($this->mysqli->error));
				}
			}
			
			return true;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
}
?>