<?php


class Cookie {

	public static function exists($name) {
		return isset($_COOKIE[$name]);
	}

	public static function get($name) {
		return $_COOKIE[$name];
	}

	public static function set($name, $value, $expire) {
		return setcookie($name, $value, time() + $expire, '/');
	}

	public static function delete($name) {
		if (Cookie::exists($name)) {
			self::set($name, '', -1);
		}
	}

}
