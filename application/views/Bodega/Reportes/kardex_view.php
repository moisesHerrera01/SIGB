<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>1-Reporte de generación de Kardex</font></h3>";
  echo "</div>";
echo "</div>";

$inicio= array(
    'name' => 'fecha_inicio',
    'placeholder' => 'Escribe la fecha de inicio',
    'class' => "form-control",
    'type'=>'date'
);

$fin= array(
    'name' => 'fecha_fin',
    'placeholder' => 'Escribe la fecha de Fin',
    'class' => "form-control",
    'type'=>'date'
);

$det = array(
    'name' => 'autocomplete1',
    'placeholder' => 'Escribe Producto',
    'class' => "form-control autocomplete",
    'autocomplete' => 'off',
    'uri' => 'index.php/Bodega/Productos/Autocomplete',
    'name_op' => 'producto',
    'siguiente' => 'button',
    'content' => 'suggestions'
);

$fuente = array(
    'name' => 'autocomplete1',
    'placeholder' => 'Escribe Fuente de Fondos',
    'class' => "form-control autocomplete",
    'autocomplete' => 'off',
    'uri' => 'index.php/Bodega/Fuentefondos/Autocomplete',
    'name_op' => 'fuente',
    'siguiente' => 'btn',
    'content' => 'suggestions1'
);

$button = array('class' => 'btn btn-success',);
$atriLabel = array('class' => 'col-lg-2 control-label');

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo "<div class='content-form'>";
  echo "<div class='limit-content-title'>";
    echo "<span class='icono icon-filter icon-title'> Filtro</span>";
  echo "</div>";
  echo "<div class='limit-content'>";
    echo form_open('Bodega/Kardex/RecibirProducto', $atributos);

    echo "<div class='form-group'>";
      echo form_label('Fecha Inicial:', 'inicio', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($inicio);
      echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
      echo form_label('Fecha Final:', 'fin', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($fin);
      echo "</div>";
    echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Productos:', 'pro', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($det);
          echo form_hidden('producto');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fuente de Fondos:', 'f', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fuente);
          echo form_hidden('fuente');
          echo '<div id="suggestions1" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
