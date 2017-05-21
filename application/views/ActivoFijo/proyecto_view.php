<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Proyecto</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/proyecto/RecibirDatos", $atributos);

  $nom = array(
      'name' => 'nombre_proyecto',
      'placeholder' => 'Ingresa el nombre del proyecto',
      'class' => "form-control"
  );

  $abr = array(
      'name' => 'numero_proyecto',
      'placeholder' => 'Ingresa el número del proyecto',
      'class' => "form-control"
  );

  $ab = array(
      'name' => 'descripcion',
      'placeholder' => 'Ingresa la descripción',
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
    echo form_label('Numero:', 'abr', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($abr);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Descripción:', 'ab', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ab);
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'nombre_proyecto', 'numero_proyecto','descripcion'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/proyecto/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
