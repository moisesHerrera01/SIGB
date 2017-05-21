<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>11- Reporte por Version de Sistema Operativo</font></h3>";
  echo "</div>";
echo "</div>";

$version = array(
    'name' => 'autocomplete',
    'placeholder' => 'VERSION DEL OS',
    'class' => "form-control autocomplete",
    'autocomplete' => 'off',
    'uri' => 'index.php/ActivoFijo/Sistema_operativo/Autocomplete',
    'name_op' => 'version',
    'siguiente' => 'fechaMin',
    'content' => 'suggestions'
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
    echo form_open("/ActivoFijo/Reportes/Reporte_sistema_operativo/RecibirDatos", $atributos);

    echo "<div class='form-group'>";
      echo form_label('Version OS:', 'fun', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($version);
        echo form_hidden('version');
        echo '<div id="suggestions" class="suggestions"></div>';
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
