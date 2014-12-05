<?php
class WebRequest {
	public static function isAjaxRequest() {
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		}
		return false;
	}
	
	public static function isWebRequest() {
		return !self::isAjaxRequest();
	}
}

class Alerter {
	public static function setSuccessMessage($msg) {
		$_SESSION['success_message'] = $msg;
	}
	
	public static function setErrorMessage($msg) {
		$_SESSION['error_message'] = $msg;
	}
	
	public static function setInfoMessage($msg) {
		$_SESSION['info_message'] = $msg;
	}
}
?>