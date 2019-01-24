<?php

class User extends Model {

	private $_sessionName = USER_SESSION_NAME;
	private $_cookieName = REMEMBER_ME_COOKIE_NAME;

	public static $loggedInUser = NULL;

	public function __construct($user = '') {
		parent::__construct('user');

		if ($user != '') {
			$field = is_int($user) ? 'id' : 'username';
			$u = $this->first([$field => $user]);
			if ($u) Helpers::setAttrs($this, $u);
		}
	}

	public function findByUsername($username) {
		return $this->first(['username' => $username]);
	}

	public function login() {
		$hash = md5(uniqid());

		$this->query("DELETE FROM user_session WHERE user_id = ?", [$this->id]);
		$this->_db->insert(
				'user_session', ['session' => $hash, 'user_id' => $this->id]
		);
		Cookie::set($this->_cookieName, $hash, REMEMBER_ME_COOKIE_EXPIRE);
		Session::set($this->_sessionName, $this->id);

		self::$loggedInUser = self::currentUser();
	}

	public function logout() {
		$sql = "DELETE FROM user_session WHERE user_id = ?";
		$this->query($sql, [$this->id]);

		Session::delete(USER_SESSION_NAME);
		Cookie::delete(REMEMBER_ME_COOKIE_NAME);
		self::$loggedInUser = NULL;
	}


	public static function loginUserFromCookie() {
		$user_session = DB::getInstance()->first(
				'user_session',
				['session' => Cookie::get(REMEMBER_ME_COOKIE_NAME)]
		);

		if ($user_session->user_id != '') {
			$user = new self((int) $user_session->user_id);
		}
		$user->login();
		return self::$loggedInUser;
	}


	public static function currentUser() {
		if (!isset(self::$loggedInUser) && Session::exists(USER_SESSION_NAME)) {
			$user = new User((int) Session::get(USER_SESSION_NAME));
			self::$loggedInUser = $user;
		}
		return self::$loggedInUser;
	}
}
