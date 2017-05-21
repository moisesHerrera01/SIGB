<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Registro de Disco Duro</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Disco_Duro/RecibirDatos", $atributos);

  $nom = array(
      'name' => 'capacidad',
      'placeholder' => 'Ingrese la cantidad',
      'type' => 'number',
      'class' => "form-control"
  );

  $unidad= array(
      'default'=>'SELECCIONE UNIDAD',
      'KB' => 'KB',
      'MB' => 'MB',
      'GB' =>'GB',
      'TB' => 'TB'
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Capacidad:', 'nom', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($nom);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Unidad:', 'unidad', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_dropdown('unidad', $unidad, 'default', array('class' => 'form-control'));
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'capacidad'], ['unidad'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Disco_Duro/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
