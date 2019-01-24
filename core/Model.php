<?php

/**
 * Class Model
 * Classe de base dont héritent les modèles. Met à disposition un "ORM"
 * Simplifié.
 * # Exemples
 * * ``
 *
 */
class Model {

	protected $_db;
	protected $_table;
	protected $_modelName;
	protected $_fields = [];

	public $pk;

	/**
	 * Prend en argument le nom d'une table existant dans la base de données.
	 *
	 * @param $tableName
	 */
	public function __construct($tableName) {
		$this->_db = DB::getInstance();
		$this->_table = $tableName;
		$this->_modelName = Helpers::camel($tableName);
		$this->_initFields();
	}

	/*------------------------------------------------------------------*\
	|*					                Private Methods                         *|
	\*------------------------------------------------------------------*/

	/**
	 * Methode interne qui set les attributs de l'instance en fonction
	 * des attributs de la table.
	 */
	private function _initFields() {
		foreach ($this->columns() as $column) {
			$this->{$column->Field} = NULL; // set les attributs dynamiquement
			$this->_fields[] = $column->Field;
		}
	}

	/*------------------------------------------------------------------*\
	|*					                Public Methods                          *|
	\*------------------------------------------------------------------*/

	/*------------------------------*\
	|*				    CRUD          		*|
	\*------------------------------*/

	/**
	 * Wrapper de la methode `query` de `DB`
	 *
	 * @param $sql
	 * @param $bind
	 *
	 * @return \DB
	 */
	public function query($sql, $bind) {
		return $this->_db->query($sql, $bind);
	}

	/**
	 * Permet d'insérer une nouvelle entrée dans la table de ce modèle.
	 * * Usage: `insert(['champ1' => 'val1', 'champ2' => 'val2', ...])`
	 *
	 * @param $fields
	 *
	 * @return bool
	 */
	public function insert($fields) {
		return empty($fields) ? false : $this->_db->insert($this->_table, $fields);
	}

	/**
	 * Permet de maj les champs d'une entrée de ce modèle.
	 * * Usage: `insert('pk', ['champ1' => 'val1', 'champ2' => 'val2', ...])`
	 *
	 * @param $pk
	 * @param $fields
	 *
	 * @return bool
	 */
	public function update($pk, $fields) {
		return empty($fields) || !is_numeric($pk) ? false
				: $this->_db->update($this->_table, $pk, $fields);
	}

	/**
	 * Permet de supprimer une entrée de ce modèle.
	 * * Usage: `delete('pk')`
	 *
	 * @param string $pk
	 *
	 * @return bool
	 */
	public function delete($pk = '') {
		return $pk == '' ? false : $this->_db->delete($this->_table, $pk);
	}

	/*------------------------------*\
	|*				    Tools         		*|
	\*------------------------------*/


	/**
	 * Methode plus haut niveau que son homologue de la classe `DB` qui permet de
	 * de retourner des entrées particulières de ce modèle.
	 *
	 * * Usage: `find([['champ1' => 'valeur1', ...]], ['ordre'], ['max'])`
	 * * Exemple1: `find()` retourne toutes les entrées de ce modèle
	 * * Exemple2: `find(['owner' => 'claude', 'deadline' => '2019-12-04']
	 * , 'id', '5')`
	 *      retourne les 5 premières **tasks** de **claude** ayant comme deadline le
	 *      04/12/2019 ordonnées par leur id.
	 *
	 * @param array  $args
	 * @param string $order
	 * @param string $max
	 *
	 * @return array
	 */
	public function find($args = [], $order = "", $max = "") {
		$results = [];
		$queryResults = $this->_db->find($this->_table, $args, $order, $max);

		if ($queryResults) {
			foreach ($queryResults as $element) {
				$object = new $this->_modelName($this->_table);
				Helpers::setAttrs($object, $element);
				$results[] = $object;
			}
		}
		return $results;
	}

	/**
	 * Methode plus haut niveau que son homologue de la classe `DB` qui permet de
	 * de retourner la première entrée d'une recherche. Voir `find` pour des
	 * exemples d'utilisation des requetes.
	 *
	 * * Usage: `first([[args]])
	 *
	 * @param array  $args
	 *
	 * @return object
	 */
	public function first($args = []) {
		$object = NULL;
		$queryResult = $this->_db->first($this->_table, $args);

		if ($queryResult) {
			$object = new $this->_modelName($this->_table);
			Helpers::setAttrs($object, $queryResult);
		}
		return $object;
	}

	/**
	 * Equivalent de `find()` ce qui retourne toutes les entrées de ce modèle.
	 * @return array
	 */
	public function all() {
		return $this->find();
	}


	// LES 3 METHODES QUI SUIVENT SONT EXPERIMENTALES !

	/**
	 * WIP: En anticipation, pas encore réélement testée
	 *
	 * @return bool
	 */
	public function save() {
		$fields = [];
		foreach ($this->_fields as $column) {
			$fields[$column] = $this->$column;
		}
		$update = property_exists($this, 'id') && $this->id != '';
		return $update ? $this->update($this->id, $fields) : $this->insert($fields);
	}

	/**
	 * WIP: En anticipation, pas encore réélement testée
	 *
	 * @return object
	 */
	public function data() {
		return (object) array_map(                    // (object) \equiv stdObject()
				function($a) { return $this->$a; },
				$this->_fields
		);
	}

	/**
	 * WIP: En anticipation, pas encore réélement testée
	 *
	 * @param $args
	 */
	public function assign($args) {
		if (!empty($args)) {
			foreach ($args as $k => $v) {
				if (in_array($k, $this->_fields)) {
					$this->$k = Helpers::sanitize($v);
				}
			}
		}
	}

	/*------------------------------*\
	|*				    Getters        		*|
	\*------------------------------*/

	/**
	 * @return array
	 */
	public function columns() {
		return $this->_db->columns($this->_table);
	}

	/**
	 * @return array
	 */
	public function fields() {
		return $this->_fields;
	}

	/**
	 * @return mixed
	 */
	public function modelName() {
		return $this->_modelName;
	}
}
