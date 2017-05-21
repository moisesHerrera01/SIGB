<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Sub categoria</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Subcategoria/RecibirDatos", $atributos);

  $nomC = array(
      'name' => 'nombre_categoria',
      'placeholder' => 'Escribe el nombre de la categoría',
      'value' => $nombre,
      'readonly'=>"readonly",
      'class' => "form-control"
  );

  $nomS = array(
      'name' => 'nombre_subcategoria',
      'placeholder' => 'Ingrese el nombre de la sub categoría',
      'class' => "form-control"
  );

  $num = array(
      'name' => 'numero_subcategoria',
      'type' => 'number',
      'value' => sprintf("%'03d", $count),
      'placeholder' => 'Ingrese el numero de la sub categoría',
      'readonly'=>"readonly",
      'class' => "form-control"
  );

  $des = array(
      'name' => 'descripcion',
      'placeholder' => 'Ingrese la descripción de la sub categoría',
      'class' => "form-control"
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Categoría:', 'nomC', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($nomC);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Nombre:', 'nomS', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($nomS);
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
  echo form_hidden('id_cat',$id_cat);
  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'id_categoria','nombre_subcategoria','descripcion'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Subcategoria/mostrarTabla/'.$id_cat
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
