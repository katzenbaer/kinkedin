<?php
require_once __DIR__ . '/UserProfile.php';

class EmptyFieldException extends Exception {}
class InvalidCredentialsException extends Exception {}
class PasswordLengthException extends Exception {}
class UserAlreadyExistsException extends Exception {}
class UserDoesNotExistException extends Exception {}

class User {
	public $mysqli;
	private $id, $email, $firstName, $lastName, $password;
	private $profile = NULL;
	
	function User($mysqli, $id, $email, $firstName, $lastName, $password) {
		$this->mysqli = $mysqli;
		
		$this->id = $id;
		$this->email = $email;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->password = $password;
	}
	
	/**
	 * Returns the user's id.
	 * @return Integer
	 */
	function getId() {
		return $this->id;
	}
	
	/**
	 * Returns the user's email.
	 * @return String
	 */
	function getEmail() {
		return $this->email;
	}
	
	/**
	 * Returns the user's full name.
	 * @return String
	 */
	function getFullName() {
		return $this->getFirstName() . ' ' . $this->getLastName();
	}
	
	/**
	 * Returns the user's first name.
	 * @return String
	 */
	function getFirstName() {
		return $this->firstName;
	}
	
	/**
	 * Returns the user's last name.
	 * @return String
	 */
	function getLastName() {
		return $this->lastName;
	}
	
	/**
	 * Returns the user's password hash.
	 * @return String
	 */
	function getPasswordHash() {
		return $this->password;
	}
	
	/**
	 * Returns the user's profile.
	 * @return UserProfile
	 */
	function getProfile() {
		if ($this->profile == NULL) {
			$this->profile = new UserProfile($this->mysqli, $this->getEmail(), $this->getId());
		}
		return $this->profile;
	}
	
	/**
	 * Returns whether this user is connected to another user.
	 * @return Boolean
	 */
	function hasConnection($email, $state = 'accepted') {
		if ($email == $this->getEmail()) {
			return false;
		}
		$otherUser = (new Users($this->mysqli))->findByEmail($email);
		if ($otherUser == NULL) {
			(new Session())->destroy();
			return false;
		}
		
		if ($otherUser->getId() < $this->getId()) {
			$firstId = $otherUser->getId();
			$secondId = $this->getId();
		} else {
			$firstId = $this->getId();
			$secondId = $otherUser->getId();
		}
		
		$stmt = <<<SQL
SELECT COUNT(*) FROM `connections`
WHERE `to_user` = ? AND `from_user` = ? AND `status` = '{$state}'
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('ii', $firstId, $secondId);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();
			return $count > 0;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	/**
	 * Sends a Connection request to a user with toUserId.
	 * @return Boolean whether the request was successful.
	 */
	function request($toUserId) {
		$otherUser = (new Users($this->mysqli))->findById($toUserId);
		if ($otherUser == NULL) {
			throw new UserDoesNotExistException();
		}
		
		if ($this->getId() < $toUserId) {
			$firstId = $this->getId();
			$secondId = $toUserId;
		} else {
			$firstId = $toUserId;
			$secondId = $this->getId();
		}
		
		$stmt = <<<SQL
INSERT INTO `connections` (`to_user`, `from_user`, `status`, `timestamp`)
VALUES (?, ?, 'pending', UNIX_TIMESTAMP())
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('ii', $firstId, $secondId);
			
			if ($stmt->execute() === FALSE) {
				return false;
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			return true;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	/**
	 * Returns a list of Users that this user is connected with.
	 * @return Array
	 */
	function getConnections($status = 'accepted', $limit = 10) {
		$res = array();

		// query where we are the to_user
		$stmt = <<<SQL
SELECT `id`, `email`, `first_name`, `last_name`, `password` FROM `connections`
INNER JOIN `users` ON `connections`.`from_user` = `users`.`id`
WHERE `status` = '{$status}' AND `to_user` = ?
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('i', $this->getId());
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($id, $email, $firstName, $lastName, $password);
			while ($result = $stmt->fetch()) {
				$res[] = new User($this->mysqli, $id, $email, $firstName, $lastName, $password);
			}
			$stmt->close();
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
		
		// query where we are the from_user
		$stmt = <<<SQL
SELECT `id`, `email`, `first_name`, `last_name`, `password` FROM `connections`
INNER JOIN `users` ON `connections`.`to_user` = `users`.`id`
WHERE `status` = '{$status}' AND `from_user` = ?
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('i', $this->getId());
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($id, $email, $firstName, $lastName, $password);
			while ($result = $stmt->fetch()) {
				$res[] = new User($this->mysqli, $id, $email, $firstName, $lastName, $password);
			}
			$stmt->close();
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
		
		return array_slice($res, 0, $limit);
	}
}

class Users {
	private $mysqli;
	
	function Users($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	 * Fetches a User from the database using the given id.
	 * @return User
	 */
	function findById($id) {
		$stmt = <<<SQL
SELECT `id`, `email`, `first_name`, `last_name`, `password` FROM `users`
WHERE `id` = ?
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('i', $id);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($id, $email, $firstName, $lastName, $password);
			if ($result = $stmt->fetch()) {
				return new User($this->mysqli, $id, $email, $firstName, $lastName, $password);
			}
			$stmt->close();
			return null;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	/**
	 * Fetches a User from the database using the given brand.
	 */
	function findByBrand($brand) {
		$stmt = <<<SQL
SELECT `id`, `email`, `first_name`, `last_name`, `password` FROM `users`
WHERE `brand` = ?
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('s', $brand);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($id, $email, $firstName, $lastName, $password);
			while ($result = $stmt->fetch()) {
				return new User($this->mysqli, $id, $email, $firstName, $lastName, $password);
			}
			$stmt->close();
			return null;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	/**
	 * Fetches a User from the database using the given email.
	 */
	function findByEmail($email) {
		$stmt = <<<SQL
SELECT `id`, `email`, `first_name`, `last_name`, `password` FROM `users`
WHERE `email` = ?
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('s', $email);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($id, $email, $firstName, $lastName, $password);
			while ($result = $stmt->fetch()) {
				return new User($this->mysqli, $id, $email, $firstName, $lastName, $password);
			}
			$stmt->close();
			return null;
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
	
	/**
	 * Returns whether the credentials are valid.
	 * @return Boolean
	 */
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
	
	/**
	 * Returns a list of empty fields in the form for error purposes.
	 * @return Array
	 */
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
	
	/**
	 * Attempts to register a user with the data in form.
	 * @return Boolean
	 * @throws EmptyFieldException if any required fields are empty.
	 * @throws PasswordLengthException if the password is less than 6 characters.
	 * @throws UserAlreadyExistsException if the user already exists.
	 */
	function register($form) {
		if (count($this->registerEmptyFields($form)) > 0) {
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