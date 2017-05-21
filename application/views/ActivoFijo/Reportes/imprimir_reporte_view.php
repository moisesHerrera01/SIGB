<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $title?></title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet">
    <link href=<?= base_url("assets/css/main.css")?> rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
  </head>
  <body onload="window.print()">
    <div>
      <?= $table?>
    </div>
  </body>
</html>
