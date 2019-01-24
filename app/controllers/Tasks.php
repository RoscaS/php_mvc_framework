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

	public function form($description, $checked, $deadline, $id) {
		$completed = $checked ? 'checked' : 'value="0"';
		$html
				= '<form class="form" method="post" style="display: none;" action="/tasks/update">';
		$html .= '<input name="description" maxlength="255" class="form-description" type="text" value="' . $description . '">';
		$html .= '<input name="deadline" class="form-date" type="date" value="' . $deadline . '">';
		$html .= '<input name="completed" class="form-check" type="checkbox"' . $completed . '>';
		$html .= '<input name="id" type="hidden" value="' . $id . '">';
		$html .= '<div>';
		$html .= '<button class="form-submit" type="submit">';
		$html .= '<i class="fas fa-save" title="Enregistrer"></i></button>';
		$html .= '<a class="delete" title="Supprimer" href="'. ROOT . 'tasks/delete?pk='. $id .'">';
		$html .= '<i class="fas fa-trash-alt"></i></a>';
		$html .= '</div></form>';
		return $html;
	}

	public function taskTable() {
		$html = '';
		foreach ($this->tasks as $task) {
			$html .= '<div class="task' . ($task->completed ? " completed" : "") . '">';
			$html .= '<span class="description" title="'. $task->description .'">';
			$html .= $task->description . '</span>';
			$html .= '<span class="date">';
			$html .= date("j F Y", strtotime($task->deadline));
			$html .= '</span><a class="edit">';
			$html .= '<i class="fas fa-pen" title="Editer"></i></a>';
			$html .= $this->form(
					$task->description, $task->completed, $task->deadline, $task->id);
			$html .= '</div>';
		}
		return $html;
	}
}
