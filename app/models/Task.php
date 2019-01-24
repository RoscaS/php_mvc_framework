<?php


class Task extends Model {

	public function __construct() {
		parent::__construct('task');
	}

	public function userTasks($id) {
		return $this->find(['owner' => $id]);
	}
}
