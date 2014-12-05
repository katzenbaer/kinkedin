<?php
$dependencies = array(
	'/vendor/autoload.php',
	'/sources/Global.php',
	'/classes/Session.php',
	'/classes/User.php'
);
foreach ($dependencies as $dependency) {
	require_once __DIR__ . $dependency;
}

function indexPage() {
	$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
	$twig = new Twig_Environment($loader, array(
	  // options here
	));

	/*if (isset($_POST['login_email']) && isset($_POST['login_password'])) {
		$user = User::authenticate($_POST['login_email'], $_POST['login_password']);
	}*/
		
	$session = new Session();
	if ($session->isLoggedIn()) {
		echo $twig->render('index.html', array(
				'session' => $session
		));	
	} else {
		echo $twig->render('landing.html');
	}
}

indexPage();

?>
