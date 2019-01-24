<?php

class Validate {

	private $_passed = false;
	private $_errors = [];
	private $_db = NULL;

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	/*------------------------------------------------------------------*\
	|*					                Private Methods                         *|
	\*------------------------------------------------------------------*/

	/**
	 * @param $display
	 * @param $minMax
	 * @param $value
	 * @param $item
	 */
	private function _sizeError($display, $minMax, $value, $item) {
		$error = "Le champ \"{$display}\" est trop {$minMax}. ";
		$error .= $minMax === 'court' ? 'Minimum ' : 'Maximum ';
		$error .= "{$value} caractères !";
		$this->_addError([$error, $item]);
	}

	/*------------------------------------------------------------------*\
	|*					                Public Methods                          *|
	\*------------------------------------------------------------------*/

	/**
	 * @param       $src
	 * @param array $items
	 *
	 * @return \Validate
	 */
	public function check($src, $items = []) {
		$this->_errors = [];
		foreach ($items as $item => $rules) {
			$item    = Helpers::sanitize($item);
			$display = $rules['display'];

			foreach ($rules as $rule => $ruleValue) {
				$value = Helpers::sanitize(trim($src[$item]));

				if ($rule === 'required' && empty($value)) {
					$this->_addError(["Le champ \"{$display}\" est requis !", $item]);
				}

				elseif (!empty($value)) {

					switch ($rule) {

						case 'min':
							if (strlen($value) < $ruleValue) {
								$this->_sizeError($display, 'court', $ruleValue, $item);
							}
							break;

						case 'max':
							if (strlen($value) > $ruleValue) {
								$this->_sizeError($display, 'long', $ruleValue, $item);
							}
							break;

						case 'unique':
							$query = $this->_db->query(
									"SELECT {$item} FROM {$ruleValue} WHERE {$item} = ?", [$value]
							);
							if ($query->count()) {
								$this->_addError(["${display} existe déja !", $item]);
							}
							break;
					}
				}
			}
		}
		if (empty($this->_errors)) {
			$this->_passed = true;
		}
		return $this;
	}

	/**
	 * @param $error
	 */
	public function _addError($error) {
		$this->_errors[] = $error;
		$this->_passed   = empty($this->_errors);
	}

	/*------------------------------*\
	|*				    Getters        		*|
	\*------------------------------*/

	/**
	 * @return array
	 */
	public function errors() {
		return $this->_errors;
	}

	/**
	 * @return bool
	 */
	public function passed() {
		return $this->_passed;
	}

	/*------------------------------*\
	|*				    HTML          		*|
	\*------------------------------*/

	/**
	 * @return string
	 */
	public function errorList() {
		$html = '<ul class="error-list">';
		foreach ($this->_errors as $e) {
			$html .= '<li class="error">' . (is_array($e) ? $e[0] : $e) . '</li>';
		}
		return $html . '</ul>';
	}


}
