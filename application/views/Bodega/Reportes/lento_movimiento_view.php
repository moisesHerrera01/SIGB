<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>7-Reporte de productos con lento movimiento</font></h3>";
  echo "</div>";
echo "</div>";

$fun = array(
    'name' => 'autocomplete2',
    'placeholder' => 'Escribe Fuente de Fondos',
    'class' => "form-control autocomplete",
    'autocomplete' => 'off',
    'uri' => 'index.php/Bodega/Fuentefondos/Autocomplete',
    'name_op' => 'fuente',
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
    echo form_open("/Bodega/Productos/RecibirMovimiento", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Fuente de Fondos:', 'fun', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fun);
          echo form_hidden('fuente');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

        echo form_hidden('fuente');

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
