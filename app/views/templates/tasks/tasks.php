<?php $this->startSection('head'); ?>
<link rel="stylesheet" href="<?= ROOT ?>static/css/tasks.css">
<?php $this->endSection(); ?>



<?php $this->startSection('body'); ?>


<h1>
  Taches de <span class="username"><?= $this->username ?></span>
  <a class="logout" href="<?= ROOT ?>register/logout">déconnexion</a>
</h1>

<section class="section">
  <div class="content tasks">
		<?= $this->taskList ?>
  </div>

</section>
<section class="section">
  <a class="button" id="NewEntryButton" href="#">
    Ajouter
  </a>
</section>

<section class="section" id="NewEntryForm">
  <form class="form new-entry-form" action="<?= ROOT ?>tasks/add" method="post">
    <div>
      <label for="description">Description:</label>
      <div>
        <input type="text" name="description" value="" minlength="3" required>
      </div>
    </div>
    <div>
      <label for="deadline">Deadline:</label>
      <div>
      <input type="date" name="deadline" value="" required>
      </div>
    </div>
    <input class="button" type="submit" value="Ajouter">
  </form>
</section>


<?php $this->endSection(); ?>

