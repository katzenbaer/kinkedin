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
?>