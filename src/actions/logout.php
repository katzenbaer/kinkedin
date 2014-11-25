<?php
	require_once __DIR__ . '/../sources/Global.php';
	require_once __DIR__ . '/../classes/Session.php';
	
	class LogoutAction {
		function logout() {
			$session = new Session();
			$session->destroy();
	
			return true;
		}
	}
	
	if (WebRequest::isAjaxRequest()) {
		$action = new LogoutAction();
		
		echo json_encode(array(
			'success' => $action->logout()
		));
	}
?>
