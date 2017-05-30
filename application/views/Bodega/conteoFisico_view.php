<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Conteo Fisico</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/ConteoFisico/RecibirDatos", $atributos);

      $nom = array(
          'name' => 'nombre',
          'placeholder' => 'Escribe Nombre',
          'class' => "form-control"
      );

      $fechaini = array(
          'name' => 'fecha_inicial',
          'type' => "date",
          'placeholder' => 'Escribe Fecha Inicial',
          'class' => "form-control"
      );

      $fechafin = array(
          'name' => 'fecha_final',
          'type' => "date",
          'placeholder' => 'Escribe Fecha Final',
          'class' => "form-control"
      );

      $des = array(
          'name' => 'descripcion',
          'placeholder' => 'Escribe descripcion',
          'class' => "form-control"
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
        echo form_label('Fecha Inicial:', 'inicial', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fechaini);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fecha Final:', 'Final', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fechafin);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Descripción:', 'abr', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($des);
        echo "</div>";
      echo "</div>";

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
    'url' => 'index.php/Bodega/ConteoFisico/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
