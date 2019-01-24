<?php

class Register extends Controller {

	public function __construct($controller, $action) {
		parent::__construct($controller, $action);
		$this->load_model('User');
	}

	public function indexAction() {
		$this->loginAction();
	}

	public function loginAction() {
		$validation = new Validate();

		if (User::currentUser()) {
			Router::redirect('tasks');
		}

		if ($_POST) {

			$validation->check($_POST, [
					'username' => [
							'display'  => "Utilisateur",
							'required' => true,
							'min'      => '3',
					],
					'password' => [
							'display'  => 'Mot de passe',
							'required' => true,
					],
			]);

			if ($validation->passed()) {
				$user = $this->model->findByUsername($_POST['username']);
				$pw = Helpers::getInput('password');

				if ($user && password_verify($pw, $user->password)) {
					$user->login();
					Router::redirect('');
				}
				else {
					$validation->_addError("Nom d'utilisateur ou mot de passe incorrecte !");
				}
			}
		}

		$this->view->errorList = $validation->errorList();
		$this->view->render('register/login');
	}

	public function logoutAction() {
		if (User::currentUser()) {
			User::currentUser()->logout();
		}
		Router::redirect('register/login');
	}
}
