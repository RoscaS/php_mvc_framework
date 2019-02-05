<?php

class Router {

	public static function route($url) {
		// Controller

		$controller = (isset($url[0]) && $url[0] != '') ? ucwords($url[0]) : DEFAULT_CONTROLLER;
		array_shift($url);

		// Action
		$action = (isset($url[0]) && $url[0] != '') ? "$url[0]Action" : "indexAction";
		array_shift($url);

		// Args
		$args = $url;

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
