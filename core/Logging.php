<?php


class Logging {

	static private $_file = LOGS . 'server_logs.txt';
	static private $_buffer;

	static public function time() {
		$t = microtime(true);
		$micro = sprintf("%06d", ($t - floor($t)) * 1000000);
		$d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
		return '['.$d->format("Y-m-d H:i:s.u").'] ';
	}


	static public function write($text) {
		self::$_buffer = fopen(self::$_file, 'a');
		fwrite(self::$_buffer, self::time() . $text . "\n");
		fclose(self::$_buffer);
	}


	static public function logAction($table, $action, $id) {
		$user = User::currentUser();
		$s = $action . ' -> ' . $table;
		$s .= "\telement_ID: " . $id . "\tby_user_ID: " . $user->id;
		self::write($s);
	}

}
