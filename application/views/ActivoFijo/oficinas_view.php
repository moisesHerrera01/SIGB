<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Oficina</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Oficinas/RecibirDatos", $atributos);

  $sa = array(
      'name' => 'autocomplete',
      'placeholder' => 'Seccion-Almacen',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Oficinas/Autocomplete',
      'name_op' => 'seccion_almacen',
      'siguiente' => 'nombre',
      'content' => 'suggestions'
  );

  $nom = array(
      'name' => 'nombre',
      'placeholder' => 'Ingrese el nombre de la oficina',
      'class' => "form-control"
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Sección-Almacen:', 'sa', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($sa);
      echo form_hidden('seccion_almacen');
      echo '<div id="suggestions" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Nombre:', 'nom', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($nom);
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','seccion_almacen','autocomplete', 'nombre'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Oficinas/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
