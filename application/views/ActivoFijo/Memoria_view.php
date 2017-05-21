<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Tipo de Memoria</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Memoria/RecibirDatos", $atributos);

  $tip = array(
      'name' => 'tipo_memoria',
      'placeholder' => 'Ingrese el tipo de memoria',
      'class' => "form-control"
  );


  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Tipo:', 'tip', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($tip);
    echo "</div>";
  echo "</div>";


  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'tipo_memoria'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Memoria/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
