<?php


class Html {

	static public function TasksUpdateForm($task, $completed) {
		return <<<HTML
				
		<form class="form" method="post" style="display: none;" action="/tasks/update">
		 <input name="description" maxlength="255" class="form-description" type="text" value="$task->description">
		 <input name="deadline" class="form-date" type="date" value="$task->deadline">
		 <input name="completed" class="form-check" type="checkbox" $completed>
		 <input name="id" type="hidden" value="$task->id">
		 <div>
			 <button class="form-submit" type="submit">
				 <i class="fas fa-save" title="Enregistrer"></i>
			 </button>
			 <a class="delete" title="Supprimer" href="'. ROOT . 'tasks/delete?pk=$task->id">
				 <i class="fas fa-trash-alt"></i>
			 </a>
		 </div>
		</form>
		
HTML;
	}

	static public function TasksTable($completed, $date, $form, $task) {
		return <<<HTML
							
		<div class="task $completed">
			<span class="description" title="$task->description">$task->description</span>
			<span class="date">$date </span>
			<a class="edit">
				<i class="fas fa-pen" title="Editer"></i>
			</a>
			$form
		</div>
		
HTML;
	}
}
