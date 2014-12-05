<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Session.php';

class Authentication {
	private $users, $session;
	
	function Authentication($users, $session) {
		$this->users = $users;
		$this->session = $session;
	}
	
	function login($email, $password, &$message) {
		$message = NULL;
		try {
			if ($this->users->authenticate($email, $password) === true) {
				$this->session->establish($email);
				return true;
		 	} else {
 				$message = 'The credentials you submitted are invalid.';
		 	}
		} catch (Exception $e) {
			$message = 'A problem occured.';
		}
		return false;
	}
	
	function signup($form, &$errors, &$errorMessage) {
		$errors = array();
		$errorMessage = NULL;
		try {	
			if ($this->users->register($form) === true) {
				$this->session->establish($form['email']);
			}
			return true;
		} catch (EmptyFieldException $e) {
			$errors = $this->users->registerEmptyFields($form);
		} catch (PasswordLengthException $e) {
			$errorMessage = 'Your password must be at least 6 characters in length.';
		} catch (UserAlreadyExistsException $e) {
			$errorMessage = <<<HTML
A user with the email, <strong>{$form['email']}</strong>, already exists. <a href="#">Forgot your password?</a>
HTML;
		}
		return false;
	}
}
?>