<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="<?= ROOT ?>static/css/style.css">
  <link rel="stylesheet" href="<?= ROOT ?>static/css/variables.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
  <script src="<?= ROOT ?>static/vendor/jquery-3.3.1.js"></script>
  <script src="<?= ROOT ?>static/js/main.js"></script>
  <title><?= $this->siteTitle() ?></title>

	<?= $this->section('head'); ?>

</head>

<body>

<div class="header">
  <h1><span class="manager">Tach</span>'Manager</h1>
  <p>Vos taches en toute simplicité.</p>
</div>

<div class="container">

  <?= $this->section('body'); ?>

</div>

</body>
</html>
