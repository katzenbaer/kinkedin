<?php
$dependencies = array(
	'/vendor/autoload.php',
	'/sources/Global.php',
	'/classes/Session.php',
	'/classes/User.php',
	'/classes/MySQL.php'
);
foreach ($dependencies as $dependency) {
	require_once __DIR__ . $dependency;
}

function profilePage() {
	$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
	$twig = new Twig_Environment($loader, array(
	  // options here
	));

	$userId = $_GET['u'];
	$user = (new Users(MySQL::getConnection()))->findById($userId);
	$session = new Session();

	if (Session::isLoggedIn()) {
		echo $twig->render('profile.html', array(
		    'user' => $user,
				'session' => $session,
		));	
	} else {
		echo $twig->render('landing.html');
	}
}

profilePage();

?>
