<?php

echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>Reporte de ordenes de compra.</font></h3>";
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

$tipo= array(
    'default'=>'SELECCIONE EL TIPO DE EMPRESA',
    'MICRO EMPRESA' => 'MICRO EMPRESA',
    'PEQUEÑA EMPRESA' => 'PEQUEÑA EMPRESA',
    'MEDIANA EMPRESA'=>'MEDIANA EMPRESA',
    'GRAN EMPRESA'=>'GRAN EMPRESA',
    'OTROS CONTRIBUYENTES'=>'OTROS CONTRIBUYENTES',
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
    echo form_open("/Compras/Reportes/Reporte_orden_compra/Recibirfechas", $atributos);

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
        echo form_label('Tipo de empresa:', 'tipo', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('tipo_empresa', $tipo, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
