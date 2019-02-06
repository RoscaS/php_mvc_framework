<?php

class Tasks extends Controller {

	public $tasks;
	public $owner;
	public $ownerId;

	public function __construct($controller, $action) {
		parent::__construct($controller, $action);
		$this->load_model('Task');
		$this->owner = User::currentUser();
		$this->ownerId = $this->owner->id;
		$this->tasks = $this->model->userTasks($this->ownerId);
	}

	public function indexAction() {
		if (!User::currentUser()) {
			Router::redirect('');
		}

		$this->view->taskList = $this->taskTable();
		$this->view->username = ucfirst($this->owner->username);
		$this->view->render('tasks/tasks');
	}

	public function updateAction() {
		if ($_POST) {
			$this->model->update(
					$_POST['id'],
					[
							'deadline'    => Helpers::sanitize($_POST['deadline']),
							'description' => Helpers::sanitize($_POST['description']),
							'completed'   => key_exists('completed', $_POST),
					]
			);
		}
		Router::redirect('tasks');
	}

	public function deleteAction() {
		if ($_GET) {
			$this->model->delete($_GET['pk']);
		}
		Router::redirect('tasks');
	}

	public function addAction() {
		if ($_POST) {
			$this->model->insert([
					'description' => Helpers::sanitize($_POST['description']),
					'deadline'    => Helpers::sanitize($_POST['deadline']),
					'owner'       => $this->ownerId,
			]);
		}
		Router::redirect('tasks');
	}

	/*------------------------------*\
	|*				    HTML          		*|
	\*------------------------------*/

	public function form($task) {
		$completed = $task->completed ? 'checked' : 'value="0"';
		return Html::TasksUpdateForm($task, $completed);
	}

	public function taskTable() {
		$html = '';
		foreach ($this->tasks as $task) {
			$completed = $task->completed ? 'completed' : '';
			$date = date("j F Y", strtotime($task->deadline));
			$form = $this->form($task);
			$html .= Html::TasksTable($completed, $date, $form, $task);
		}
		return $html;
	}
}
