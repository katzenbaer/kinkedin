<?php
class UserProfile {
	private $mysqli;
	private $email, $id;
	private $data;
	
	function UserProfile($mysqli, $email, $id) {
		$this->mysqli = $mysqli;
		$this->email = $email;
		$this->id = $id;
		
		$this->fetchData();
	}
	
	/**
	 * Returns the User's picture.
	 * @return String
	 */
	function getPicture() {
		return $this->data['picture'];
	}
	
	function setPicture($data) {
		$this->data['picture'] = $data;
	}
	
	/**
	 * Returns the User's location.
	 * @return String
	 */
	function getLocation() {
		return $this->data['location'];
	}
	
	function setLocation($data) {
		$this->data['location'] = $data;
	}
	
	/**
	 * Returns the User's title.
	 * @return String
	 */
	function getTitle() {
		return $this->data['title'];
	}
	
	function setTitle($data) {
		$this->data['title'] = $data;
	}
	
	/**
	 * Returns the User's aliases.
	 * @return String
	 */
	function getAliases() {
		return $this->data['aliases'];
	}
	
	function setAliases($data) {
		$this->data['aliases'] = $data;
	}
	
	/**
	 * Returns the User's website
	 * @return String
	 */
	function getWebsite() {
		return $this->data['website'];
	}
	
	function setWebsite($data) {
		$this->data['website'] = $data;
	}
	
	/**
	 * Returns the User's twitter handle
	 * @return String
	 */
	function getTwitter() {
		return $this->data['twitter'];
	}
	
	function setTwitter($data) {
		$this->data['twitter'] = $data;
	}
	
	/**
	 * Returns the User's date of birth
	 * @return String
	 */
	function getDateOfBirth() {
		return $this->data['dob'];
	}
	
	function setDateOfBirth($data) {
		$this->data['dob'] = $data;
	}
	
	/**
	 * Returns the User's debut
	 * @return String
	 */
	function getDebut() {
		return $this->data['debut'];
	}
	
	function setDebut($data) {
		$this->data['debut'] = $data;
	}
	
	/**
	 * Returns the User's measurements
	 * @return String
	 */
	function getMeasurements() {
		return $this->data['measurements'];
	}
	
	function setMeasurements($data) {
		$this->data['measurements'] = $data;
	}
	
	/**
	 * Returns the User's height
	 * @return String
	 */
	function getHeight() {
		return $this->data['height'];
	}
	
	function setHeight($data) {
		$this->data['height'] = $data;
	}
	
	/**
	 * Returns the User's eyes color
	 * @return String
	 */
	function getEyesColor() {
		return $this->data['eyecolor'];
	}
	
	function setEyesColor($data) {
		$this->data['eyecolor'] = $data;
	}
	
	/**
	 * Returns the User's hair color
	 * @return String
	 */
	function getHairColor() {
		return $this->data['haircolor'];
	}
	
	function setHairColor($data) {
		$this->data['haircolor'] = $data;
	}
	
	/**
	 * Returns the User's race
	 * @return String
	 */
	function getRace() {
		return $this->data['race'];
	}
	
	function setRace($data) {
		$this->data['race'] = $data;
	}
	
	/**
	 * Returns the User's ethnicity
	 * @return String
	 */
	function getEthnicity() {
		return $this->data['ethnicity'];
	}
	
	function setEthnicity($data) {
		$this->data['ethnicity'] = $data;
	}
	
	/**
	 * Retrieves profile from server.
	 */
	private function fetchData() {
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
	
	function applyDictionary($dict) {
		foreach ($dict as $key => $value) {
			if (isset($this->data[$key])) {
				$this->data[$key] = trim($value);
			}
		}
	}
	
	/**
	 * Update the database with the user's profile.
	 */
	function commit() {
		$attributes = array('location', 'title', 'aliases', 'website', 'twitter', 'dob', 'debut',
			'measurements', 'height', 'eyecolor', 'haircolor', 'race', 'ethnicity');
		foreach ($attributes as $attribute) {
			$stmt = <<<SQL
UPDATE `profile`
SET `value` = ?
WHERE `attribute` = ? AND `user_id` = ?
SQL;
			if ($stmt = $this->mysqli->prepare($stmt)) {
				$stmt->bind_param('ssi', $this->data[$attribute], $attribute, $this->id);
			
				if ($stmt->execute() === FALSE) {
					die('unable to execute' . htmlspecialchars($this->mysqli->error));
				}
			} else {
				die('unable to prepare the statement. ' . htmlspecialchars($this->mysqli->error));
			}	
		}
		return true;
	}
}
?>