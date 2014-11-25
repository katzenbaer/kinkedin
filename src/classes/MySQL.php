<?php

class MySQLConnectionException extends Exception {}

class MySQL {
	static $db_host = 'localhost';
	static $db_user = 'root';
	static $db_pass = '';
	static $db_name = 'kinkedin';
	
	static function getConnection() {
		$mysqli = new mysqli(self::$db_host, self::$db_user, self::$db_pass, self::$db_name);
		if (mysqli_connect_errno()) {
			throw new MySQLConnectionException("Connection Failed: " . mysqli_connect_errno());
		}
		return $mysqli;
	}
}
?>