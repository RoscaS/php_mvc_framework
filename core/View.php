<?php

/**
 * Class View
 *
 * Classe de base pour la gestion de la présentation.
 * * Possibilité de changer le layout par défaut avec
 * la méthode `setLayout` en lui passant le nom d'un
 * layout situé dans **app/views/layout**
 * * Le template de la page doit être spécifie dans les
 * methodes `xxxAction` du controlleur via la methode
 * `render` qui prend en argument le nom d'un template
 * situé dans **app/views/templates**
 */
class View {

	protected $_head;
	protected $_body;
	protected $_layout = DEFAULT_LAYOUT;
	protected $_siteTitle = SITE_TITLE;

	public function __construct() { }

	/**
	 * Charge le template `$template` et set le titre de
	 * la page sur le nom du fichier (traductions dans
	 * **config/translations.php**)
	 * @param $template
	 */

	/*------------------------------------------------------------------*\
	|*					                Public Methods                          *|
	\*------------------------------------------------------------------*/

	public function render($template) {
		$template = TEMPLATES . $template . '.php';

		// Titre automatique de la page courante
		$this->setSiteTitle(Helpers::currentPage());

		if (file_exists($template)) {
			include $template;
			include LAYOUTS . $this->_layout . '.php';
		}
		else {
			//TODO: logging
			die("The view \"$template\" does not exist.");
		}
	}

	/**
	 * Délimite une section dans un **layout**.
	 * @param $sectionName
	 *
	 * @return bool
	 */
	public function section($sectionName) {
		if ($sectionName == 'head') {
			return $this->_head;
		}
		elseif ($sectionName == 'body') {
			return $this->_body;
		}
		return false;
	}

	/**
	 * "Balise" de début de section dans un **template**.
	 * @param $sectionName
	 */
	public function startSection($sectionName) {
		$this->_htmlSection = $sectionName;
		ob_start();
	}

	/**
	 * "Balise" de fin de section dans un **template**.
	 * * Ferme la dernière section ouverte
	 */
	public function endSection() {
		if ($this->_htmlSection == 'head') {
			$this->_head = ob_get_clean();
		}
		elseif ($this->_htmlSection == 'body') {
			$this->_body = ob_get_clean();
		}
		else {
			die('Start method must be used first !');
		}
	}

	/*------------------------------*\
	|*				    Getters        		*|
	\*------------------------------*/

	/**
	 * Retourne le titre du site.
	 * @return string
	 */
	public function siteTitle() {
		return $this->_siteTitle;
	}

	/*------------------------------*\
	|*				    Setters        		*|
	\*------------------------------*/



	/**
	 * Applique **$title** comme contenu de la
	 * balise `title` dans le head du document.
	 * @param $title
	 */
	public function setSiteTitle($title) {
		$this->_siteTitle = $title;
	}

	/**
	 * Change le layout utilisé.
	 * * Default: **views/layouts/default.php**
	 * @param $layout
	 */
	public function setLayout($layout) {
		$this->_layout = $layout;
	}
}
