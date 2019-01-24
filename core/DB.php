<?php


/**
 * Class DB (singleton)
 * Classe de controle de la base de données. Les methodes sont assez bas niveau
 * Et il n'est pas conseillé de les utiliser directement. Il vaut mieux
 * privilégier l'utilisation des méthodes mises à disposition par les objets
 * héritant de la classe `Modèle`.
 * * Les settings associés se trouvent dans le fichier **config/config.php**
 */
class DB {

	private $_pdo;
	private $_query;
	private $_result;
	private $_error = false;
	private $_lastInsertID = NULL;
	private static $_instance = NULL;

	private function __construct() {
		try {
			$this->_pdo = new PDO(
					DATABASE['connection'] .
					':host=' . DATABASE['host'] .
					';dbname=' . DATABASE['dbname'],
					DATABASE['user'],
					DATABASE['password']
			);
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}

	/*------------------------------------------------------------------*\
	|*					                Private Methods                         *|
	\*------------------------------------------------------------------*/

	/**
	 * Methode interne qui interface avec la methode `bindValue` de PDO.
	 *
	 * @param $args
	 */
	private function _bindArgs($args) {
		if (count($args)) {
			for ($i = 0; $i < count($args); $i++) {
				$this->_query->bindValue($i + 1, $args[$i]);
			}
		}
	}


	/**
	 * Methode interne qui finalise le query et set les attributs classe.
	 */
	private function _executeQuery() {
		if ($this->_query->execute()) {
			$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
			$this->_count = $this->_query->rowCount();
			$this->_lastInsertID = $this->_pdo->lastInsertId();
		}
		else {
			$this->_error = true;
		}
	}

	/**
	 * Methode interne qui adapte les arguments reçu dans $args pour les rendre
	 * utilisable par PDO.
	 *
	 * @param        $table
	 * @param array  $args
	 * @param string $order
	 * @param string $max
	 *
	 * @return array
	 */
	private function _parse($table, $args = [], $order = "", $max = "") {
		$cond = "";
		$bind = [];

		if ($args) {
			$cond = [];
			foreach ($args as $k => $v) {
				$cond[] = $k;
				$bind[] = $v;
			}
			$cond = implode(' = ? AND ', array_values($cond));
			$cond = "WHERE $cond = ?";
		}

		$order = $order ? "ORDER BY {$order}" : "";
		$max = $max ? "LIMIT {$max}" : "";

		$sql = "SELECT * FROM {$table} {$cond} {$order} {$max}";
		return ['sql' => trim($sql), 'bind' => $bind];
	}

	/**
	 * Methode interne qui est le point d'entré des requêtes en mode "ORM" faites
	 * avec les methodes `find` et `first`.
	 *
	 * @param        $table
	 * @param array  $args
	 * @param string $order
	 * @param string $max
	 *
	 * @return bool|int
	 */
	protected function _read($table, $args = [], $order = "", $max = "") {
		$parse = $this->_parse($table, $args, $order, $max);
		if ($this->query($parse['sql'], $parse['bind'])) {
			return $this->count();
		}
		return false;
	}

	/*------------------------------------------------------------------*\
	|*					                Public Methods                          *|
	\*------------------------------------------------------------------*/

	/*------------------------------*\
	|*				    CRUD          		*|
	\*------------------------------*/


	/**
	 * Permet de faire une requête SQL classique via PDO.
	 * Utilisation de l'extérieur via la methode statique `getInstance()`.
	 * L'objet retourné peut être utilisé avec les methodes `result()`,
	 * `lastInsertID()`, `isError()`, `count()` et `columns()`.
	 * * Usage: `query('sql', ['bind1', 'bind2',...])`
	 * * Exemple1: `query('SELECT * FROM ?', ['task'])`
	 * * Exemple2: `DB::getInstance()->query('SELECT * FROM ?', ['task'])`
	 * * Exemple3: `query('SELECT * FROM ?', ['task'])->result()`
	 * * Exemple4: `query('SELECT * FROM ?', ['task'])->count()`
	 *
	 * @param       $sql
	 * @param array $args
	 *
	 * @return \DB
	 */
	public function query($sql, $args = []) {
		$this->_error = false;
		$this->_query = $this->_pdo->prepare($sql);

		if ($this->_query) {
			$this->_bindArgs($args);
			$this->_executeQuery();
		}
		return $this;
	}

	/**
	 * Permet d'insérer une nouvelle entrée dans la base de donnée. Utilisation
	 * de l'extérieur via la methode statique `getInstance()`
	 * * Usage: `insert('table', ['champ1' => 'val1', 'champ2' => 'val2'])`
	 *
	 * @param       $table
	 * @param array $fields
	 *
	 * @return bool
	 */
	public function insert($table, $fields = []) {
		$keys = implode('`,`', array_keys($fields));
		$values = rtrim(str_repeat("?,", count($fields)), ',');
		$sql = "INSERT INTO {$table} (`{$keys}`) VALUES ({$values})";
		$action = !$this->query($sql, array_values($fields))->_error;
		Logging::logAction($table, 'insert', $this->_lastInsertID);
		return $action;
	}

	/**
	 * Permet de maj une entrée de la base de donnée. Utilisation
	 * de l'extérieur via la methode statique `getInstance()`
	 * * Usage: `update('table', pk, ['champ1' => 'val1', 'champ2' => 'val2'])`
	 *
	 * @param       $table
	 * @param       $pk
	 * @param array $fields
	 *
	 * @return bool
	 */
	public function update($table, $pk, $fields = []) {
		if (is_numeric($pk)) {
			$keys = implode(' = ?, ', array_keys($fields)) . ' = ?';
			$sql = "UPDATE {$table} SET {$keys} WHERE id = {$pk}";
			$action = !$this->query($sql, array_values($fields))->_error;
			Logging::logAction($table, 'update', $pk);
			return $action;
		}
		return false;
	}

	/**
	 * Permet de supprimer une entrée de la base de donnée. Utilisation
	 * de l'extérieur via la methode statique `getInstance()`
	 * * Usage: `delete('table', pk)`
	 *
	 * @param $table
	 * @param $pk
	 *
	 * @return bool
	 */
	public function delete($table, $pk) {
		if (is_numeric($pk)) {
			$sql = "DELETE FROM {$table} WHERE id = {$pk}";
			$action = !$this->query($sql)->_error;
			Logging::logAction($table, 'delete', $pk);
			return $action;
		}
		return false;
	}

	/*------------------------------*\
	|*				    Tools         		*|
	\*------------------------------*/

	/**
	 * Retourne le nombre de résultats contenu dans un objet de type `DB`
	 *
	 * @return int
	 */
	public function count() {
		return $this->_result ? count($this->_result) : 0;
	}

	/**
	 * Retourne le nom des colonnes d'un objet du type `DB`
	 *
	 * @return array
	 */
	public function columns($table) {
		return $this->query("SHOW COLUMNS FROM {$table}")->_result;
	}

	/**
	 * Methode qui permet de de retourner des informations de
	 * la base de donnée.
	 * Utilisation de l'extérieur via la methode statique `getInstance()`.
	 *
	 * * Usage: `find('table', [['champ1' => 'valeur1', ...]], ['ordre'], ['max'])`
	 * * Exemple1: `find('Task')` retourne toutes les entrées de la table **task**
	 * * Exemple2: `find('Task', ['owner' => 'claude', 'deadline' => '2019-12-04']
	 * , 'id', '5')`
	 *      retourne les 5 premières **tasks** de **claude** ayant comme deadline le
	 *      04/12/2019 ordonnées par leur id.
	 *
	 * @param        $table
	 * @param array  $args
	 * @param string $order
	 * @param string $max
	 *
	 * @return bool
	 */
	public function find($table, $args = [], $order = "", $max = "") {
		return $this->_read($table, $args, $order, $max) ? $this->_result : false;
	}


	/**
	 * Retourne le premier objet du resultat de la requète. Voir `find` pour des
	 * des exemples d'utilisation des requetes.
	 *
	 * * Usage: `first('table', [[args]])
	 *
	 * @return object|bool
	 */
	public function first($table, $args = []) {
		return $this->_read($table, $args) ? $this->_result[0] : false;
	}

	/*------------------------------*\
	|*				    Getters        		*|
	\*------------------------------*/

	/**
	 * Utilisable sur un objet de type `DB`. Retourne les résultats d'un query.
	 * @return object
	 */
	public function result() {
		return $this->_result;
	}


	/**
	 * Utilisable sur un objet de type `DB`. Retourne le status de la dernière
	 * requête.
	 *
	 * @return bool
	 */
	public function isError() {
		return $this->_error;
	}

	/**
	 * Utilisable sur un objet de type `DB`. Retourne l'id de la dernière
	 * insertion.
	 *
	 * @return null
	 */
	public function lastInsertID() {
		return $this->_lastInsertID;
	}

	/*------------------------------*\
	|*				    Static        		*|
	\*------------------------------*/

	/**
	 * Permet de retourner un objet du type `DB`.
	 * @return \DB
	 */
	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}

}
