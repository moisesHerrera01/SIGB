<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Condición bien</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Condicion_bien/RecibirDatos", $atributos);

  $nom = array(
      'name' => 'nombre_condicion_bien',
      'placeholder' => 'Escribe el nombre de condición del bien',
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


  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'nombre_condicion_bien'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Condicion_bien/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
