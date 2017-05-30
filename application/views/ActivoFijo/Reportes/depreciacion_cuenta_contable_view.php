<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>8- Reporte Depreciacion por Cuenta Contable</font></h3>";
  echo "</div>";
echo "</div>";

$cuenta = array(
    'name' => 'autocomplete7',
    'placeholder' => 'Ingrese cuenta contable',
    'class' => "form-control",
    'autocomplete' => 'off'
);

$fuente = array(
    'name' => 'autocomplete6',
    'placeholder' => 'Ingrese Fuente de Fondo',
    'class' => "form-control",
    'autocomplete' => 'off'
);

$fecha = array(
    'name' => 'fecha',
    'placeholder' => 'Escribe la fecha del ultimo calculo',
    'class' => "form-control",
    'type'=>'date'
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
    echo form_open("/ActivoFijo/Reportes/Depreciacion_cuenta_contable/RecibirDatos", $atributos);

    echo "<div class='form-group'>";
      echo form_label('Cuenta Contable:', 'cuen', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($cuenta);
        echo form_hidden('cuenta');
        echo '<div id="suggestions1" class="suggestions"></div>';
      echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
      echo form_label('Fuente de Fondo:', 'proy', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($fuente);
        echo form_hidden('fuente');
        echo '<div id="suggestions2" class="suggestions"></div>';
      echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
      echo form_label('Fecha Ultima de Calculo:', 'fecha_calculo', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($fecha);
      echo "</div>";
    echo "</div>";

  echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
