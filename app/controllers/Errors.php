<?php


class Errors extends Controller {

	public function __construct($controller, $action) {
		parent::__construct($controller, $action);
	}

	public function page_not_foundAction() {
		$this->view->render('errors/404');
	}
}
