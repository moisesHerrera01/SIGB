<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>8-Reporte de comparación conteo físico</font></h3>";
  echo "</div>";
echo "</div>";

$conteo = array(
    'name' => 'conteo',
    'placeholder' => 'Escribe Conteo',
    'class' => "form-control autocomplete",
    'autocomplete' => 'off',
    'uri' => 'index.php/Bodega/ConteoFisico/Autocomplete',
    'name_op' => 'nada',
    'siguiente' => 'button',
    'content' => 'suggestions'
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
    echo form_open("/Bodega/conteoFisico/RecibirConteo", $atributos);
      echo "<div class='form-group'>";
        echo form_label('Conteo Fisico:', 'cont', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($conteo);
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";
      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
