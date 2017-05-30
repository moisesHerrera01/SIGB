<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>5-Reporte de gasto global por sección</font></h3>";
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
    'placeholder' => 'Escribe la fecha de inicio',
    'class' => "form-control",
    'type'=>'date'
);

$sec= array(
    'name' => 'autocomplete',
    'placeholder' => 'Escribe la seccion o Unidad',
    'class' => "form-control",
    'autocomplete' => 'off'
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
    echo form_open("/Bodega/Detalle_solicitud_producto/RecibirGastos", $atributos);

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
      echo form_label('Sección:', 'sec', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($sec);
        echo form_hidden('seccion');
        echo '<div id="suggestions" class="suggestions"></div>';
      echo "</div>";
    echo "</div>";

  echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
