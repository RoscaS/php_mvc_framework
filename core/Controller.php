<?php


/**
 * Class Controller
 * Classe de base dont héritent les Controlleurs.
 *
 */
class Controller extends Application {

	protected $_controller;
	protected $_action;
	public $model;

	public $view;

	public function __construct($controller, $action) {
		parent::__construct();
		$this->_controller = $controller;
		$this->_action     = $action;
		$this->view        = new View();
	}

	/**
	 * Permet de bind un modèle à un nouveau controlleur.
	 * * Usage: `load_model('Modele')`
	 * @param $model
	 */
	protected function load_model($model) {
		if (class_exists($model)) {
			$this->model = new $model(strtolower($model));
		}
	}
}
