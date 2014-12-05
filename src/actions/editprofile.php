<?php
require_once __DIR__ . '/../sources/Global.php';
require_once __DIR__ . '/../classes/MySQL.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/User.php';

class EditProfileAction {
	private $mysql, $users, $session;
	function EditProfileAction() {
		$this->mysql = MySQL::getConnection();
		$this->users = new Users($this->mysql);
		$this->session = new Session();
	}
	
	function commit() {
		// 'title', 'aliases', 'website', 'twitter', 'location', 'dob', 'debut', 
		// 'measurements', 'height', 'eyescolor', 'haircolor', 'race', 'ethnicity'		
		$profile = $this->session->getUser()->getProfile();
		$profile->applyDictionary($_POST);
		$success = $profile->commit();
		
		return $success;
	}
}

if (WebRequest::isAjaxRequest()) {
	$action = new EditProfileAction();
	
	$success = $action->commit();
	echo json_encode(array(
		'success' => $success
	));
}
?>
