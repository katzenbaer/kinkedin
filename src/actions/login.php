<?php
require_once __DIR__ . '/../sources/Global.php';
require_once __DIR__ . '/../classes/Authentication.php';
require_once __DIR__ . '/../classes/MySQL.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/User.php';

class LoginAction {
	private $mysql, $users, $session, $authentication;
	function LoginAction() {
		$this->mysql = MySQL::getConnection();
		$this->users = new Users($this->mysql);
		$this->session = new Session();
		$this->authentication = new Authentication($this->users, $this->session);
	}
	
	function login(&$message) {
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

		$message = null;
		try {
			$success = $this->authentication->login($email, $password, $message);
		} catch (MySQLConnectionException $e) {
			$success = false;
			$message = 'An error occured while establishing the database connection.';
		}
		
		return $success;
	}
}

if (WebRequest::isAjaxRequest()) {
	$action = new LoginAction();
	
	$success = $action->login($message);
	echo json_encode(array(
		'success' => $success,
		'message' => $message
	));
}
?>
