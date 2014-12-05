<?php
require_once __DIR__ . '/../simpletest/autorun.php';
require_once __DIR__ . '/../../classes/Authentication.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/User.php';

Mock::generate('Session');
Mock::generate('Users');

class AuthenticationClassTest extends UnitTestCase {
	var $mockSession, $mockUsers;
	var $authentication;
	
	function setUp() {
		$this->mockSession = new MockSession();
		$this->mockUsers = new MockUsers();
		$this->authentication = new Authentication($this->mockUsers, $this->mockSession);
	}
	function tearDown() { }
	
  function testLogin_Success() {
		// Arrange
		$email = 'foo@foo.com';
		$pass = 'bar';
		$this->mockUsers->returns('authenticate', true, array($email, $pass));
		
		// Expect
		$this->mockUsers->expectOnce('authenticate', array($email, $pass));
		$this->mockSession->expectOnce('establish', array($email));
		
		// Act
		$result = $this->authentication->login($email, $pass, $message);
		
		// Assert
		$this->assertTrue($result);
		$this->assertNull($message);
  }
	
  function testLogin_InvalidCredentials() {
		// Arrange
		$email = 'foo@foo.com';
		$pass = 'bar';
		$this->mockUsers->returns('authenticate', false, array($email, $pass));
		
		// Expect
		$this->mockUsers->expectOnce('authenticate', array($email, $pass));
		$this->mockSession->expectNever('establish', array($email));
		
		// Act
		$result = $this->authentication->login($email, $pass, $message);
		
		// Assert
		$this->assertFalse($result);
		$this->assertEqual($message, 'The credentials you submitted are invalid.');
  }
	
  function testLogin_ProblemOccured() {
		// Arrange
		$email = 'foo@foo.com';
		$pass = 'bar';
		$this->mockUsers->throwOn('authenticate', new Exception());
		
		// Expect
		$this->mockUsers->expectOnce('authenticate', array($email, $pass));
		$this->mockSession->expectNever('establish', array($email));
		
		// Act
		$result = $this->authentication->login($email, $pass, $message);
		
		// Assert
		$this->assertFalse($result);
		$this->assertEqual($message, 'A problem occured.');
  }
	
	// @TODO: Write tests for sign-up
	
}
?>