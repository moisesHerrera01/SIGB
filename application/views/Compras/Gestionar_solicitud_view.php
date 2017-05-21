<?php
  $USER = $this->session->userdata('logged_in');

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form',
    'enctype'=>"multipart/form-data"
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Solicitud de Compras</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Compras/Gestionar_Solicitud/ActualizarDatos", $atributos);

      $sol = array(
          'name' => 'solicitante',
          'placeholder' => 'Ingrese solicitante',
          'class' => "form-control",
          'readonly'=>"readonly",
          'value'=> $solicitante,
      );

      $just = array(
          'name' => 'justificacion',
          'placeholder' => 'Ingrese la justificación',
          'class' => "form-control"
      );

      $val = array(
          'name' => 'valor',
          'placeholder' => 'Ingrese valor estimado',
          'class' => "form-control"
      );

      $com_c = array(
        'name' => 'comentario_compras',
        'class' => "form-control",
        "rows" => "4",
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Solicitante:', 'sol', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sol);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Justificacion:', 'just', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($just);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Valor:', 'val', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($val);
        echo "</div>";
      echo "</div>";


        echo "<div class='form-group'>";
          echo form_label('Comentario compras:', 'com', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_textarea($com_c);
          echo "</div>";
        echo "</div>";


      echo form_hidden('id');
      echo form_hidden('nivel');
      echo form_submit('','Guardar', $button);

      echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['autocomplete', 'justificacion','fecha_solicitud','valor',
      'solicitante','numero','especificaciones'],false,['comentario_compras'])\">Limpiar</a>";

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
    'url' => 'index.php/Compras/solicitud/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";


?>
