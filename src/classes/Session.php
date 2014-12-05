<?php
session_start();

class Session {
	function Session() {
		
	}
	
	function getEmail() {
		return $_SESSION['email'];
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