<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Almacen</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Almacenes/RecibirDatos", $atributos);

  $al = array(
      'name' => 'autocomplete1',
      'placeholder' => 'Ingrese el nombre del almacen',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Almacenes/AutocompleteAlmacen',
      'name_op' => 'almacen',
      'siguiente' => 'seccion',
      'content' => 'suggestions1'
  );

  $sec = array(
      'name' => 'autocomplete2',
      'placeholder' => 'Ingrese el nombre de la sección',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/almacenes/AutocompleteSeccion',
      'name_op' => 'seccion',
      'siguiente' => 'button',
      'content' => 'suggestions2'
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Almacen:', 'al', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($al);
      echo form_hidden('almacen');
      echo '<div id="suggestions1" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Sección:', 'sec', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($sec);
      echo form_hidden('seccion');
      echo '<div id="suggestions2" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','almacen','autocomplete1','seccion', 'autocomplete2'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Almacenes/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
