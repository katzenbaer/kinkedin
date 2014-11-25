<?php
require_once __DIR__ . '/../simpletest/autorun.php';
require_once __DIR__ . '/../../classes/Authentication.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/User.php';

Mock::generate('Session');
Mock::generate('User');

class AuthenticationClassTest extends UnitTestCase {
	var $mockSession, $mockUser;
	var $authentication;
	
	function setUp() {
		$this->mockSession = new MockSession();
		$this->mockUser = new MockUser();
		$this->authentication = new Authentication($this->mockUser, $this->mockSession);
	}
	function tearDown() { }
	
  function testLogin_Success() {
		// Arrange
		$email = 'foo@foo.com';
		$pass = 'bar';
		$this->mockUser->returns('authenticate', true, array($email, $pass));
		
		// Expect
		$this->mockUser->expectOnce('authenticate', array($email, $pass));
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
		$this->mockUser->returns('authenticate', false, array($email, $pass));
		
		// Expect
		$this->mockUser->expectOnce('authenticate', array($email, $pass));
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
		$this->mockUser->throwOn('authenticate', new Exception());
		
		// Expect
		$this->mockUser->expectOnce('authenticate', array($email, $pass));
		$this->mockSession->expectNever('establish', array($email));
		
		// Act
		$result = $this->authentication->login($email, $pass, $message);
		
		// Assert
		$this->assertFalse($result);
		$this->assertEqual($message, 'A problem occured.');
  }
	
}
?>