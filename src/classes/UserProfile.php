<?php
class UserProfile {
	private $mysqli;
	private $email;
	private $data;
	
	function UserProfile($mysqli, $email) {
		$this->mysqli = $mysqli;
		$this->email = $email;
		
		$this->fetchData();
	}
	
	/**
	 * Returns the User's location.
	 * @return String
	 */
	function getLocation() {
		return $this->data['location'];
	}
	
	/**
	 * Returns the User's title.
	 * @return String
	 */
	function getTitle() {
		return $this->data['title'];
	}
	
	/**
	 * Returns the User's aliases.
	 * @return String
	 */
	function getAliases() {
		return $this->data['aliases'];
	}
	
	/**
	 * Returns the User's website
	 * @return String
	 */
	function getWebsite() {
		return $this->data['website'];
	}
	
	/**
	 * Returns the User's twitter handle
	 * @return String
	 */
	function getTwitter() {
		return $this->data['twitter'];
	}
	
	/**
	 * Returns the User's date of birth
	 * @return String
	 */
	function getDateOfBirth() {
		return $this->data['dob'];
	}
	
	/**
	 * Returns the User's debut
	 * @return String
	 */
	function getDebut() {
		return $this->data['debut'];
	}
	
	/**
	 * Returns the User's measurements
	 * @return String
	 */
	function getMeasurements() {
		return $this->data['measurements'];
	}
	
	/**
	 * Returns the User's height
	 * @return String
	 */
	function getHeight() {
		return $this->data['height'];
	}
	
	/**
	 * Returns the User's eyes color
	 * @return String
	 */
	function getEyesColor() {
		return $this->data['eyecolor'];
	}
	
	/**
	 * Returns the User's hair color
	 * @return String
	 */
	function getHairColor() {
		return $this->data['haircolor'];
	}
	
	/**
	 * Returns the User's race
	 * @return String
	 */
	function getRace() {
		return $this->data['race'];
	}
	
	/**
	 * Returns the User's ethnicity
	 * @return String
	 */
	function getEthnicity() {
		return $this->data['ethnicity'];
	}
	
	function fetchData() {
		$this->data = array(); // Clear data
		
		$stmt = <<<SQL
SELECT `attribute`, `value` FROM `profile`
INNER JOIN `users` ON `users`.`id` = `profile`.`user_id`
WHERE `users`.`email` = ?
ORDER BY `timestamp` DESC
SQL;
		if ($stmt = $this->mysqli->prepare($stmt)) {
			$stmt->bind_param('s', $this->email);
			
			if ($stmt->execute() === FALSE) {
				die('unable to execute' . htmlspecialchars($this->mysqli->error));
			}
			
			$stmt->bind_result($attribute, $value);
			while ($result = $stmt->fetch()) {
				$this->data[$attribute] = $value;
			}
			$stmt->close();
		} else {
			die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
		}
	}
}
?>