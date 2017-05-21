<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title><?= $title?></title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/login.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/iconos.css")?> rel="stylesheet" media="screen">
  </head>
  <body>
    <?php
      if ($msg) {
        echo $msg;
      }
    ?>
    <div class="content">
      <div class="content-login-title">
        <span class="title-login"><span class="icono icon-home3"> SICBAF</span></span>
      </div>
      <div class="content-login">
      <?php
            $button = array('class' => 'btn btn-default btn-block');

            $username = array(
                'name' => 'username',
                'placeholder' => 'Ingrese su nombre de usuario',
                'class' => "form-control"
            );

            $password = array(
              'name' => 'password',
              'placeholder' => 'Ingrese su contraseña',
              'class' => 'form-control'
            );

            $radio = array(
              'name' => 'modulo',
              'value' => ''
            );

            $atributos = array(
              'class' => '',
              'role' => 'form'
            );

            echo form_open('Login/verificaLogin', $atributos);

              echo "<div class='form-group'>";
                echo form_label('Usuario:', 'nom');
                echo form_input($username);
              echo "</div>";

              echo "<div class='form-group'>";
                echo form_label('Contraseña:', 'pass');
                echo form_password($password);
              echo "</div>";
              echo "<br>";
              echo form_submit('','Ingresar', $button);

            echo form_close();
      ?>
      </div>
      <div class="content-login-footer">
        <img src="<?= base_url("assets/image/Logo_UES_blanco.png")?>" alt="" height="65px"/>
        <img id="escudo" src="<?= base_url("assets/image/escudo.png")?>" alt="" height="65px"/>
      </div>
    </div>
  </body>
  <!-- script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script -->
  <script>window.jQuery || document.write("<script src='<?= base_url('assets/js/jquery-1.11.3.min.js') ?>'>")</script>
  <script src=<?= base_url("vendor/twbs/bootstrap/dist/js/bootstrap.min.js")?>></script>
  <script type="text/javascript">
    $(".close").click(function(){
        $("#myAlert").slideUp();
    });
  </script>
</html>
