<?php
require_once __DIR__ . '/../sources/Global.php';
require_once __DIR__ . '/../classes/Authentication.php';
require_once __DIR__ . '/../classes/MySQL.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/User.php';

class SignupAction {
	private $mysql, $users, $session, $authentication;
	function SignupAction() {
		$this->mysql = MySQL::getConnection();
		$this->users = new Users($this->mysql);
		$this->session = new Session();
		$this->authentication = new Authentication($this->users, $this->session);
	}
	
	function signup(&$errors, &$message) {
		$firstName = trim($_POST['firstName']);
		$lastName = trim($_POST['lastName']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

		$form = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'email' => $email,
			'password' => $password
		);

		$success = $this->authentication->signup($form, $errors, $message);
		return $success;
	}
}

if (WebRequest::isAjaxRequest()) {
	$action = new SignupAction();
	
	$success = $action->signup($errors, $message);
	echo json_encode(array(
		'success' => $success,
		'errors' => $errors,
		'message' => $message
	));
}
?>
