<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Versiones de sistemas operativos</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Sistema_operativo/RecibirDatos", $atributos);

  $ver = array(
      'name' => 'version_sistema_operativo',
      'placeholder' => 'Ingrese la versión de sistema operativo',
      'class' => "form-control"
  );


  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Versión:', 'ver', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ver);
    echo "</div>";
  echo "</div>";


  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'version_sistema_operativo'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Sistema_operativo/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
