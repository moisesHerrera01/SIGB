<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>10- Reporte Tipo Computadora</font></h3>";
  echo "</div>";
echo "</div>";

$tipo = array(
    'default' => 'TIPO COMPUTADORA',
    'LAPTOP' => 'LAPTOP',
    'DESKTOP' => 'DESKTOP',
    'SERVIDOR' => 'SERVIDOR'
);

$fechaInicial = array(
    'name' => 'fechaMin',
    'type' => "date",
    'placeholder' => 'Escribe Fecha de Inicial',
    'class' => "form-control"
);

$fechaFinal = array(
    'name' => 'fechaMax',
    'type' => "date",
    'placeholder' => 'Escribe Fecha de Final',
    'class' => "form-control"
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
    echo form_open("/ActivoFijo/Reportes/Reporte_tipo_computadora/RecibirDatos", $atributos);

    echo "<div class='form-group'>";
      echo form_label('Tipo:', 'tipo', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_dropdown('tipo_computadora', $tipo, 'default', array('class' => 'form-control'));
      echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
      echo form_label('Fecha inicial:', 'fechaini', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($fechaInicial);
      echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
      echo form_label('Fecha Final:', 'fechafin', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($fechaFinal);
      echo "</div>";
    echo "</div>";

  echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
