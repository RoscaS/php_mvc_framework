<?php

class Home extends Controller {

	public function __construct($controller, $action) {
		parent::__construct($controller, $action);

	}

	public function indexAction() {
		if (!User::currentUser()) {
			Router::redirect('register/login');
		}
		else {
			Router::redirect('tasks');
		}

		//$this->view->render('home/index');
	}

}

