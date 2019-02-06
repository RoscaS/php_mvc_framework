<?php

class Router {

	public static function route($uri) {
		// Controller

		$controller = (isset($uri[0]) && $uri[0] != '') ? ucwords($uri[0]) : DEFAULT_CONTROLLER;
		array_shift($uri);

		// Action
		$action = (isset($uri[0]) && $uri[0] != '') ? "$uri[0]Action" : "indexAction";
		array_shift($uri);

		// Args
		$args = $uri;

		self::validUrl($controller, $action);
		$dispatch = new $controller($controller, $action);
		call_user_func_array([$dispatch, $action], $args);
		die();
	}

	public static function redirect($location) {
			header('Location: ' . ROOT . $location);
			exit();
	}

	private static function validUrl($controller, $action) {
		if (!class_exists($controller)) {
			self::redirect('errors/page_not_found');
		}


		if (!method_exists($controller, $action)) {
			self::redirect('errors/page_not_found');
		}
	}
}
