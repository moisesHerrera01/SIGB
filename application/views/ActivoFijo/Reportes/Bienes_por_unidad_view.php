<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>3- Reporte de bienes por sección</font></h3>";
  echo "</div>";
echo "</div>";

$secc = array(
    'name' => 'autocomplete',
    'placeholder' => 'INGRESE UNA SECCIÓN:',
    'class' => "form-control",
    'autocomplete' => 'off',
    'num_uri' => '5' //lugar en la url que se debe colocar el dato a pasar
);

$ofc = array(
  'name' => 'autocomplete3',
  'placeholder' => 'INGRESE UNA OFICINA:',
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
    echo form_open("/ActivoFijo/Reportes/Bienes_por_unidad/RecibirBienesUnidad", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Sección:', 'fun', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($secc);
          echo form_hidden('seccion');
          echo '<div id="suggestions1" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Oficina:', 'fun', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($ofc);
          echo form_hidden('oficina');
          echo '<div id="suggestions2" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
