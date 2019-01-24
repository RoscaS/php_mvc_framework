<?php

class Helpers {

	static function camel($string) {
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
	}

	static function sanitize($string) {
		return htmlentities($string, ENT_QUOTES, 'UTF-8');
	}

	static function setAttrs($object, $attrArray) {
		foreach ($attrArray as $attrName => $value) {
			$object->$attrName = $value;
		}
	}

	static function getInput($input) {
		return self::sanitize(
				isset($_POST[$input]) ? $_POST[$input] : $_GET[$input]
		);
	}

	static function currentPage() {
		$translationArray = include('config/translations.php');
		$path = explode('/', $_SERVER['PHP_SELF']);
		$key = $path[sizeof($path)-1];

		if (array_key_exists($key, $translationArray)) {
			return $translationArray[$key];
		}
		else {
			// fallback si l'entrée n'existe pas dans le dictionnaire
			return $key;
		}

	}
}
