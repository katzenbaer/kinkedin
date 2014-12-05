<?php
session_start();

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/User.php';

class Session {
	private $mysqli;
	
	function Session() {
		$this->mysqli = MySQL::getConnection();
	}
	
	function getEmail() {
		return $_SESSION['email'];
	}
	
	function getUser() {
		$users = new Users($this->mysqli);
		return $users->findByEmail($this->getEmail());
	}
	
	function isLoggedIn() {
		return isset($_SESSION['email']);
	}
	
	function establish($email) {		
		$_SESSION['email'] = strtolower($email);
	}
	
	function destroy() {
		unset($_SESSION['email']);
	}
}
?>