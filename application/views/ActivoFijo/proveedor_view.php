<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Proveedor</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/proveedor/RecibirDatos", $atributos);

  $nom = array(
      'name' => 'nombre',
      'placeholder' => 'Ingrese el nombre del proveedor',
      'class' => "form-control"
  );

  $nit = array(
      'name' => 'nit',
      'placeholder' => 'Ingrese el numero de NIT',
      'class' => "form-control"
  );

  $email = array(
      'name' => 'correo',
      'placeholder' => 'Ingrese el correo electronico',
      'class' => "form-control"
  );

  $tel = array(
      'name' => 'telefono',
      'placeholder' => 'Ingrese el numero de telefono',
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
    echo form_label('NIT:', 'abr', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($nit);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Email:', 'ab', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($email);
    echo "</div>";
  echo "</div>";

    echo "<div class='form-group'>";
    echo form_label('Teléfono:', 'ab', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($tel);
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'nombre', 'nit','correo','telefono'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/proveedor/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
