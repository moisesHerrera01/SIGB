<?php
  $USER = $this->session->userdata('logged_in');

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Solicitud</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/solicitud_control/ActualizarDatos", $atributos);
      $dat= array(
          'name' => 'fecha_solicitud',
          'placeholder' => 'Escribe la fecha de ingreso',
          'class' => "form-control",
          'readonly'=>"readonly",
          'type'=>'date'
      );

      $sec= array(
          'name' => 'id_seccion',
          'placeholder' => 'Escribe la sección',
          'class' => "form-control",
          'readonly'=>"readonly",
      );

      $num = array(
          'name' => 'numero_solicitud',
          'placeholder' => 'Escribe el número de la Solicitud',
          'class' => "form-control",
          'readonly'=>"readonly",
      );

      $com = array(
        'name' => 'comentario_solicitud',
        'placeholder' => 'INGRESE COMENTARIOS A LA SOLICITUD',
        'class' => "form-control",
        "rows" => "5",
      );


      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Fecha:', 'dat', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($dat);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Sección:', 'sec', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sec);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Solicitud:', 'num', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($num);
        echo "</div>";
      echo "</div>";


      if ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO' || $USER['rol'] == 'ADMINISTRADOR SICBAF'
      || $USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA') {
        echo "<div class='form-group'>";
          echo form_label('Comentario:', 'com', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_textarea($com);
          echo "</div>";
        echo "</div>";
      }

      echo form_hidden('id');
      echo form_hidden('nivel');
      echo form_submit('','Guardar', $button);

      echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";

      echo form_close();
    echo "</div>";
  echo "</div>";

  $buscar = array(
    'name' => 'buscar',
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/Bodega/solicitud/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
