<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Cuenta Contable</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Cuenta_Contable/RecibirDatos", $atributos);

  $nom = array(
      'name' => 'nombre_cuenta',
      'placeholder' => 'Ingrese el nombre de la cuenta contable',
      'class' => "form-control"
  );

  $num = array(
      'name' => 'numero_cuenta',
      'type' => 'number',
      'placeholder' => 'Ingrese el numero de la cuenta contable',
      'class' => "form-control"
  );

  $por = array(
    'name' => 'porcentaje_depreciacion',
    'type' => 'number',
    'step' => '10',
    'min' => '0',
    'max' => '100',
    'placeholder' => 'Ingrese el porcentaje de depreciación',
    'class' => "form-control"
  );

  $vid = array(
      'name' => 'vida_util',
      'type' => 'number',
      'placeholder' => 'Ingrese la vida util en años',
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
    echo form_label('Numero:', 'num', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($num);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Porcentaje:', 'por', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($por);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Vida util:', 'vid', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($vid);
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'nombre_cuenta','numero_cuenta','porcentaje_depreciacion','vida_util'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Cuenta_Contable/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
