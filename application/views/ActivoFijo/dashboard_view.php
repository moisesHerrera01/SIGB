<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $title?></title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/main.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/menu.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/sweetalert.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/iconos.css")?> rel="stylesheet" media="screen">
    <script type="text/javascript">
      var baseurl = "<?= base_url(); ?>";
    </script>
  </head>
  <body>
    <div class="content">
      <?php
      if (isset($menu)) {
        echo $menu;
      }
      ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src=><\/script>')</script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
    <script src=<?= base_url("vendor/twbs/bootstrap/dist/js/bootstrap.min.js")?>></script>
    <script src=<?= base_url("assets/js/main.js")?>></script>
    <script src=<?= base_url("assets/js/sweetalert.min.js")?>></script>
    <script src=<?= base_url("assets/js/jQueryRotate.js")?>></script>
    <?php
      if (isset($js)) {
        echo "<script src=".base_url($js)." type=\"text/javascript\"></script>";
      }
    ?>
  </body>
</html>
