<?php

// Constantes globales
define('DS', DIRECTORY_SEPARATOR); // backslash sur posix et slash sur windows
define('BASE_DIR', dirname(__FILE__) . DS);

define('CONFIG', BASE_DIR . 'config' . DS);
define('CORE', BASE_DIR . 'core' . DS);
define('APP', BASE_DIR . 'app' . DS);
define('HELPERS', BASE_DIR . 'helpers' . DS);
define('LOGS', BASE_DIR . 'logs' . DS);

define('MODELS', APP . 'models' . DS);
define('CONTROLLERS', APP . 'controllers' . DS);
define('VIEWS', APP . 'views' . DS);
define('LAYOUTS', VIEWS . 'layouts' . DS);
define('TEMPLATES', VIEWS . 'templates' . DS);

// Include des config
require_once CONFIG . 'config.php';

// Autoload des classes
spl_autoload_register(function ($className) {
	$paths = [
			CONTROLLERS . $className . '.php',
			HELPERS . $className . '.php',
			MODELS . $className . '.php',
			CORE . $className . '.php',
	];

	foreach ($paths as $path) {
		if (file_exists($path)) {
			require_once $path;
		}
	}
});

// Start de la session
session_start();

// Tente de reconnecter un éventuel utilisateur connu
if (!Session::exists(USER_SESSION_NAME) && Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
	User::loginUserFromCookie();
}

// Split l'url en liste de strings
$uri = isset($_SERVER['PATH_INFO'])
		? explode('/', ltrim($_SERVER['PATH_INFO'], '/'))
		: [];


// Routage de l'uri
Router::route($uri);


