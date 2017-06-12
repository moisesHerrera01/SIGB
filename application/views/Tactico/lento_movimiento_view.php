<?php
$user = $this->session->userdata('logged_in');
echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

$fuente = array(
    'name' => 'autocomplete1',
    'placeholder' => 'Escribe Fuente de Fondos',
    'class' => "form-control",
    'autocomplete' => 'off'
);

$oe = array(
    'name' => 'autocomplete2',
    'placeholder' => 'Ingrese el Objeto especifico',
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
    echo "<div class='title-reporte'>";
    echo "<span class='icono icon-filter icon-title'> Filtro</span>";
  echo "</div>";
  echo "<div class='limit-content'>";
    echo form_open("/Tactico/lento_movimiento/RecibirMovimiento", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Fuente de Fondos:', 'f', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fuente);
          echo form_hidden('fuente');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
      echo form_label('Objeto Especifico:', 'oe', $atriLabel);
      echo "<div class='col-lg-10'>";
        echo form_input($oe);
        echo form_hidden('especifico');
        echo '<div id="suggestions1" class="suggestions"></div>';
      echo "</div>";
    echo "</div>";
  echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>

