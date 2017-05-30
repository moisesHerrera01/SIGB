<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Fuente de Fondos</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/FuenteFondos/RecibirDatos", $atributos);

      $nom = array(
          'name' => 'nombreFuente',
          'placeholder' => 'Escribe el Nombre',
          'class' => "form-control"
      );

      $cod = array(
          'name' => 'codigo',
          'placeholder' => 'Escribe el Código',
          'class' => "form-control"
      );

      $desc = array(
          'name' => 'descripcion',
          'placeholder' => 'Escribe una Descripción',
          'class' => "form-control",
          'type' => "text"
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Nombre:', 'nom', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($nom);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Código:', 'cod', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($cod);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Descripción:', 'desc', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($desc);
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');

      echo form_submit('','Guardar', $button);
      echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";

      echo form_close();
    echo "</div>";
  echo "</div>";

  $buscar = array(
    'name' => 'buscar',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/Bodega/FuenteFondos/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
