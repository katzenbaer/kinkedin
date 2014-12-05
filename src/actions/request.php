<?php
require_once __DIR__ . '/../sources/Global.php';
require_once __DIR__ . '/../classes/MySQL.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/User.php';

class RequestAction {
	private $mysql, $users, $session;
	function RequestAction() {
		$this->mysql = MySQL::getConnection();
		$this->users = new Users($this->mysql);
		$this->session = new Session();
	}
	
	function request() {
		$toUserId = trim($_POST['user']);
		
		$success = false;
		try {
			$success = $this->session->getUser()->request($toUserId);
		} catch (UserDoesNotExistException $e) {
			Alerter::setErrorMessage("The user you are trying to connect with does not exist.");
		}
		
		return $success;
	}
}

if (WebRequest::isAjaxRequest()) {
	$action = new RequestAction();
	
	$success = $action->request();
	echo json_encode(array(
		'success' => $success
	));
}
?>
