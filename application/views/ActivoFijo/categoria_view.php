<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Categoría</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Categoria/RecibirDatos", $atributos);

  $nom = array(
      'name' => 'nombre_categoria',
      'placeholder' => 'Ingrese el nombre de la categoria',
      'class' => "form-control"
  );

  $num = array(
      'name' => 'numero_categoria',
      'type' => 'number',
      'value' => sprintf("%'03d", $count),
      'placeholder' => 'Ingrese el numero de la categoría',
      'readonly'=>"readonly",
      'class' => "form-control"
  );

  $des = array(
      'name' => 'descripcion',
      'placeholder' => 'Ingrese la descripción de la categoría',
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
    echo form_label('Descripción:', 'des', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($des);
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'nombre_categoria','descripcion'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Categoria/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
