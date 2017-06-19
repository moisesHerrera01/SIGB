<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Usuario</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/Usuarios_Rol/RecibirDatos", $atributos);

  $us = array(
      'name' => 'autocomplete1',
      'placeholder' => 'Ingrese el nombre del usuario',
      'class' => "form-control",
      'autocomplete' => 'off'
  );

  $rol = array(
      'name' => 'autocomplete2',
      'placeholder' => 'Ingrese el nombre del rol',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off'
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Usuario:', 'us', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($us);
      echo form_hidden('usuario');
      echo '<div id="suggestions1" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Rol:', 'rol', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($rol);
      echo form_hidden('rol');
      echo '<div id="suggestions2" class="suggestions"></div>';
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
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/Usuarios_Rol/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
