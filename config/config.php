<?php


// Variables d'environement
try {
	require 'env.php';
} catch (Exception $e) {
	echo $e->getMessage(), "\n";
}

define('DEBUG', true);

// Controleur par défaut si il n'est pas spécifié dans l'url
define('DEFAULT_CONTROLLER', 'Home');

// Si pas de layout spécifié dans le controlleur
define('DEFAULT_LAYOUT', 'default');

// Titre par défaut
define('SITE_TITLE', 'My MVC website');

// Root path du serveur
define('ROOT', '/');

// Secret
define('USER_SESSION_NAME', 'SessionName');
define('REMEMBER_ME_COOKIE_NAME', implode(explode(' ', SITE_TITLE)));

// TTL en secondes d'un cookie
define('REMEMBER_ME_COOKIE_EXPIRE', 3600);

// Base de donnée
define('DATABASE', [
		'connection' => 'mysql',
		'host'       => 'localhost',
		'dbname'     => 'mvc_db',
		'user'       => 'root',
		'password'   => $env_data['password'],
		'port'       => '3306',
]);

