<?php $this->startSection('head'); ?>
<link rel="stylesheet" href="<?= ROOT ?>static/css/login.css">
<?php $this->endSection(); ?>


<?php $this->startSection('body'); ?>

<div class="errors p-5">
	<?= $this->errorList ?>
</div>

<div class="col-md-4 offset-4 mt-5">
  <form class="form" action="<?= ROOT ?>register/login" method="post">
    <h1 class="title">Connexion</h1>
    <div>
      <label for="username">Utilisateur:</label>
      <input class="add-entry-input" type="text" name="username" id="username" value="" minlength="3" required>
    </div>
    <div>
      <label for="password">Mot de passe:</label>
      <input class="add-entry-input" type="password" name="password" id="password" value="" required>
    </div>
    <div>
      <input class="button connect-btn" type="submit" value="Connexion">
    </div>
  </form>
</div>



<?php $this->endSection(); ?>
